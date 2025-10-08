<x-guest-layout>

    <h1 class="text-3xl font-bold text-gray-800 text-center mb-1">Sign In</h1>

    <p class="text-sm text-gray-500 text-center mb-6">Silahkan masukkan email dan password untuk masuk!</p>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-4">
            <x-input-label for="email" :value="__('Email')" class="text-sm font-medium text-gray-700" />
            <x-text-input id="email" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400" 
                          type="email" 
                          name="email" 
                          :value="old('email')" 
                          required autofocus autocomplete="username" 
                          placeholder="Masukkan Email" 
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mb-6">
            <x-input-label for="password" :value="__('Password')" class="text-sm font-medium text-gray-700" />
            <x-text-input id="password" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm placeholder-gray-400"
                          type="password"
                          name="password"
                          required autocomplete="current-password"
                          placeholder="Masukkan Password"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        
        <x-primary-button class="w-full justify-center bg-blue-800 hover:#001A6E focus:ring-blue-500 py-2.5">
            {{ __('Sign In') }}
        </x-primary-button>

        <div class="flex items-center justify-center mt-4">
            <span class="text-sm text-gray-600">
                Belum memiliki akun? 
                @if (Route::has('register'))
                    <a class="text-blue-800 hover:text-blue-900 font-semibold" href="{{ route('register') }}">
                        Sign Up
                    </a>
                @endif
            </span>
        </div>
    </form>
</x-guest-layout>