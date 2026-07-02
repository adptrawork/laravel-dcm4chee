<section>
    <header>
        <x-ui.heading level="h2" size="md">{{ __('Update Password') }}</x-ui.heading>
        <x-ui.text size="sm" class="mt-1">{{ __('Ensure your account is using a long, random password to stay secure.') }}</x-ui.text>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-4">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Current Password') }}</label>
            <x-ui.input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" :revealable="true" />
            @error('current_password', 'updatePassword') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="update_password_password" class="block text-sm font-medium text-gray-700 mb-1">{{ __('New Password') }}</label>
            <x-ui.input id="update_password_password" name="password" type="password" autocomplete="new-password" :revealable="true" />
            @error('password', 'updatePassword') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Confirm Password') }}</label>
            <x-ui.input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" :revealable="true" />
            @error('password_confirmation', 'updatePassword') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center gap-4">
            <x-ui.button type="submit" variant="primary">{{ __('Save') }}</x-ui.button>
            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-green-600">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
