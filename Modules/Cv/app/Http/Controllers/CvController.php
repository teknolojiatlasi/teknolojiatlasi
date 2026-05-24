<?php

namespace Modules\Cv\Http\Controllers;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Cv\Models\Cv;
use RuntimeException;

class CvController extends Controller
{
    protected const GUEST_CV_SESSION_KEY = 'cv_guest_access';

    public function create()
    {
        return view('cv::admin.create');
    }

    public function store(Request $request)
    {
        try {
            $data = $this->validatePayload($request);
            $data['user_id'] = $request->user()?->id;

            $cv = $this->persistCv(new Cv(), $data, $request);
            $this->registerGuestAccess($request, $cv);

            return redirect()
                ->route('cv.edit', $cv)
                ->with('success', 'CV başarıyla oluşturuldu.');
        } catch (RuntimeException $exception) {
            return back()
                ->withInput()
                ->withErrors(['photo' => $exception->getMessage()]);
        }
    }

    public function edit(Request $request, Cv $cv)
    {
        $this->authorizeCvAccess($request, $cv);
        $cv->load(['educations', 'experiences', 'skills']);

        return view('cv::admin.edit', compact('cv'));
    }

    public function update(Request $request, Cv $cv)
    {
        try {
            $this->authorizeCvAccess($request, $cv);
            $data = $this->validatePayload($request, true);

            $this->persistCv($cv, $data, $request);
            $this->registerGuestAccess($request, $cv);

            return redirect()
                ->route('cv.edit', $cv)
                ->with('success', 'CV güncellendi.');
        } catch (RuntimeException $exception) {
            return back()
                ->withInput()
                ->withErrors(['photo' => $exception->getMessage()]);
        }
    }

    public function show(Request $request, Cv $cv)
    {
        $this->authorizeCvAccess($request, $cv);
        $cv->load(['educations', 'experiences', 'skills']);

        return view('cv::templates.' . $cv->template, compact('cv'));
    }

    public function pdf(Request $request, Cv $cv)
    {
        $this->authorizeCvAccess($request, $cv);
        $cv->load(['educations', 'experiences', 'skills']);

        $pdf = Pdf::loadView('cv::pdf.' . $cv->template, compact('cv'));

        return $pdf->download(Str::slug($cv->full_name) . '-cv.pdf');
    }

    public function destroy(Request $request, Cv $cv)
    {
        $this->authorizeCvAccess($request, $cv);

        DB::transaction(function () use ($cv) {
            if ($cv->photo) {
                Storage::disk('public')->delete($cv->photo);
            }

            $cv->educations()->delete();
            $cv->experiences()->delete();
            $cv->skills()->delete();
            $cv->delete();
        });

        $this->forgetGuestAccess($request, $cv);

        return redirect()
            ->route('cv.create')
            ->with('success', 'CV silindi.');
    }

