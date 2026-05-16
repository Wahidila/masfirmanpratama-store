import './bootstrap';

import Alpine from 'alpinejs';

import cart from './stores/cart';

window.Alpine = Alpine;

// Register stores BEFORE Alpine.start() so they're available during x-init.
document.addEventListener('alpine:init', () => {
    Alpine.store('cart', cart);
});

Alpine.start();
