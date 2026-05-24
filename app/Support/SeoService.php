<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Modules\Blog\Models\Blog;
use Modules\Contact\Models\ContactSetting;
use Modules\Sinav\Models\Lesson;
use Modules\Sinav\Models\Test;
use Modules\Sossial\Models\Post;
use Modules\Sossial\Models\Tag;
use Modules\Survey\Models\Survey;

class SeoService
{
    public function forCurrentRequest(?Request $request = null): array
    {
        $request ??= request();

        $siteName = (string) config('seo.site_name', config('app.name'));
        $defaultTitle = (string) config('seo.default_title', $siteName);
        $defaultDescription = (string) config('seo.default_description', $siteName);
        $defaultImage = $this->absoluteAsset((string) config('seo.default_image', ''));

        $data = [
            'site_name' => $siteName,
            'title' => $defaultTitle,
            'description' => $defaultDescription,
            'canonical' => $this->canonicalUrl($request),
            'robots' => $this->robotsFor($request),
            'type' => 'website',
            'image' => $defaultImage,
            'published_time' => null,
            'modified_time' => null,
            'schema' => $this->baseSchemas($siteName, $defaultDescription, $defaultImage),
        ];

        $routeName = $request->route()?->getName();

        switch ($routeName) {
            case 'anasayfa':
                $data['title'] = $siteName . ' | Güncel içerikler, iş ilanları, sınavlar ve anketler';
                $data['description'] = 'İş ilanları, blog yazıları, sınav içerikleri, anketler ve sosyal paylaşımlar tek merkezde. Hızlı açılan, arama motoru dostu içerik hub.';
                $data['schema'][] = $this->webPageSchema($data['canonical'], 'WebPage', $data['title'], $data['description']);
                break;

            case 'blog.public.index':
                $category = $request->query('category');
                $prefix = $category ? 'Kategori arşivi' : 'Blog ve iş ilanı arşivi';
                $data['title'] = $siteName . ' | ' . $prefix;
                $data['description'] = 'Yayınlanan blog yazılarını ve iş ilanlarını kategori filtreleriyle keşfedin. SEO uyumlu arşiv sayfası ile içeriklere hızlı erişin.';
                $data['type'] = 'website';
                $data['schema'][] = $this->webPageSchema($data['canonical'], 'CollectionPage', $data['title'], $data['description']);
                break;

            case 'blog.public.show':
                /** @var Blog|null $blog */
                $blog = $request->route('blog');
                if ($blog instanceof Blog) {
                    $data['title'] = $blog->title . ' | ' . $siteName;
                    $data['description'] = $this->excerpt($blog->content, 160) ?: $defaultDescription;
                    $data['type'] = 'article';
                    $data['image'] = $this->blogImage($blog) ?: $defaultImage;
                    $data['published_time'] = optional($blog->created_at)->toIso8601String();
                    $data['modified_time'] = optional($blog->updated_at)->toIso8601String();
                    $data['schema'][] = $this->articleSchema($blog, $data['canonical'], $siteName, $data['description'], $data['image']);
                }
                break;

            case 'sinav.lessons.index':
                $data['title'] = $siteName . ' | Sınav dersleri ve test arşivi';
                $data['description'] = 'Ders bazlı sınav konularını ve aktif testleri tek ekranda inceleyin. Hızlı gezinme ve temiz bilgi mimarisi ile sınav arşivi.';
                $data['schema'][] = $this->webPageSchema($data['canonical'], 'CollectionPage', $data['title'], $data['description']);
                break;

            case 'sinav.lessons.show':
                /** @var Lesson|null $lesson */
                $lesson = $request->route('lesson');
                if ($lesson instanceof Lesson) {
                    $data['title'] = $lesson->name . ' dersi testleri | ' . $siteName;
                    $data['description'] = $this->excerpt($lesson->description, 160)
                        ?: $lesson->name . ' dersi için aktif konu başlıkları ve çözülebilir testleri inceleyin.';
                    $data['schema'][] = $this->webPageSchema($data['canonical'], 'CollectionPage', $data['title'], $data['description']);
                }
                break;

            case 'sinav.tests.show':
                /** @var Test|null $test */
                $test = $request->route('test');
                if ($test instanceof Test) {
                    $data['title'] = $test->title . ' | Online test';
                    $data['description'] = trim($test->title . ' testi. ' . ($test->duration_minutes ? $test->duration_minutes . ' dakikalık süreyle hemen çözmeye başlayın.' : 'Online çözüm sayfası.'));
                    $data['type'] = 'article';
                    $data['schema'][] = $this->webPageSchema($data['canonical'], 'Quiz', $data['title'], $data['description']);
                }
                break;

            case 'survey.public.index':
                $data['title'] = $siteName . ' | Güncel anketler';
                $data['description'] = 'Aktif ve geçmiş anketleri tek sayfada inceleyin, açık anketlere katılın ve sonuçları karşılaştırın.';
                $data['schema'][] = $this->webPageSchema($data['canonical'], 'CollectionPage', $data['title'], $data['description']);
                break;

            case 'survey.public.show':
            case 'survey.public.results':
                /** @var Survey|null $survey */
                $survey = $request->route('survey');
                if ($survey instanceof Survey) {
                    $suffix = $routeName === 'survey.public.results' ? 'sonuçları' : 'anketi';
                    $data['title'] = $survey->title . ' | ' . $suffix;
                    $data['description'] = $this->excerpt($survey->description, 160)
                        ?: $survey->title . ' anketine katılın veya sonuçlarını inceleyin.';
                    $data['schema'][] = $this->webPageSchema($data['canonical'], 'WebPage', $data['title'], $data['description']);
                }
                break;

            case 'contact_public_index':
                $data['title'] = $siteName . ' | İletişim';
                $data['description'] = 'Adres, telefon, e-posta ve iletişim formu üzerinden bize hızlıca ulaşın.';
                $data['schema'][] = $this->contactPageSchema($data['canonical'], $siteName, $data['description']);
                break;

            case 'sosial.feed':
                $data['title'] = $siteName . ' | Sosial akış';
                $data['description'] = 'Topluluk paylaşımlarını, bağlantılı blog içeriklerini ve etkileşimli yorum akışını keşfedin.';
                $data['schema'][] = $this->webPageSchema($data['canonical'], 'CollectionPage', $data['title'], $data['description']);
                break;

            case 'sosial.explore':
                $data['title'] = $siteName . ' | Sosial keşfet';
                $data['description'] = 'Etiketler ve öne çıkan içerikler üzerinden sosyal içerik arşivini keşfedin.';
                $data['schema'][] = $this->webPageSchema($data['canonical'], 'CollectionPage', $data['title'], $data['description']);
                break;

            case 'sosial.tags.show':
                /** @var Tag|null $tag */
                $tag = $request->route('tag');
                if ($tag instanceof Tag) {
                    $data['title'] = '#' . $tag->name . ' | Sosial etiketi';
                    $data['description'] = '#' . $tag->name . ' etiketiyle paylaşılan içerikleri inceleyin.';
                    $data['schema'][] = $this->webPageSchema($data['canonical'], 'CollectionPage', $data['title'], $data['description']);
                }
                break;

            case 'sosial.posts.show':
                /** @var Post|null $post */
                $post = $request->route('post');
                if ($post instanceof Post) {
                    $post->loadMissing(['user', 'blog']);
                    $data['title'] = ($post->blog?->title ?: $this->excerpt($post->body, 70) ?: 'Sosial paylaşım') . ' | ' . $siteName;
                    $data['description'] = $this->excerpt($post->body, 160)
                        ?: ($post->blog?->title ? $post->blog->title . ' bağlantılı paylaşım.' : 'Sosial paylaşım detay sayfası.');
                    $data['schema'][] = $this->socialPostSchema($post, $data['canonical'], $data['description']);
                }
                break;

            case 'sosial.profile.show':
                /** @var User|null $user */
                $user = $request->route('user');
                if ($user instanceof User) {
                    $data['title'] = $user->name . ' | Profil';
                    $data['description'] = $user->name . ' profil sayfası ve sosyal paylaşımları.';
                    $data['image'] = $user->avatarUrl();
                    $data['schema'][] = $this->profileSchema($user, $data['canonical'], $data['description']);
                }
                break;
        }

        $data['description'] = $this->plainText($data['description']);

        return $data;
    }

