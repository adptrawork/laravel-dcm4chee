<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">{{ __('This is a secure area. Please confirm your password before continuing.') }}</div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf
        <div class="space-y-4">
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Password') }}</label>
                <x-ui.input type="password" name="password" required autocomplete="current-password" placeholder="••••••••" :revealable="true" />
                @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <x-ui.button type="submit" variant="primary" class="!w-full !justify-center">{{ __('Confirm') }}</x-ui.button>
        </div>
    </form>
</x-guest-layout>
