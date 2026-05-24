<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use Illuminate\Http\Response;
use Modules\Blog\Models\Blog;
use Modules\Contact\Models\ContactSetting;
use Modules\Sinav\Models\Lesson;
use Modules\Sinav\Models\Test;
use Modules\Sossial\Models\Post;
use Modules\Sossial\Models\Tag;
use Modules\Survey\Models\Survey;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = collect([
            $this->entry(route('anasayfa'), Blog::query()->max('updated_at'), 'daily', '1.0'),
            $this->entry(route('blog.public.index'), Blog::query()->max('updated_at'), 'daily', '0.9'),
            $this->entry(route('sinav.lessons.index'), Test::query()->max('updated_at'), 'daily', '0.9'),
            $this->entry(route('survey.public.index'), Survey::query()->max('updated_at'), 'daily', '0.8'),
            $this->entry(route('contact_public_index'), ContactSetting::query()->max('updated_at'), 'monthly', '0.6'),
            $this->entry(route('sosial.feed'), Post::query()->max('updated_at'), 'hourly', '0.7'),
            $this->entry(route('sosial.explore'), Tag::query()->max('updated_at'), 'daily', '0.6'),
        ])
            ->merge(
                Blog::query()
                    ->where('status', true)
                    ->latest('updated_at')
                    ->get()
                    ->map(fn (Blog $blog) => $this->entry(route('blog.public.show', $blog), $blog->updated_at, 'weekly', '0.8'))
            )
            ->merge(
                Lesson::query()
                    ->where('is_active', true)
                    ->get()
                    ->map(fn (Lesson $lesson) => $this->entry(route('sinav.lessons.show', $lesson), $lesson->updated_at, 'weekly', '0.7'))
            )
            ->merge(
                Test::query()
                    ->where('is_active', true)
                    ->get()
                    ->map(fn (Test $test) => $this->entry(route('sinav.tests.show', $test), $test->updated_at, 'weekly', '0.7'))
            )
            ->merge(
                Survey::query()
                    ->where('is_public', true)
                    ->where('is_active', true)
                    ->get()
                    ->flatMap(fn (Survey $survey) => [
                        $this->entry(route('survey.public.show', $survey->slug), $survey->updated_at, 'weekly', '0.6'),
                        $this->entry(route('survey.public.results', $survey->slug), $survey->updated_at, 'weekly', '0.5'),
                    ])
            )
            ->merge(
                Tag::query()
                    ->has('posts')
                    ->get()
                    ->map(fn (Tag $tag) => $this->entry(route('sosial.tags.show', $tag), $tag->updated_at, 'daily', '0.5'))
            )
            ->merge(
                Post::query()
                    ->latest('updated_at')
                    ->get()
                    ->map(fn (Post $post) => $this->entry(route('sosial.posts.show', $post), $post->updated_at, 'daily', '0.5'))
            )
            ->filter()
            ->values();

        return response()
            ->view('sitemap.xml', ['urls' => $urls])
            ->header('Content-Type', 'application/xml; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    private function entry(string $loc, mixed $lastmod = null, string $changefreq = 'weekly', string $priority = '0.5'): array
    {
        $lastmod = $this->normalizeLastmod($lastmod);

        return [
            'loc' => $loc,
            'lastmod' => $lastmod?->toAtomString(),
            'changefreq' => $changefreq,
            'priority' => $priority,
        ];
    }

    private function normalizeLastmod(mixed $lastmod): ?Carbon
    {
        if ($lastmod instanceof Carbon) {
            return $lastmod;
        }

        if ($lastmod === null || $lastmod === '') {
            return null;
        }

        try {
            return Carbon::parse($lastmod);
        } catch (\Throwable) {
            return null;
        }
    }
}
