<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <div
            class="relative w-full h-auto"
            x-cloak
            x-data="{
                showRecoveryInput: @js($errors->has('recovery_code')),
                code: '',
                recovery_code: '',
                toggleInput() {
                    this.showRecoveryInput = !this.showRecoveryInput;

                    this.code = '';
                    this.recovery_code = '';

                    $dispatch('clear-2fa-auth-code');

                    $nextTick(() => {
                        this.showRecoveryInput
                            ? this.$refs.recovery_code?.focus()
                            : $dispatch('focus-2fa-auth-code');
                    });
                },
            }"
        >
            <div x-show="!showRecoveryInput">
                <x-auth-header
                    :title="__('Authentication Code')"
                    :description="__('Enter the authentication code provided by your authenticator application.')"
                />
            </div>

            <div x-show="showRecoveryInput">
                <x-auth-header
                    :title="__('Recovery Code')"
                    :description="__('Please confirm access to your account by entering one of your emergency recovery codes.')"
                />
            </div>

            <form method="POST" action="{{ route('two-factor.login.store') }}">
                @csrf

                <div class="space-y-5 text-center">
                    <div x-show="!showRecoveryInput">
                        <div class="flex items-center justify-center my-5 gap-2"
                             x-data="{
                                 inputs: ['', '', '', '', '', ''],
                                 focusNext(index) {
                                     if (index < 5) {
                                         $refs['otp-' + (index + 1)].focus();
                                     }
                                 },
                                 handleInput(index, event) {
                                     this.inputs[index] = event.target.value;
                                     this.code = this.inputs.join('');
                                     if (event.target.value && index < 5) {
                                         this.focusNext(index);
                                     }
                                 },
                                 handleKeydown(index, event) {
                                     if (event.key === 'Backspace' && !this.inputs[index] && index > 0) {
                                         $refs['otp-' + (index - 1)].focus();
                                     }
                                 }
                             }"
                             @clear-2fa-auth-code.window="inputs = ['', '', '', '', '', '']; code = ''; $refs['otp-0'].focus()"
                             @focus-2fa-auth-code.window="$refs['otp-0'].focus()">
                            <template x-for="(input, index) in inputs" :key="index">
                                <input
                                    type="text"
                                    maxlength="1"
                                    :x-ref="'otp-' + index"
                                    x-model="inputs[index]"
                                    @input="handleInput(index, $event)"
                                    @keydown="handleKeydown(index, $event)"
                                    class="w-12 h-12 text-center text-2xl font-bold border-2 border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 transition-colors"
                                    pattern="[0-9]"
                                    inputmode="numeric"
                                />
                            </template>
                            <input type="hidden" name="code" x-model="code">
                        </div>
                    </div>

                    <div x-show="showRecoveryInput">
                        <div class="my-5">
                            <input
                                type="text"
                                name="recovery_code"
                                x-ref="recovery_code"
                                x-bind:required="showRecoveryInput"
                                autocomplete="one-time-code"
                                x-model="recovery_code"
                                class="w-full px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white placeholder-zinc-500 dark:placeholder-zinc-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                            />
                        </div>

                        @error('recovery_code')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        class="w-full px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-500/50 transition-colors"
                    >
                        {{ __('Continue') }}
                    </button>
                </div>

                <div class="mt-5 space-x-0.5 text-sm leading-5 text-center text-zinc-600 dark:text-zinc-400">
                    <span class="opacity-50">{{ __('or you can') }}</span>
                    <button type="button" class="inline font-medium underline cursor-pointer opacity-80 hover:opacity-100">
                        <span x-show="!showRecoveryInput" @click="toggleInput()">{{ __('login using a recovery code') }}</span>
                        <span x-show="showRecoveryInput" @click="toggleInput()">{{ __('login using an authentication code') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.auth>