    private function robotsFor(Request $request): string
    {
        $routeName = (string) $request->route()?->getName();

        if (
            $routeName !== ''
            && (
                Str::contains($routeName, 'admin')
                || Str::startsWith($routeName, ['page.', 'game.', 'exam.', 'media.'])
                || (Str::startsWith($routeName, 'blog.') && ! Str::startsWith($routeName, 'blog.public.'))
                || (Str::startsWith($routeName, 'survey.') && ! Str::startsWith($routeName, 'survey.public.'))
            )
        ) {
            return 'noindex, nofollow';
        }

        if ($request->routeIs(
            'login',
            'register',
            'password.*',
            'verification.*',
            'dashboard',
            'profile.*',
            'sosial.my',
            'sosial.following',
            'sosial.messages.*',
            'sosial.posts.create',
            'sosial.posts.edit'
        )) {
            return 'noindex, nofollow';
        }

        return 'index, follow';
    }

    private function canonicalUrl(Request $request): string
    {
        $params = collect($request->query())
            ->reject(function ($value, string $key): bool {
                return Str::startsWith($key, 'utm_')
                    || in_array($key, ['fbclid', 'gclid', 'msclkid'], true);
            })
            ->all();

        $query = Arr::query($params);

        return $query !== '' ? $request->url() . '?' . $query : $request->url();
    }

