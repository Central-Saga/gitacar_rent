<x-layouts::auth>
    <div class="flex flex-col w-full">
        <!-- Header -->
        <div class="mb-10">
            <h1 class="text-[32px] font-bold text-textDark mb-1">{{ __('Login') }}</h1>
            <div class="text-[15px] font-medium text-textGray flex items-center gap-1">
                <span>{{ __('Don\'t have an account?') }}</span>
                <flux:link :href="route('register')" wire:navigate
                    class="text-primary font-semibold hover:text-primaryDark !no-underline">{{ __('sign up') }}
                </flux:link>
            </div>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6 w-full">
            @csrf

            <!-- Email Address/Phone -->
            <div class="relative w-full">
                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5 text-textGray">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                </div>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    autocomplete="email" placeholder="Email"
                    class="w-full rounded-2xl border border-inputBorder bg-white pl-12 pr-5 py-4 text-[15px] font-medium text-textDark placeholder:text-textGray focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all shadow-sm" />
            </div>

            <!-- Password -->
            <div class="relative w-full">
                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5 text-textGray">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                </div>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                    placeholder="Password"
                    class="w-full rounded-2xl border border-inputBorder bg-white pl-12 pr-24 py-4 text-[15px] font-medium text-textDark placeholder:text-textGray focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all shadow-sm" />
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" wire:navigate
                        class="absolute right-5 top-1/2 -translate-y-1/2 text-primary font-bold text-[11px] tracking-widest uppercase hover:text-primaryDark">
                        {{ __('FORGOT') }}
                    </a>
                @endif
            </div>

            <!-- Remember Me -->
            <input type="checkbox" name="remember" class="hidden" checked />

            <div class="flex items-center justify-end mt-4">
                <button type="submit"
                    class="bg-primary hover:bg-primaryDark text-white font-semibold rounded-2xl px-6 py-3 flex items-center justify-center gap-2 transition-transform hover:-translate-y-0.5 shadow-md shadow-primary/20">
                    {{ __('Login') }}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                        stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </button>
            </div>
        </form>
    </div>
</x-layouts::auth>