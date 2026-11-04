import * as Turbo from '@hotwired/turbo';
import 'instant.page';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
window.Turbo = Turbo;

Alpine.start();

// Bridge Turbo page navigation to legacy DOMContentLoaded listeners
let isFirstLoad = true;
document.addEventListener('turbo:load', () => {
    if (isFirstLoad) {
        isFirstLoad = false;
        return;
    }
    document.dispatchEvent(new Event('DOMContentLoaded'));
});
