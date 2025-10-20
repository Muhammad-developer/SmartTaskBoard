import './bootstrap';
import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';
import collapse from '@alpinejs/collapse';
import Sortable from 'sortablejs';

// Register Alpine plugins
Alpine.plugin(intersect);
Alpine.plugin(collapse);

// Make Sortable available globally
window.Sortable = Sortable;

// Start Alpine
window.Alpine = Alpine;
Alpine.start();
