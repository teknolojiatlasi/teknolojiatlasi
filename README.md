1. ADIM — LARAVEL 11 PROJEYİ OLUŞTURALIM
laravel new modular-cms
cd modular-cms


veya:

composer create-project laravel/laravel modular-cms
cd modular-cms


✅ Kontrol:

php artisan serve


Tarayıcıda Laravel açılıyorsa → DEVAM

🧱 2. ADIM — NWIDART LARAVEL MODULES KURULUMU
composer require nwidart/laravel-modules


✅ ELLE ServiceProvider EKLEME YOK
Laravel 11 otomatik tanır.

🧱 3. ADIM — CONFIG + STUB PUBLISH
php artisan vendor:publish --provider="Nwidart\Modules\LaravelModulesServiceProvider"


✅ Oluşması gereken:

config/modules.php

🧱 4. ADIM — composer.json (EN KRİTİK ADIM ❗)

composer.json → extra bölümünü BİREBİR şöyle yap:

"extra": {
    "laravel": {
        "dont-discover": []
    },
    "merge-plugin": {
        "include": [
            "Modules/*/composer.json"
        ]
    }
}


Sonra:

composer dump-autoload


✅ Bu yapılmazsa:

ServiceProvider bulunmaz

Route’lar gelmez

“Class not found” olur

🧱 5. ADIM — config/modules.php OPTİMİZASYONU
🎯 Amacımız:

Gereksiz dosyalar üretilmesin

Modüller sade olsun

✅ ÖNERİLEN AYARLAR
🔹 Stub ayarı
'stubs' => [
    'enabled' => false,
],

🔹 Generator (NET VE TEMİZ)
'generator' => [

    'provider' => ['path' => 'app/Providers', 'generate' => true],
    'controller' => ['path' => 'app/Http/Controllers', 'generate' => true],
    'model' => ['path' => 'app/Models', 'generate' => true],

    'routes' => ['path' => 'routes', 'generate' => true],
    'views' => ['path' => 'resources/views', 'generate' => true],
    'config' => ['path' => 'config', 'generate' => true],

    // ❌ KAPALI
    'migration' => ['generate' => false],
    'factory' => ['generate' => false],
    'seeder' => ['generate' => false],
],

🧱 6. ADIM — İLK MODÜLLERİ OLUŞTURALIM

php artisan module:make Blog --plain


✅ Klasör yapısı:

Modules/
 ├── Dashboard
 ├── Auth
 ├── User
 ├── Page
 └── Blog

🧱 7. ADIM — MODÜLLERİ AKTİF ET

php artisan module:enable Blog


✅ module_statuses.json oluşur.


x Controller oluşturma (Modül içine)
php artisan module:make-controller BlogController x

3. Blog Model oluşturma
php artisan module:make-model Blog Blog


git pull

composer install --no-dev --optimize-autoloader

php artisan optimize:clear

php artisan migrate --force

php artisan storage:link


php artisan optimize:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear
