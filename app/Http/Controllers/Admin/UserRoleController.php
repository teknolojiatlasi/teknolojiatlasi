<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserRoleController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax() || $request->expectsJson()) {
            return $this->datatable($request);
        }

        return view('admin.users.index');
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user->load('roles'),
            'roles' => Role::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', 'exists:roles,id'],
            'two_factor_required' => ['nullable', 'boolean'],
        ]);

        $roleIds = collect($validated['roles'] ?? [])->map(fn ($roleId) => (int) $roleId)->all();
        $roleNames = Role::query()
            ->whereIn('id', $roleIds)
            ->pluck('name')
            ->all();

        if ($user->is(Auth::user()) && $user->hasAnyRole(['superadmin', 'admin']) && ! array_intersect(['superadmin', 'admin'], $roleNames)) {
            throw ValidationException::withMessages([
                'roles' => 'Kendi yonetici rolunuzu kaldiramazsiniz.',
            ]);
        }

        $user->syncRoles($roleNames);
        $user->forceFill([
            'two_factor_required' => $request->boolean('two_factor_required'),
        ])->save();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Kullanici yetkileri guncellendi.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->is(Auth::user())) {
            return redirect()
                ->route('admin.users.index')
                ->withErrors(['user' => 'Kendi kullanici hesabinizi silemezsiniz.']);
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Kullanici silindi.');
    }

    protected function datatable(Request $request): JsonResponse
    {
        $draw = max(0, (int) $request->input('draw', 0));
        $start = max(0, (int) $request->input('start', 0));
        $length = (int) $request->input('length', 10);
        $length = $length > 0 ? min($length, 100) : 10;
        $search = trim((string) $request->input('search.value', ''));

        $baseQuery = User::query();
        $filteredQuery = User::query()
            ->with('roles')
            ->when($search !== '', function ($query) use ($search) {
                $like = '%' . $search . '%';

                $query->where(function ($query) use ($like) {
                    $query->where('name', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhereHas('roles', function ($query) use ($like) {
                            $query->where('name', 'like', $like);
                        });
                });
            });

        $recordsTotal = (clone $baseQuery)->count();
        $recordsFiltered = (clone $filteredQuery)->count();

        $columns = [
            0 => 'id',
            1 => 'name',
            2 => 'email',
        ];

        $orderColumnIndex = (int) $request->input('order.0.column', 0);
        $orderDirection = strtolower((string) $request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';

        $users = $filteredQuery
            ->orderBy($orderColumn, $orderDirection)
            ->skip($start)
            ->take($length)
            ->get();

        $data = $users->map(function (User $user): array {
            $deleteForm = '';

            if (! $user->is(Auth::user())) {
                $deleteForm = sprintf(
                    '<form method="POST" action="%s" class="d-inline" onsubmit="return confirm(\'Bu kullaniciyi silmek istediginize emin misiniz?\')">%s%s<button type="submit" class="btn btn-sm btn-danger">Sil</button></form>',
                    e(route('admin.users.destroy', $user)),
                    csrf_field(),
                    method_field('DELETE')
                );
            }

            return [
                'id' => e((string) $user->id),
                'name' => e($user->name),
                'email' => e($user->email),
                'roles' => e($user->roles->pluck('name')->implode(', ') ?: '-'),
                'two_factor' => (! $user->hasRole('superadmin') && $user->hasRole('admin'))
                    ? 'Aktif (admin zorunlu)'
                    : ($user->two_factor_required ? 'Aktif (secili)' : 'Pasif'),
                'actions' => sprintf(
                    '<a href="%s" class="btn btn-sm btn-primary">Duzenle</a> %s',
                    e(route('admin.users.edit', $user)),
                    $deleteForm
                ),
            ];
        })->all();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }
}
