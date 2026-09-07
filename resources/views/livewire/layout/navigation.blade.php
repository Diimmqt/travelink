<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="bg-white border-b border-brex-mist sticky top-0 z-50">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20 items-center">
            <div class="flex items-center gap-8">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5 group" wire:navigate>
                        <div class="w-9 h-9 rounded-brex bg-brex-ink flex items-center justify-center text-white font-bold text-lg">
                            T
                        </div>
                        <span class="font-semibold text-2xl tracking-brex-24 text-brex-ink">
                            Travelink
                        </span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden sm:flex sm:items-center sm:gap-2">
                    <a href="{{ route('home') }}" wire:navigate 
                       class="px-3.5 py-2 text-sm font-medium rounded-brex transition duration-150 tracking-brex-24 {{ request()->routeIs('home') ? 'text-brex-ink bg-brex-fog border border-brex-mist font-semibold' : 'text-brex-graphite hover:text-brex-ink hover:bg-brex-fog' }}">
                        {{ __('Beranda') }}
                    </a>
                    <a href="{{ route('tickets.history') }}" wire:navigate 
                       class="px-3.5 py-2 text-sm font-medium rounded-brex transition duration-150 tracking-brex-24 {{ request()->routeIs('tickets.*') ? 'text-brex-ink bg-brex-fog border border-brex-mist font-semibold' : 'text-brex-graphite hover:text-brex-ink hover:bg-brex-fog' }}">
                        {{ __('Tiket Saya') }}
                    </a>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:gap-3">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="brex-btn-secondary text-sm font-medium">
                            <span class="text-xs text-brex-pewter">Halo,</span>
                            <span class="text-brex-ink font-semibold" x-data="{{ json_encode(['name' => auth()->user()->nama ?? auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.nama"></span>

                            <svg class="w-4 h-4 text-brex-graphite" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="bg-white border border-brex-mist rounded-brex shadow-brex-modal overflow-hidden py-1">
                            <x-dropdown-link :href="route('profile')" wire:navigate class="text-brex-graphite hover:text-brex-ink hover:bg-brex-fog px-4 py-2.5 text-sm flex items-center gap-2 font-medium">
                                <svg class="w-4 h-4 text-brex-pewter" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                {{ __('Profil Saya') }}
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('tickets.history')" wire:navigate class="text-brex-graphite hover:text-brex-ink hover:bg-brex-fog px-4 py-2.5 text-sm flex items-center gap-2 font-medium">
                                <svg class="w-4 h-4 text-brex-pewter" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                </svg>
                                {{ __('Riwayat Tiket') }}
                            </x-dropdown-link>

                            <div class="border-t border-brex-mist my-1"></div>

                            <!-- Authentication -->
                            <button wire:click="logout" class="w-full text-start text-red-600 hover:bg-red-50 px-4 py-2.5 text-sm flex items-center gap-2 font-semibold transition duration-150">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                {{ __('Keluar') }}
                            </button>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-brex text-brex-ink hover:bg-brex-fog focus:outline-none transition duration-150">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white border-b border-brex-mist px-4 pt-2 pb-6 space-y-3">
        <div class="space-y-1">
            <a href="{{ route('home') }}" wire:navigate class="block px-4 py-2.5 rounded-brex text-base font-medium text-brex-ink hover:bg-brex-fog">
                {{ __('Beranda') }}
            </a>
            <a href="{{ route('tickets.history') }}" wire:navigate class="block px-4 py-2.5 rounded-brex text-base font-medium text-brex-ink hover:bg-brex-fog">
                {{ __('Tiket Saya') }}
            </a>
            <a href="{{ route('profile') }}" wire:navigate class="block px-4 py-2.5 rounded-brex text-base font-medium text-brex-ink hover:bg-brex-fog">
                {{ __('Profil Saya') }}
            </a>
        </div>

        <div class="pt-4 border-t border-brex-mist flex flex-col gap-2">
            <span class="text-sm text-brex-pewter px-4">
                Masuk sebagai <strong class="text-brex-ink">{{ auth()->user()->nama ?? auth()->user()->name }}</strong>
            </span>
            <button wire:click="logout" class="w-full text-center py-2.5 border border-red-200 text-red-600 font-semibold rounded-brex hover:bg-red-50 transition">
                {{ __('Keluar') }}
            </button>
        </div>
    </div>
</nav>
