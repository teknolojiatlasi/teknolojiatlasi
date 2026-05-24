<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (!('serviceWorker' in navigator) || !('PushManager' in window) || !('Notification' in window)) {
            return;
        }

        const vapidKeyUrl = @json(route('push.vapid-public-key'));
        const subscribeUrl = @json(route('push.subscriptions.store'));
        const unsubscribeUrl = @json(route('push.subscriptions.destroy'));
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const promptStorageKey = 'pwa-push-permission-requested';
        const permissionDeniedKey = 'pwa-push-permission-denied';
        const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
        const isIos = /iphone|ipad|ipod/i.test(navigator.userAgent || '');

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
        };

        const urlBase64ToUint8Array = (base64String) => {
            const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
            const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
            const rawData = window.atob(base64);

            return Uint8Array.from(rawData, (char) => char.charCodeAt(0));
        };

        const persistSubscription = async (subscription) => {
            if (!csrfToken || !subscription) {
                return;
            }

            await fetch(subscribeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    endpoint: subscription.endpoint,
                    keys: {
                        p256dh: subscription.toJSON().keys?.p256dh,
                        auth: subscription.toJSON().keys?.auth,
                    },
                }),
            });
        };

        const removeSubscription = async (subscription) => {
            if (!csrfToken || !subscription) {
                return;
            }

            await fetch(unsubscribeUrl, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    endpoint: subscription.endpoint,
                }),
            });
        };

        const ensurePushSubscription = async ({ askPermission = false } = {}) => {
            const response = await fetch(vapidKeyUrl, {
                headers: {
                    Accept: 'application/json',
                },
            });

            if (!response.ok) {
                return;
            }

            const config = await response.json();

            if (!config.enabled || !config.publicKey) {
                return;
            }

            const registration = await navigator.serviceWorker.ready;
            const existingSubscription = await registration.pushManager.getSubscription();

            if (Notification.permission === 'granted') {
                if (existingSubscription) {
                    await persistSubscription(existingSubscription);
                    return;
                }

                const createdSubscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array(config.publicKey),
                });

                await persistSubscription(createdSubscription);
                return;
            }

            if (!askPermission || Notification.permission === 'denied') {
                if (Notification.permission === 'denied' && existingSubscription) {
                    await removeSubscription(existingSubscription);
                }

                return;
            }

            const permission = await Notification.requestPermission();

            storage.set(promptStorageKey, '1');

            if (permission === 'denied') {
                storage.set(permissionDeniedKey, '1');
                return;
            }

            if (permission !== 'granted') {
                return;
            }

            storage.set(permissionDeniedKey, '0');

            const createdSubscription = existingSubscription || await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(config.publicKey),
            });

            await persistSubscription(createdSubscription);
        };

        window.pwaPush = {
            enable() {
                return ensurePushSubscription({ askPermission: true });
            },
        };

        window.addEventListener('appinstalled', () => {
            ensurePushSubscription({ askPermission: true }).catch(() => {});
        });

        if (Notification.permission === 'granted') {
            ensurePushSubscription().catch(() => {});
            return;
        }

        if (!isStandalone) {
            return;
        }

        if (isIos || storage.get(promptStorageKey) !== '1') {
            ensurePushSubscription({ askPermission: true }).catch(() => {});
        }
    });
</script>
