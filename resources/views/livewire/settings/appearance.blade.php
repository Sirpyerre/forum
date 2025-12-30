<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Appearance')" :subheading="__('Update the appearance settings for your account')">
        <div x-data class="flex gap-2 p-1 bg-zinc-100 dark:bg-zinc-800 rounded-lg">
            <button
                @click="$flux.appearance = 'light'"
                :class="$flux.appearance === 'light' ? 'bg-white dark:bg-zinc-700 shadow-sm' : 'hover:bg-zinc-200 dark:hover:bg-zinc-700'"
                class="flex-1 flex items-center justify-center gap-2 px-4 py-2 rounded-md text-sm font-medium text-zinc-900 dark:text-white transition-colors"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                {{ __('Light') }}
            </button>

            <button
                @click="$flux.appearance = 'dark'"
                :class="$flux.appearance === 'dark' ? 'bg-white dark:bg-zinc-700 shadow-sm' : 'hover:bg-zinc-200 dark:hover:bg-zinc-700'"
                class="flex-1 flex items-center justify-center gap-2 px-4 py-2 rounded-md text-sm font-medium text-zinc-900 dark:text-white transition-colors"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
                {{ __('Dark') }}
            </button>

            <button
                @click="$flux.appearance = 'system'"
                :class="$flux.appearance === 'system' ? 'bg-white dark:bg-zinc-700 shadow-sm' : 'hover:bg-zinc-200 dark:hover:bg-zinc-700'"
                class="flex-1 flex items-center justify-center gap-2 px-4 py-2 rounded-md text-sm font-medium text-zinc-900 dark:text-white transition-colors"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                {{ __('System') }}
            </button>
        </div>
    </x-settings.layout>
</section>
