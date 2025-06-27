<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <p class="text-6xl font-bold font-oswald" style="padding-bottom: 38px;">SIGN UP</p>
        </x-slot>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div>
                <x-label for="name" value="{{ __('USERNAME') }}" />
                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <x-label for="email" value="{{ __('EMAIL') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('PASSWORD') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="{{ __('CONFIRM PASSWORD') }}" />
                <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required />

                            <div class="ms-2">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Terms of Service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="mt-10">
                <x-button class="w-full items-center justify-center font-oswald text-lg">
                    {{ __('SIGN UP') }}
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
                <p class="font-oswald text-xs">ALREADY HAVE ACCOUNT? <a href="{{ route('login') }}" class="text-blue-500 hover:underline">SIGN IN</a> </p>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
