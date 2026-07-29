import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

const reverbScheme = import.meta.env.VITE_REVERB_SCHEME ?? 'http';

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    forceTLS: reverbScheme === 'https',
    enabledTransports: ['ws', 'wss'],
    // Private channels (e.g. course-restricted chat) need an authenticated
    // /broadcasting/auth request — without the token header it 419s and the
    // subscription silently fails.
    auth: {
        headers: {
            'X-CSRF-TOKEN': csrfToken,
        },
    },
});