<?php

namespace App\Services;

use App\Models\PushSubscription;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Blog\Models\Blog;

class WebPushService
{
    public function isConfigured(): bool
    {
        return (bool) config('services.webpush.enabled')
            && filled(config('services.webpush.vapid.public_key'))
            && filled(config('services.webpush.vapid.private_key'))
            && filled(config('services.webpush.vapid.subject'));
    }

    public function sendNewBlogNotification(Blog $blog): void
    {
        if (! $this->isConfigured()) {
            return;
        }

        if (! class_exists(\Minishlink\WebPush\WebPush::class) || ! class_exists(\Minishlink\WebPush\Subscription::class)) {
            Log::warning('Web push package is not installed; blog push notification skipped.', [
                'blog_id' => $blog->id,
            ]);

            return;
        }

        $subscriptions = PushSubscription::query()->get();

        if ($subscriptions->isEmpty()) {
            return;
        }

        $auth = [
            'VAPID' => [
                'subject' => (string) config('services.webpush.vapid.subject'),
                'publicKey' => (string) config('services.webpush.vapid.public_key'),
                'privateKey' => (string) config('services.webpush.vapid.private_key'),
            ],
        ];

        $payload = json_encode([
            'title' => 'Yeni blog yazisi yayinda',
            'body' => Str::limit(trim(strip_tags((string) $blog->content)), 140) ?: $blog->title,
            'icon' => asset('images/icons/icon-192x192.png'),
            'badge' => asset('images/icons/icon-96x96.png'),
            'image' => $blog->cover_image ? asset('storage/' . ltrim($blog->cover_image, '/')) : null,
            'tag' => 'blog-' . $blog->id,
            'data' => [
                'url' => route('blog.public.show', $blog),
                'blogId' => $blog->id,
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        if ($payload === false) {
            return;
        }

        $webPush = new \Minishlink\WebPush\WebPush($auth);

        foreach ($subscriptions as $subscription) {
            $webPush->queueNotification(
                \Minishlink\WebPush\Subscription::create([
                    'endpoint' => $subscription->endpoint,
                    'publicKey' => $subscription->public_key,
                    'authToken' => $subscription->auth_token,
                    'contentEncoding' => $subscription->content_encoding ?: 'aesgcm',
                ]),
                $payload,
            );
        }

        foreach ($webPush->flush() as $report) {
            if ($report->isSuccess()) {
                continue;
            }

            $endpoint = $report->getRequest()?->getUri()?->__toString();

            if ($endpoint) {
                PushSubscription::query()->where('endpoint', $endpoint)->delete();
            }

            Log::warning('Web push delivery failed.', [
                'endpoint' => $endpoint,
                'reason' => $report->getReason(),
            ]);
        }
    }
}
