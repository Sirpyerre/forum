<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<section class="mt-10 space-y-6" x-data="{ showDeleteModal: {{ $errors->isNotEmpty() ? 'true' : 'false' }} }">
    <div class="relative mb-5">
        <h3 class="text-xl font-semibold text-zinc-900 dark:text-white">{{ __('Delete account') }}</h3>
        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">{{ __('Delete your account and all of its resources') }}</p>
    </div>

    <button
        type="button"
        @click="showDeleteModal = true"
        class="px-4 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 focus:ring-4 focus:ring-red-500/50 transition-colors"
        data-test="delete-user-button"
    >
        {{ __('Delete account') }}
    </button>

    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="showDeleteModal = false" class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity"></div>

            <div class="relative bg-white dark:bg-zinc-800 rounded-lg shadow-xl max-w-lg w-full p-6">
                <form method="POST" wire:submit="deleteUser" class="space-y-6">
                    <div>
                        <h3 class="text-xl font-semibold text-zinc-900 dark:text-white">{{ __('Are you sure you want to delete your account?') }}</h3>

                        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                        </p>
                    </div>

                    <div>
                        <label for="delete_password" class="block text-sm font-medium text-zinc-900 dark:text-white mb-2">{{ __('Password') }}</label>
                        <input
                            id="delete_password"
                            type="password"
                            wire:model="password"
                            class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white placeholder-zinc-500 dark:placeholder-zinc-400 focus:ring-2 focus:ring-red-500 focus:border-transparent transition-colors"
                        />
                        @error('password')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end space-x-2">
                        <button
                            type="button"
                            @click="showDeleteModal = false"
                            class="px-4 py-2 bg-zinc-200 dark:bg-zinc-700 text-zinc-900 dark:text-white font-semibold rounded-lg hover:bg-zinc-300 dark:hover:bg-zinc-600 transition-colors"
                        >
                            {{ __('Cancel') }}
                        </button>

                        <button
                            type="submit"
                            class="px-4 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 focus:ring-4 focus:ring-red-500/50 transition-colors"
                            data-test="confirm-delete-user-button"
                        >
                            {{ __('Delete account') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
