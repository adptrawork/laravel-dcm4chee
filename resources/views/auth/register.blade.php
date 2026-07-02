<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="space-y-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Name') }}</label>
                <x-ui.input type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Your full name" />
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Email') }}</label>
                <x-ui.input type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="email@hospital.com" />
                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Password') }}</label>
                <x-ui.input type="password" name="password" required autocomplete="new-password" placeholder="••••••••" :revealable="true" />
                @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Confirm Password') }}</label>
                <x-ui.input type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" :revealable="true" />
                @error('password_confirmation') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">{{ __('Already registered?') }}</a>
                <x-ui.button type="submit" variant="primary">{{ __('Register') }}</x-ui.button>
            </div>
        </div>
    </form>
</x-guest-layout>