    protected function validatePayload(Request $request, bool $isUpdate = false): array
    {
        return $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'about' => ['nullable', 'string'],
            'photo' => [$isUpdate ? 'nullable' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'template' => ['required', 'in:modern,classic,blue'],
            'educations' => ['nullable', 'array'],
            'educations.*.school' => ['nullable', 'string', 'max:255'],
            'educations.*.degree' => ['nullable', 'string', 'max:255'],
            'educations.*.year' => ['nullable', 'string', 'max:50'],
            'educations.*.description' => ['nullable', 'string'],
            'experiences' => ['nullable', 'array'],
            'experiences.*.company' => ['nullable', 'string', 'max:255'],
            'experiences.*.position' => ['nullable', 'string', 'max:255'],
            'experiences.*.start_date' => ['nullable', 'string', 'max:50'],
            'experiences.*.end_date' => ['nullable', 'string', 'max:50'],
            'experiences.*.description' => ['nullable', 'string'],
            'skills' => ['nullable', 'array'],
            'skills.*.name' => ['nullable', 'string', 'max:255'],
            'skills.*.level' => ['nullable', 'integer', 'min:1', 'max:4'],
        ]);
    }

    protected function persistCv(Cv $cv, array $data, Request $request): Cv
    {
        return DB::transaction(function () use ($cv, $data, $request) {
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');

                if (! $file->isValid()) {
                    throw new RuntimeException('Profil fotoğrafı geçerli bir dosya olarak yüklenemedi.');
                }

                Storage::disk('public')->makeDirectory('cv/photos');

                if ($cv->exists && $cv->photo) {
                    Storage::disk('public')->delete($cv->photo);
                }

                $data['photo'] = $file->store('cv/photos', 'public');

                if (! $data['photo']) {
                    throw new RuntimeException('Profil fotoğrafı sunucuya kaydedilemedi.');
                }
            } else {
                unset($data['photo']);
            }

            $cv->fill([
                'user_id' => $data['user_id'] ?? $cv->user_id,
                'full_name' => $data['full_name'],
                'title' => $data['title'] ?? null,
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'about' => $data['about'] ?? null,
                'template' => $data['template'],
                ...array_key_exists('photo', $data) ? ['photo' => $data['photo']] : [],
            ]);
            $cv->save();

            $cv->educations()->delete();
            foreach ($this->normalizedEducations($data['educations'] ?? []) as $index => $education) {
                $cv->educations()->create($education + ['order' => $index]);
            }

            $cv->experiences()->delete();
            foreach ($this->normalizedExperiences($data['experiences'] ?? []) as $index => $experience) {
                $cv->experiences()->create($experience + ['sort_order' => $index]);
            }

            $cv->skills()->delete();
            foreach ($this->normalizedSkills($data['skills'] ?? []) as $index => $skill) {
                $cv->skills()->create($skill + ['sort_order' => $index]);
            }

            return $cv;
        });
    }

    protected function authorizeCvAccess(Request $request, Cv $cv): void
    {
        $user = $request->user();

        if ($user?->hasAnyRole(['superadmin', 'admin'])) {
            return;
        }

        if ($user && $cv->user_id !== null && $cv->user_id === $user->id) {
            return;
        }

        if ($cv->user_id === null && $this->hasGuestAccess($request, $cv)) {
            return;
        }

        abort(403);
    }

    protected function registerGuestAccess(Request $request, Cv $cv): void
    {
        if ($cv->user_id !== null) {
            return;
        }

        $allowedIds = collect($request->session()->get(self::GUEST_CV_SESSION_KEY, []))
            ->push($cv->getKey())
            ->unique()
            ->values()
            ->all();

        $request->session()->put(self::GUEST_CV_SESSION_KEY, $allowedIds);
    }

    protected function hasGuestAccess(Request $request, Cv $cv): bool
    {
        return in_array($cv->getKey(), $request->session()->get(self::GUEST_CV_SESSION_KEY, []), true);
    }

    protected function forgetGuestAccess(Request $request, Cv $cv): void
    {
        $allowedIds = collect($request->session()->get(self::GUEST_CV_SESSION_KEY, []))
            ->reject(fn ($id) => (int) $id === (int) $cv->getKey())
            ->values()
            ->all();

        $request->session()->put(self::GUEST_CV_SESSION_KEY, $allowedIds);
    }

    protected function normalizedEducations(array $items): array
    {
        return collect($items)
            ->map(fn ($item) => [
                'school' => trim((string) ($item['school'] ?? '')),
                'degree' => trim((string) ($item['degree'] ?? '')),
                'year' => trim((string) ($item['year'] ?? '')),
                'description' => trim((string) ($item['description'] ?? '')) ?: null,
            ])
            ->filter(fn ($item) => $item['school'] !== '' || $item['degree'] !== '' || $item['year'] !== '' || $item['description'] !== null)
            ->values()
            ->all();
    }

    protected function normalizedExperiences(array $items): array
    {
        return collect($items)
            ->map(fn ($item) => [
                'company' => trim((string) ($item['company'] ?? '')),
                'position' => trim((string) ($item['position'] ?? '')),
                'start_date' => trim((string) ($item['start_date'] ?? '')),
                'end_date' => trim((string) ($item['end_date'] ?? '')) ?: null,
                'description' => trim((string) ($item['description'] ?? '')) ?: null,
            ])
            ->filter(fn ($item) => $item['company'] !== '' || $item['position'] !== '' || $item['start_date'] !== '' || $item['end_date'] !== null || $item['description'] !== null)
            ->values()
            ->all();
    }

    protected function normalizedSkills(array $items): array
    {
        return collect($items)
            ->map(fn ($item) => [
                'name' => trim((string) ($item['name'] ?? '')),
                'level' => isset($item['level']) && $item['level'] !== '' ? (int) $item['level'] : 3,
            ])
            ->filter(fn ($item) => $item['name'] !== '')
            ->values()
            ->all();
    }
}
