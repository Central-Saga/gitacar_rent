<?php

namespace App\Livewire\Settings;

use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts::app')]
#[Title('Profile')]
class Profile extends Component
{
    use ProfileValidationRules;
    use WithFileUploads;

    public string $name = '';

    public string $email = '';

    public ?string $phone_number = '';

    public ?string $username = '';

    public $avatar;

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone_number = $user->phone_number;
        $this->username = $user->username;
    }

    public function render(): View
    {
        return view('pages.settings.profile');
    }

    public function updateProfileInformation(): void
    {
        /** @var User $user */
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

    public function resendVerificationNotification(): void
    {
        /** @var User $user */
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
        /** @var User $user */
        $user = Auth::user();

        return $user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail();
    }
}
