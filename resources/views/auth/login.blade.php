<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <p class="text-6xl font-bold font-oswald" style="padding-bottom: 85px;">SIGN IN</p>
        </x-slot>

        <x-validation-errors class="mb-4" />

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-label for="email" value="{{ __('EMAIL') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                    required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('PASSWORD') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required
                    autocomplete="current-password" />
            </div>

            <div class="flex items-center block mt-4 justify-between">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ms-2 font-oswald text-sm">{{ __('Remember me') }}</span>
                </label>
                @if (Route::has('password.request'))
                    <a class="font-oswald underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>

            <div class="mt-10">
                <x-button class="w-full items-center justify-center font-oswald text-lg">
                    {{ __('SIGN IN') }}
                </x-button>
            </div>
            <div class="mt-4">
                <a href="{{ route('google.redirect') }}"
                    class="flex items-center justify-center w-full py-[5px] border border-[#F91118] rounded-md bg-white shadow-sm hover:bg-gray-100">
                    <img src="images/icons/google.svg" alt="Google Icon"
                        class="w-5 h-5 mr-5">
                    <span class="text-[#F91118] font-bold text-lg uppercase font-oswald">Sign In with Google</span>
                </a>
            </div>
            <div class="mt-4 text-center">
                <p class="font-oswald text-xs">DONT HAVE AN ACCOUNT? <a href="{{ route('register') }}"
                        class="text-blue-500 hover:underline">SIGN UP</a> </p>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
