<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use function Livewire\Volt\mount;

new #[Layout('components.layouts.auth')] class extends Component {
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Mount the component and save intended URL if provided.
     */
    public function mount(): void
    {
        if (request()->has('intended')) {
            $intendedUrl = request()->get('intended');
            if ($intendedUrl) {
                Session::put('url.intended', $intendedUrl);
            }
        }
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => 'Email and Password doesn\'t match',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        // Use full HTTP redirect so the landing page fully reloads
        // This ensures all page scripts (GSAP, AOS, custom init) run after login
        $this->redirectIntended(default: route('redirect.role', absolute: false), navigate: false);
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}; ?>

<div class="space-y-6">
    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <!-- Alert Messages -->
    <x-alert />

    <form wire:submit.prevent="login" class="space-y-6">
        <!-- Email field -->
        <div class="space-y-1">
            <label for="email" class="block text-sm font-medium text-slate-700">
                {{ __('Email address') }}
            </label>
            <input
                wire:model.live="email"
                type="email"
                id="email"
                name="email"
                autocomplete="email"
                required
                placeholder="email@example.com"
                class="mt-1 w-full rounded-lg border @error('email') border-red-500 @else border-slate-200 @enderror bg-white text-slate-800 placeholder-slate-400
                       focus:outline-none focus:ring-2 focus:ring-[#133E87]/30 focus:border-[#133E87] px-3 py-2.5"
            />
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password field -->
        <div class="space-y-1">
            <label for="password" class="block text-sm font-medium text-slate-700">
                {{ __('Password') }}
            </label>
            <input
                wire:model.live="password"
                type="password"
                id="password"
                name="password"
                autocomplete="current-password"
                required
                placeholder="••••••••"
                class="mt-1 w-full rounded-lg border @error('password') border-red-500 @else border-slate-200 @enderror bg-white text-slate-800 placeholder-slate-400
                       focus:outline-none focus:ring-2 focus:ring-[#133E87]/30 focus:border-[#133E87] px-3 py-2.5"
            />

            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me and Forgot Password -->
        <div class="flex items-center justify-between text-sm">
            <div class="flex items-center">
                <input
                    type="checkbox"
                    wire:model="remember"
                    id="remember"
                    class="w-4 h-4 rounded border-slate-300 text-[#133E87] focus:ring-[#133E87]/30"
                >
                <label for="remember" class="ml-2 text-slate-600 cursor-pointer">
                    {{ __('Remember me') }}
                </label>
            </div>
        </div>

        <!-- Login Button -->
        <button
            type="submit"
            class="w-full rounded-lg bg-[#1D2D20] hover:bg-[#17251A] text-[#FBFAF6] font-medium py-2.5
                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1D2D20]">
            {{ __('Sign in to account') }}
        </button>
    </form>

    @if (Route::has('register'))
    <div class="text-center text-sm text-slate-500">
        <span>{{ __('Don\'t have an account?') }}</span>
        <a href="{{ route('register') }}"
           wire:navigate
           class="text-[#133E87] hover:underline ml-1">
            {{ __('Create account') }}
        </a>
    </div>
    @endif
</div>
