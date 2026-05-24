<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Minishlink\WebPush\VAPID;

class WebPushGenerateKeys extends Command
{
    protected $signature = 'webpush:generate-keys';

    protected $description = 'Generate VAPID keys for PWA web push notifications';

    public function handle(): int
    {
        if (! class_exists(VAPID::class)) {
            $this->error('minishlink/web-push paketi kurulu degil.');
            $this->line('Calistir: composer install');
            $this->line('veya: composer update minishlink/web-push');

            return self::FAILURE;
        }

        $keys = VAPID::createVapidKeys();

        $this->newLine();
        $this->info('VAPID anahtarlari olusturuldu.');
        $this->newLine();
        $this->line('Bunlari .env dosyana ekle:');
        $this->newLine();
        $this->line('WEBPUSH_ENABLED=true');
        $this->line('WEBPUSH_VAPID_SUBJECT=mailto:admin@example.com');
        $this->line('WEBPUSH_VAPID_PUBLIC_KEY=' . $keys['publicKey']);
        $this->line('WEBPUSH_VAPID_PRIVATE_KEY=' . $keys['privateKey']);
        $this->newLine();
        $this->comment('Not: private key sadece sunucuda kalmali.');

        return self::SUCCESS;
    }
}