    private function baseSchemas(string $siteName, string $description, ?string $defaultImage): array
    {
        $homeUrl = Route::has('anasayfa') ? route('anasayfa') : url('/');

        return [
            [
                '@context' => 'https://schema.org',
                '@type' => 'Organization',
                'name' => $siteName,
                'url' => $homeUrl,
                'logo' => $defaultImage,
            ],
            [
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                'name' => $siteName,
                'url' => $homeUrl,
                'description' => $description,
                'inLanguage' => 'tr-TR',
            ],
        ];
    }

    private function webPageSchema(string $url, string $type, string $title, string $description): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => $type,
            'url' => $url,
            'name' => $title,
            'description' => $description,
            'inLanguage' => 'tr-TR',
        ];
    }

    private function articleSchema(Blog $blog, string $url, string $siteName, string $description, ?string $image): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'mainEntityOfPage' => $url,
            'headline' => $blog->title,
            'description' => $description,
            'image' => $image ? [$image] : [],
            'datePublished' => optional($blog->created_at)->toIso8601String(),
            'dateModified' => optional($blog->updated_at)->toIso8601String(),
            'author' => [
                '@type' => 'Organization',
                'name' => $siteName,
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => $siteName,
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => $this->absoluteAsset((string) config('seo.default_image', '')),
                ],
            ],
            'articleSection' => $blog->category?->name,
        ];
    }

    private function contactPageSchema(string $url, string $siteName, string $description): array
    {
        $settings = class_exists(ContactSetting::class) ? ContactSetting::query()->first() : null;

        return [
            '@context' => 'https://schema.org',
            '@type' => 'ContactPage',
            'url' => $url,
            'name' => $siteName . ' iletişim',
            'description' => $description,
            'mainEntity' => [
                '@type' => 'Organization',
                'name' => $settings?->contact_company_name ?: $siteName,
                'email' => $settings?->contact_email,
                'telephone' => $settings?->contact_phone,
                'address' => $settings?->displayAddress(),
            ],
        ];
    }

    private function socialPostSchema(Post $post, string $url, string $description): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'SocialMediaPosting',
            'url' => $url,
            'headline' => $post->blog?->title ?: 'Sosial paylaşım',
            'articleBody' => $description,
            'datePublished' => optional($post->created_at)->toIso8601String(),
            'dateModified' => optional($post->updated_at)->toIso8601String(),
            'author' => [
                '@type' => 'Person',
                'name' => $post->user?->name,
            ],
        ];
    }

    private function profileSchema(User $user, string $url, string $description): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'ProfilePage',
            'url' => $url,
            'name' => $user->name,
            'description' => $description,
            'mainEntity' => [
                '@type' => 'Person',
                'name' => $user->name,
                'image' => $user->avatarUrl(),
            ],
        ];
    }

    private function blogImage(Blog $blog): ?string
    {
        if (! $blog->cover_image) {
            return null;
        }

        return route('blog.media.show', ['path' => ltrim($blog->cover_image, '/')]);
    }

    private function absoluteAsset(string $path): ?string
    {
        $path = trim($path);

        if ($path === '') {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', 'data:'])) {
            return $path;
        }

        return asset($path);
    }

    private function excerpt(?string $text, int $limit = 160): string
    {
        return Str::limit($this->plainText($text), $limit, '');
    }

    private function plainText(?string $text): string
    {
        $text = strip_tags((string) $text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim((string) preg_replace('/\s+/u', ' ', $text));
    }
}
