<x-layouts::auth>
    <div class="flex flex-col w-full">
        <!-- Header -->
        <div class="mb-6 flex flex-col gap-2 relative">
            <a href="{{ route('login') }}" wire:navigate
                class="absolute -top-10 -left-2 p-2 hover:bg-gray-100 rounded-full transition-colors text-textDark">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                    stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
            </a>
            <h1 class="text-[32px] font-bold text-textDark mb-0 mt-4">{{ __('Create account') }}</h1>
            <div class="text-[15px] font-medium text-textGray flex items-center gap-1">
                <span>{{ __('Already have an account?') }}</span>
                <flux:link :href="route('login')" wire:navigate
                    class="text-primary font-semibold hover:text-primaryDark !no-underline">{{ __('sign in') }}
                </flux:link>
            </div>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-2xl">
                <ul class="list-disc list-inside text-sm text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6 w-full"
              x-data="{
                  password: '',
                  get strength() {
                      let s = 0;
                      if (this.password.length >= 8) s++;
                      if (this.password.length >= 12) s++;
                      if (/[a-z]/.test(this.password) && /[A-Z]/.test(this.password)) s++;
                      if (/\d/.test(this.password)) s++;
                      if (/[^a-zA-Z0-9]/.test(this.password)) s++;
                      return s;
                  },
                  get strengthLabel() {
                      const labels = ['', 'Lemah', 'Cukup', 'Sedang', 'Kuat', 'Sangat Kuat'];
                      return labels[this.strength] || '';
                  },
                  get strengthColor() {
                      const colors = ['', 'bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-lime-500', 'bg-green-500'];
                      return colors[this.strength] || '';
                  },
                  get strengthBg() {
                      const bg = ['', 'bg-red-50', 'bg-orange-50', 'bg-yellow-50', 'bg-lime-50', 'bg-green-50'];
                      return bg[this.strength] || '';
                  },
                  get strengthText() {
                      const txt = ['', 'text-red-600', 'text-orange-600', 'text-yellow-600', 'text-lime-600', 'text-green-600'];
                      return txt[this.strength] || '';
                  }
              }">
            @csrf

            <!-- Name -->
            <div>
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-5 h-5 text-textGray">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                    </div>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                        autocomplete="name" placeholder="Name"
                        class="w-full rounded-2xl border border-inputBorder bg-white pl-12 pr-5 py-4 text-[15px] font-medium text-textDark placeholder:text-textGray focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all shadow-sm" />
                </div>
                @error('name')
                    <p class="text-sm text-red-600 mt-1 px-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email Address/Phone -->
            <div>
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-5 h-5 text-textGray">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                        </svg>
                    </div>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                        placeholder="Email or phone"
                        class="w-full rounded-2xl border border-inputBorder bg-white pl-12 pr-5 py-4 text-[15px] font-medium text-textDark placeholder:text-textGray focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all shadow-sm" />
                </div>
                @error('email')
                    <p class="text-sm text-red-600 mt-1 px-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-5 h-5 text-textGray">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                    </div>
                    <input id="password" type="password" name="password" required autocomplete="new-password"
                        placeholder="Password"
                        x-model="password"
                        class="w-full rounded-2xl border border-inputBorder bg-white pl-12 pr-5 py-4 text-[15px] font-medium text-textDark placeholder:text-textGray focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all shadow-sm" />
                </div>

                <!-- Strength meter -->
                <div x-show="password.length > 0" x-cloak
                     class="mt-2" :class="strengthBg">
                    <div class="flex gap-1 h-1.5">
                        <template x-for="i in 5" :key="i">
                            <div class="flex-1 rounded-full transition-colors duration-200"
                                 :class="i <= strength ? strengthColor : 'bg-gray-200'"></div>
                        </template>
                    </div>
                    <p class="text-xs mt-1 font-medium" :class="strengthText" x-text="strengthLabel"></p>
                </div>

                @error('password')
                    <p class="text-sm text-red-600 mt-1 px-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-5 h-5 text-textGray">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                    </div>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                        autocomplete="new-password" placeholder="Confirm Password"
                        class="w-full rounded-2xl border border-inputBorder bg-white pl-12 pr-5 py-4 text-[15px] font-medium text-textDark placeholder:text-textGray focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all shadow-sm" />
                </div>
                @error('password_confirmation')
                    <p class="text-sm text-red-600 mt-1 px-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end mt-4">
                <button type="submit"
                    class="bg-primary hover:bg-primaryDark text-white font-semibold rounded-2xl px-6 py-3 flex items-center justify-center gap-2 transition-transform hover:-translate-y-0.5 shadow-md shadow-primary/20">
                    {{ __('Sign up') }}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                        stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </button>
            </div>
        </form>
    </div>
</x-layouts::auth>