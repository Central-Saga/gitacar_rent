<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use function Livewire\Volt\mount;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    // Data diri pelanggan pada user
    public string $telepon = '';

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
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'telepon' => ['required', 'string', 'max:15', 'unique:users,telepon'],
        ], [
            'name.required' => 'All fields are required',
            'email.required' => 'All fields are required',
            'email.email' => 'All fields are required',
            'email.unique' => 'Email sudah digunakan',
            'password.required' => 'All fields are required',
            'password.min' => 'Password must be at least 8 characters',
            'password.confirmed' => 'password doesn\'t match',
            'telepon.required' => 'All fields are required',
            'telepon.unique' => 'Nomor telepon sudah digunakan',
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'telepon' => $validated['telepon'],
            'role' => 'Pengunjung',
            'password' => Hash::make($validated['password']),
        ];

        // Create user first
        $user = User::create($userData);

        // Assign role "Pengunjung" to new user
        try {
            Role::firstOrCreate(['name' => 'Pengunjung', 'guard_name' => 'web']);
            $user->assignRole('Pengunjung');
        } catch (\Throwable $e) {
            // fallback: role sudah tersimpan di kolom users.role
        }

        event(new Registered($user));

        // Log the user in immediately
        Auth::login($user);
        
        // Regenerate the session to ensure security
        Session::regenerate();
        
        // Use native redirect to ensure the navigation state is properly updated
        // The navigate:false parameter ensures a full page load which refreshes all components
        $this->redirectIntended(route('redirect.role', absolute: false), navigate: false);
    }
}; ?>

<div class="space-y-6">
    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <!-- Alert Messages -->
    <x-alert />

    <form wire:submit.prevent="register" class="space-y-6">
        <!-- Name field -->
        <div class="space-y-1">
            <label for="name" class="block text-sm font-medium text-slate-700">
                {{ __('Name') }}
            </label>
            <input
                wire:model.live="name"
                type="text"
                id="name"
                name="name"
                autocomplete="name"
                required
                autofocus
                placeholder="{{ __('Full name') }}"
                class="mt-1 w-full rounded-lg border @error('name') border-red-500 @else border-slate-200 @enderror bg-white text-slate-800 placeholder-slate-400
                       focus:outline-none focus:ring-2 focus:ring-[#133E87]/30 focus:border-[#133E87] px-3 py-2.5"
            />
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

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
                autocomplete="new-password"
                required
                placeholder="••••••••"
                class="mt-1 w-full rounded-lg border @error('password') border-red-500 @else border-slate-200 @enderror bg-white text-slate-800 placeholder-slate-400
                       focus:outline-none focus:ring-2 focus:ring-[#133E87]/30 focus:border-[#133E87] px-3 py-2.5"
            />
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password field -->
        <div class="space-y-1">
            <label for="password_confirmation" class="block text-sm font-medium text-slate-700">
                {{ __('Confirm password') }}
            </label>
            <input
                wire:model.live="password_confirmation"
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                autocomplete="new-password"
                required
                placeholder="••••••••"
                class="mt-1 w-full rounded-lg border @error('password_confirmation') border-red-500 @else border-slate-200 @enderror bg-white text-slate-800 placeholder-slate-400
                       focus:outline-none focus:ring-2 focus:ring-[#133E87]/30 focus:border-[#133E87] px-3 py-2.5"
            />
            @error('password_confirmation')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Phone Number field -->
        <div class="space-y-1">
            <label for="telepon" class="block text-sm font-medium text-slate-700">
                {{ __('Phone Number') }}
            </label>
            <input
                wire:model.live="telepon"
                type="tel"
                id="telepon"
                name="telepon"
                autocomplete="tel"
                required
                placeholder="{{ __('08123456789') }}"
                class="mt-1 w-full rounded-lg border @error('telepon') border-red-500 @else border-slate-200 @enderror bg-white text-slate-800 placeholder-slate-400
                       focus:outline-none focus:ring-2 focus:ring-[#133E87]/30 focus:border-[#133E87] px-3 py-2.5"
            />
            @error('telepon')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Register Button -->
        <button
            type="submit"
            class="w-full rounded-lg bg-[#1D2D20] hover:bg-[#17251A] text-[#FBFAF6] font-medium py-2.5
                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1D2D20]">
            {{ __('Create account') }}
        </button>
    </form>

    @if (Route::has('login'))
    <div class="text-center text-sm text-slate-500">
        <span>{{ __('Already have an account?') }}</span>
        <a href="{{ route('login') }}"
           wire:navigate
           class="text-[#133E87] hover:underline ml-1">
            {{ __('Log in') }}
        </a>
    </div>
    @endif
</div>
