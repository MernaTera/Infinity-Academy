import './bootstrap';
import Echo from 'laravel-echo';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

window.Echo = new Echo({
    broadcaster: 'reverb',

    wsHost: window.location.hostname,
    wsPort: 8080,

    wssPort: 8080,
    forceTLS: false,

    enabledTransports: ['ws'], 
});
Alpine.start();
