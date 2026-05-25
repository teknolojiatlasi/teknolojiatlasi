<div
    id="pwaInstallPrompt"
    class="pwa-install-prompt"
    hidden
    aria-live="polite"
    data-installed-key="pwa-install-installed"
    data-dismissed-key="pwa-install-dismissed-at"
>
    <div class="pwa-install-card">
        <button type="button" class="pwa-install-close" data-pwa-close aria-label="Kapat">
            &times;
        </button>
        <div class="pwa-install-badge">BY</div>
        <div class="pwa-install-copy">
            <p class="pwa-install-kicker">Teknoloji Atlası uygulamasi</p>
            <h2 class="pwa-install-title" data-pwa-title>Telefonunda daha hizli ac</h2>
            <p class="pwa-install-text" data-pwa-text>
                Uygulamayi ana ekrana ekleyerek tek dokunusla acabilir ve daha hizli deneyim elde edebilirsin.
            </p>
        </div>
        <div class="pwa-install-actions">
            <button type="button" class="pwa-install-btn" data-pwa-action hidden>Yukle</button>
        </div>
    </div>
</div>

<style>
    .pwa-install-prompt {
        position: fixed;
        left: 50%;
        bottom: max(1rem, env(safe-area-inset-bottom));
        z-index: 1100;
        width: min(100% - 1rem, 30rem);
        transform: translateX(-50%);
    }

    .pwa-install-card {
        position: relative;
        display: grid;
        gap: 0.9rem;
        padding: 1rem 1rem 1rem 0.95rem;
        border: 1px solid rgba(15, 23, 42, 0.12);
        border-radius: 1.35rem;
        background: rgba(15, 23, 42, 0.96);
        color: #fff;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.3);
        backdrop-filter: blur(14px);
    }

    .pwa-install-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.9rem;
        height: 2.9rem;
        border-radius: 1rem;
        background: linear-gradient(135deg, #f59e0b, #f97316);
        color: #111827;
        font-weight: 900;
        letter-spacing: 0.04em;
    }

    .pwa-install-kicker {
        margin: 0 0 0.25rem;
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.74rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .pwa-install-title {
        margin: 0 0 0.35rem;
        font-size: 1rem;
        font-weight: 800;
        line-height: 1.3;
    }

    .pwa-install-text {
        margin: 0;
        color: rgba(255, 255, 255, 0.82);
        font-size: 0.92rem;
        line-height: 1.55;
    }

    .pwa-install-actions {
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }

    .pwa-install-btn {
        border: 0;
        border-radius: 999px;
        padding: 0.75rem 1rem;
        background: linear-gradient(135deg, #60a5fa, #2563eb);
        color: #fff;
        font-size: 0.95rem;
        font-weight: 800;
        line-height: 1;
        cursor: pointer;
    }

    .pwa-install-close {
        position: absolute;
        top: 0.7rem;
        right: 0.75rem;
        width: 2rem;
        height: 2rem;
        border: 0;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.12);
        color: #fff;
        font-size: 1.15rem;
        line-height: 1;
        cursor: pointer;
    }

    @media (min-width: 768px) {
        .pwa-install-card {
            grid-template-columns: auto 1fr auto;
            align-items: center;
            gap: 1rem;
            padding-right: 3rem;
        }

        .pwa-install-actions {
            justify-content: flex-end;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const promptRoot = document.getElementById('pwaInstallPrompt');

        if (!promptRoot) {
            return;
        }

        const actionButton = promptRoot.querySelector('[data-pwa-action]');
        const closeButton = promptRoot.querySelector('[data-pwa-close]');
        const title = promptRoot.querySelector('[data-pwa-title]');
        const text = promptRoot.querySelector('[data-pwa-text]');
        const installedKey = promptRoot.dataset.installedKey || 'pwa-install-installed';
        const dismissedKey = promptRoot.dataset.dismissedKey || 'pwa-install-dismissed-at';
        const dismissDurationMs = 7 * 24 * 60 * 60 * 1000;
        const userAgent = navigator.userAgent || '';
        const isAndroid = /android/i.test(userAgent);
        const isIos = /iphone|ipad|ipod/i.test(userAgent);
        const isIosWebkit = /safari/i.test(userAgent) && !/crios|fxios|edgios|opios/i.test(userAgent);
        const isSafari = isIos && isIosWebkit;
        const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
        const isMobile = window.matchMedia('(max-width: 991.98px)').matches || isAndroid || isIos;
        let deferredPrompt = null;

        const storage = {
            get(key) {
                try {
                    return window.localStorage.getItem(key);
                } catch (error) {
                    return null;
                }
            },
            set(key, value) {
                try {
                    window.localStorage.setItem(key, value);
                } catch (error) {
                    // Ignore storage failures.
                }
            },
            remove(key) {
                try {
                    window.localStorage.removeItem(key);
                } catch (error) {
                    // Ignore storage failures.
                }
            },
        };

        const dismissedAt = Number(storage.get(dismissedKey) || '0');
        const isDismissed = dismissedAt > 0 && (Date.now() - dismissedAt) < dismissDurationMs;
        const isInstalled = storage.get(installedKey) === '1' || isStandalone;

        const hidePrompt = ({ rememberDismiss = false, markInstalled = false } = {}) => {
            promptRoot.hidden = true;

            if (rememberDismiss) {
                storage.set(dismissedKey, String(Date.now()));
            }

            if (markInstalled) {
                storage.set(installedKey, '1');
                storage.remove(dismissedKey);
            }
        };

        const showPrompt = () => {
            if (!isMobile || isInstalled || isDismissed) {
                return;
            }

            promptRoot.hidden = false;
        };

        if (isInstalled) {
            hidePrompt({ markInstalled: true });
            return;
        }

        closeButton?.addEventListener('click', () => {
            hidePrompt({ rememberDismiss: true });
        });

        window.addEventListener('appinstalled', () => {
            hidePrompt({ markInstalled: true });
        });

        if (isIos && isSafari && !isStandalone) {
            if (title) {
                title.textContent = 'iPhone ana ekranina ekle';
            }

            if (text) {
                text.textContent = 'Safari menusunda Paylas ve ardindan Ana Ekrana Ekle adimlarini kullanarak uygulamayi telefonunda sabitleyebilirsin.';
            }

            if (actionButton) {
                actionButton.hidden = true;
            }

            showPrompt();
            return;
        }

        window.addEventListener('beforeinstallprompt', (event) => {
            event.preventDefault();
            deferredPrompt = event;

            if (title) {
                title.textContent = 'Teknoloji Atlası uygulamasini ana ekrana ekle';
            }

            if (text) {
                text.textContent = 'Uygulamayi ana ekrana ekleyerek tarayiciya girmeden acabilir, daha hizli ve uygulama benzeri deneyim kullanabilirsin.';
            }

            if (actionButton) {
                actionButton.hidden = false;
            }

            showPrompt();
        });

        actionButton?.addEventListener('click', async () => {
            if (!deferredPrompt) {
                return;
            }

            deferredPrompt.prompt();
            const choice = await deferredPrompt.userChoice;
            deferredPrompt = null;

            if (choice?.outcome === 'accepted') {
                if (window.pwaPush?.enable) {
                    window.pwaPush.enable().catch(() => {});
                }

                hidePrompt({ markInstalled: true });
                return;
            }

            hidePrompt({ rememberDismiss: true });
        });
    });
</script>
