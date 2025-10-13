import './bootstrap';
import Alpine from 'alpinejs';
import Livewire from '../../vendor/livewire/livewire/dist/livewire.esm';  // ✅ import Livewire’s JS

window.Alpine = Alpine;

// ✅ Start Livewire first (so $wire is available globally)
Livewire.start();

// ✅ Then start Alpine (so it can interact with $wire)
Alpine.start();
