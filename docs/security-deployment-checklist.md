# Security Deployment Checklist

## Cloudflare / CDN

- Proxy mode: `DNS only` yerine `Proxied`
- SSL/TLS mode: `Full (strict)`
- Always Use HTTPS: `On`
- Automatic HTTPS Rewrites: `On`
- WAF managed rules: `On`
- Bot Fight Mode veya Super Bot Fight Mode: `On`
- Rate limiting:
  - `/login`: 10 istek / 1 dakika / IP
  - `/iletisim`: 5 istek / 1 dakika / IP
  - `/blog/*/comments`: 12 istek / 1 dakika / IP
  - `/survey/*/responses`: 8 istek / 1 dakika / IP
  - `/sosial/posts`: 10 istek / 1 dakika / IP
  - `/sosial/posts/*/comments`: 20 istek / 1 dakika / IP
  - `/sosial/comments/*/replies`: 20 istek / 1 dakika / IP
  - `/sosial/messages/*`: 20 istek / 1 dakika / IP
- Country/IP reputation rules:
  - Admin path `/yonetim*` icin gerekiyorsa ulke bazli allowlist
  - Tehdit skoru yuksek IP'ler icin challenge veya block

## App Environment

- `APP_ENV=production`
- `APP_DEBUG=false`
- `FORCE_HTTPS=true`
- `SESSION_SECURE_COOKIE=true`
- `TURNSTILE_ENABLED=true`
- `TURNSTILE_SITE_KEY=...`
- `TURNSTILE_SECRET_KEY=...`
- `LOG_SECURITY_LEVEL=info`
- `LOG_SECURITY_DAYS=30`
- `UPLOAD_ANTIVIRUS_COMMAND=` opsiyonel. Ornek: `clamscan --no-summary {file}`

## Hosting

- PHP version: `8.2` veya ustu
- `display_errors=Off`
- `expose_php=Off`
- Public dizinde gereksiz demo/ornek dosyalari kaldir
- `storage/logs/security*.log` dosyalarini izleyin

## Fail2ban / Panel IP Ban

- Uygulama artik `storage/logs/security*.log` icine auth/admin anomali kaydi yazar
- Ban tetiklemek icin ozellikle su olaylar izlenebilir:
  - `auth_failure`
  - `sensitive_response`
  - `security_burst_detected`
- Panel tabanli WAF veya Fail2ban ile kisa sureli ban onerisi:
  - 10 dakika icinde 10+ anomali: 15-30 dakika ban
  - 10 dakika icinde tekrar: 12-24 saat ban
