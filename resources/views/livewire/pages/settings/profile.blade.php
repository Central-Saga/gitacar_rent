<?php

use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use ProfileValidationRules;
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public ?string $phone_number = '';
    public ?string $username = '';
    public $avatar;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
        $this->phone_number = Auth::user()->phone_number;
        $this->username = Auth::user()->username;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate($this->profileRules($user->id));

        if ($this->avatar) {
            $this->validate(['avatar' => ['image', 'max:2048']]);
            $avatarPath = $this->avatar->store('avatars', 'public');
            $user->avatar = $avatarPath;
            $this->avatar = null;
        }

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);

        $this->redirectRoute('profile.edit', navigate: true);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && !Auth::user()->hasVerifiedEmail();
    }

}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Profile Settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Profile')" :subheading="__('Perbarui foto profil, informasi dasar, dan kontak Anda.')">
        <div class="mb-8 p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg border border-zinc-200 dark:border-zinc-700/50">
            <h3 class="text-sm font-semibold text-zinc-800 dark:text-zinc-200 mb-3">{{ __('Informasi Sistem & Role') }}
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <span class="block text-xs font-medium text-zinc-500">{{ __('Role') }}</span>
                    <span
                        class="block text-sm font-medium text-zinc-900 dark:text-white mt-1 capitalize">{{ auth()->user()->roles->pluck('name')->implode(', ') ?: 'Karyawan' }}</span>
                </div>
                <div>
                    <span class="block text-xs font-medium text-zinc-500">{{ __('Tanggal Dibuat') }}</span>
                    <span
                        class="block text-sm font-medium text-zinc-900 dark:text-white mt-1">{{ auth()->user()->created_at->format('d M Y') }}</span>
                </div>
                <div>
                    <span class="block text-xs font-medium text-zinc-500">{{ __('Status Akun') }}</span>
                    <span
                        class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20 mt-1 capitalize">{{ auth()->user()->status }}</span>
                </div>
            </div>
        </div>

        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <div class="flex items-center gap-x-6">
                <div>
                    @if ($avatar)
                        <img src="{{ $avatar->temporaryUrl() }}" class="h-16 w-16 rounded-full object-cover">
                    @elseif (auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                            class="h-16 w-16 rounded-full object-cover">
                    @else
                        <div
                            class="h-16 w-16 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center text-xl font-bold text-zinc-500 dark:text-zinc-400">
                            {{ auth()->user()->initials() }}
                        </div>
                    @endif
                </div>
                <div>
                    <label
                        class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">{{ __('Foto Profil (Opsional)') }}</label>
                    <input type="file" wire:model="avatar"
                        class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-zinc-100 file:text-zinc-700 hover:file:bg-zinc-200 dark:file:bg-zinc-800 dark:file:text-zinc-300 dark:hover:file:bg-zinc-700"
                        accept="image/*">
                    <div wire:loading wire:target="avatar" class="text-sm mt-1 text-zinc-500">{{ __('Mengunggah...') }}
                    </div>
                    @error('avatar') <span class="text-sm text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <flux:input wire:model="name" :label="__('Nama Lengkap')" type="text" required autofocus
                autocomplete="name" />

            <flux:input wire:model="username" :label="__('Username (jika kamu pakai)')" type="text"
                autocomplete="username" />
            <flux:input wire:model="phone_number" :label="__('Nomor Telepon')" type="tel" autocomplete="tel" />

            <div>
                <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />

                @if ($this->hasUnverifiedEmail)
                    <div>
                        <flux:text class="mt-4">
                            {{ __('Your email address is unverified.') }}

                            <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email.') }}
                            </flux:link>
                        </flux:text>

                        @if (session('status') === 'verification-link-sent')
                            <flux:text class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </flux:text>
                        @endif
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button type="submit"
                        class="w-full !bg-teal-600 hover:!bg-teal-700 !text-white !border-transparent"
                        data-test="update-profile-button">
                        {{ __('Save') }}
                    </flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>

    </x-pages::settings.layout>
</section>