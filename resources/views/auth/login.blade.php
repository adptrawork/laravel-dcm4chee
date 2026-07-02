<x-guest-layout>
    @if (session('status'))
        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 text-blue-700 rounded-lg text-sm">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="space-y-4">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Email') }}</label>
                <x-ui.input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="admin@hospital.com" />
                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Password') }}</label>
                <x-ui.input type="password" name="password" required autocomplete="current-password" placeholder="••••••••" :revealable="true" />
                @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">{{ __('Forgot password?') }}</a>
                @endif
            </div>
            <x-ui.button type="submit" variant="primary" class="!w-full !justify-center">{{ __('Log in') }}</x-ui.button>
        </div>
    </form>
</x-guest-layout>
