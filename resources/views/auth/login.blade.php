<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Login Title: visible on all devices -->
    <h2 class="text-xl sm:text-2xl font-semibold text-gray-800 text-center mb-6 sm:mb-8">
        {{ __('Welcome Back') }}
    </h2>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-sm sm:text-base" />
            <x-text-input 
                id="email" 
                class="block mt-1 w-full text-base sm:text-lg py-2.5 sm:py-3" 
                type="email" 
                name="email" 
                :value="old('email')" 
                required 
                autofocus 
                autocomplete="username" 
                placeholder="Enter your email"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4 sm:mt-5">
            <x-input-label for="password" :value="__('Password')" class="text-sm sm:text-base" />
            <x-text-input 
                id="password" 
                class="block mt-1 w-full text-base sm:text-lg py-2.5 sm:py-3"
                type="password"
                name="password"
                required 
                autocomplete="current-password"
                placeholder="Enter your password"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Login Button -->
        <div class="mt-6 sm:mt-8">
            <x-primary-button class="w-full justify-center py-3 sm:py-3.5 text-base sm:text-lg font-medium">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
