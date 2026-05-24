# Simulation Modülü

Bu modül, Laravel 11 tabanlı mevcut modüler yapı içerisinde fizik, kimya ve matematik gibi alanlara yönelik etkileşimli simülasyon içeriklerini yönetmek için tasarlandı.

## Hedef

- Hiyerarşik kategori ve konu ağacı ile içerik organizasyonu sağlamak
- HTML/CSS/JS tabanlı interaktif simülasyonları kaydetmek ve yayınlamak
- Video ve görsel tabanlı alternatif içerikleri desteklemek
- Yapay zeka destekli editör akışına uygun versiyonlama altyapısı sunmak
- Favori, arama ve istatistik gibi ürünleşme ihtiyaçlarına temel hazırlamak

## Modül Yapısı

```text
Modules/Simulation
├── app/
│   ├── Http/Controllers
│   ├── Models
│   └── Providers
├── config
├── database/migrations
├── repositories
├── resources/views
├── routes
└── services
```

`repositories/` ve `services/` klasörleri modül seviyesinde tutuldu; `composer.json` içinde ayrıca autoload edildi.

## Domain Tasarımı

### 1. Kategori Ağacı

`simulation_categories`

- `parent_id`: ağaç yapı
- `name`, `slug`
- `description`, `icon`
- `sort_order`, `is_active`

Bu tablo; kategori, alt kategori, konu ve alt konu yapısını tek ağaç içinde taşıyacak şekilde tasarlandı. Gerekirse ileride `depth` veya `type` alanı eklenebilir.

### 2. Simülasyon İçeriği

`simulations`

- içerik metası: `title`, `slug`, `excerpt`, `content`
- sınıflandırma: `category_id`, `topic_path`
- içerik tipi: `content_type`
- editör alanları: `html_code`, `css_code`, `js_code`
- medya alanları: `video_url`, `video_source`, `cover_image`
- yayın alanları: `status`, `published_at`, `is_featured`
- SEO alanları: `seo_title`, `seo_description`, `seo_keywords`

### 3. Medya Yönetimi

`simulation_media`

- aynı simülasyona bağlı çoklu görsel/video/ek dosya desteği
- `meta` alanı ile esnek veri saklama

### 4. Versiyonlama

`simulation_versions`

- her HTML/CSS/JS revizyonunu saklar
- `version` alanı artımlıdır
- `change_note` ile edit geçmişi okunabilir hale gelir

### 5. Kullanıcı Özellikleri

`simulation_favorites`

- kullanıcı bazlı favori sistemi

`simulation_stats`

- görüntülenme, çalışma, favori ve paylaşım sayaçları

## Uygulama Katmanları

### Controller

- `SimulationController`
- listeleme ve detay gösterimi

### Repository

- `SimulationRepository`
- yayınlanmış içerik sorguları için tek nokta

### Service

- `SimulationEditorService`
- preview dokümanı üretimi
- versiyon kayıt mantığı

## Editör Tasarımı

Önerilen admin editör ekranı:

- sol panel: meta bilgiler
- orta panel: HTML / CSS / JavaScript sekmeleri
- sağ panel: canlı iframe önizleme

Canlı önizleme için:

- `srcdoc` tabanlı iframe
- sandbox kısıtları
- debounce ile kontrollü render

## Yol Haritası

1. Admin CRUD ekranları
2. Kategori ağacı sıralama ve drag-drop
3. Monaco veya CodeMirror entegrasyonu
4. güvenli HTML sanitization ve script politikası
5. medya yükleme altyapısı
6. favori ve istatistik servisleri
7. arama ve filtreleme
8. ilgili içerik önerileri
9. API endpoint genişletmesi
10. test kapsamının eklenmesi

## Notlar

- Bu başlangıç paketi modülü projeye kaydedip büyütebilmek için temel omurgayı kurar.
- Admin panel route ve view'ları henüz eklenmedi; mevcut projedeki yetki yapısına göre ikinci adımda tamamlanmalıdır.
- `show.blade.php` içindeki iframe render yaklaşımı temel örnektir; yayın öncesi XSS ve kaynak izolasyonu ayrıca sertleştirilmelidir.
