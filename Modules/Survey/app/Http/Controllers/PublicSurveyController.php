<?php

namespace Modules\Survey\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Modules\Survey\Models\Survey;
use Modules\Survey\Services\SurveyResultService;

class PublicSurveyController extends Controller
{
    public function __construct(private readonly SurveyResultService $results)
    {
    }

    public function index()
    {
        $now = now();

        $activeSurvey = Cache::remember('public.surveys.active.v1', now()->addMinutes(3), function () use ($now) {
            return Survey::with(['questions.options', 'responses'])
                ->where('is_public', true)
                ->where('is_active', true)
                ->where(function ($query) use ($now) {
                    $query->whereNull('opens_at')->orWhere('opens_at', '<=', $now);
                })
                ->where(function ($query) use ($now) {
                    $query->whereNull('closes_at')->orWhere('closes_at', '>=', $now);
                })
                ->latest()
                ->first();
        });

        $surveys = Cache::remember('public.surveys.index.v1', now()->addMinutes(5), function () {
            return Survey::query()
                ->withCount('responses')
                ->where('is_public', true)
                ->latest()
                ->get();
        });

        $activeStats = collect();
        $activeResponseCount = 0;

        if ($activeSurvey) {
            $payload = $this->results->build($activeSurvey);
            $activeStats = $payload['questionStats'];
            $activeResponseCount = $payload['responseCount'];
        }

        return view('survey::public-index', compact(
            'activeSurvey',
            'activeStats',
            'activeResponseCount',
            'surveys',
        ));
    }

    public function show(Survey $survey)
    {
        abort_unless($survey->is_open && $survey->is_public, 404);

        $survey = Cache::remember("public.surveys.show.{$survey->id}.v1", now()->addMinutes(5), function () use ($survey) {
            $survey->load('questions.options')->loadCount('responses');

            return $survey;
        });

        return view('survey::public', [
            'survey' => $survey,
            'shareUrl' => route('survey.public.results', $survey),
        ]);
    }

    public function results(Survey $survey)
    {
        abort_unless($survey->is_public, 404);

        $payload = Cache::remember("public.surveys.results.{$survey->id}.v1", now()->addMinutes(5), function () use ($survey) {
            $survey->load(['questions.options', 'responses']);

            return $this->results->build($survey);
        });

        return view('survey::results', $payload + ['publicView' => true]);
    }
}
