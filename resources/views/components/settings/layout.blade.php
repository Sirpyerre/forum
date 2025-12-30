<div class="flex items-start max-md:flex-col">
    <div class="w-full pb-4 md:w-[220px] md:mr-10">
        <nav class="space-y-1">
            <a href="{{ route('profile.edit') }}"
               class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('profile.edit') ? 'bg-zinc-200 dark:bg-zinc-800 text-zinc-900 dark:text-white' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800' }}"
               wire:navigate>
                {{ __('Profile') }}
            </a>
            <a href="{{ route('user-password.edit') }}"
               class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('user-password.edit') ? 'bg-zinc-200 dark:bg-zinc-800 text-zinc-900 dark:text-white' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800' }}"
               wire:navigate>
                {{ __('Password') }}
            </a>
            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <a href="{{ route('two-factor.show') }}"
                   class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('two-factor.show') ? 'bg-zinc-200 dark:bg-zinc-800 text-zinc-900 dark:text-white' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800' }}"
                   wire:navigate>
                    {{ __('Two-Factor Auth') }}
                </a>
            @endif
            <a href="{{ route('appearance.edit') }}"
               class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('appearance.edit') ? 'bg-zinc-200 dark:bg-zinc-800 text-zinc-900 dark:text-white' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800' }}"
               wire:navigate>
                {{ __('Appearance') }}
            </a>
        </nav>
    </div>

    <div class="h-px w-full bg-zinc-200 dark:bg-zinc-700 md:hidden my-6"></div>

    <div class="flex-1 self-stretch max-md:pt-6">
        <h2 class="text-2xl font-semibold text-zinc-900 dark:text-white">{{ $heading ?? '' }}</h2>
        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">{{ $subheading ?? '' }}</p>

        <div class="mt-5 w-full max-w-lg">
            {{ $slot }}
        </div>
    </div>
</div>
