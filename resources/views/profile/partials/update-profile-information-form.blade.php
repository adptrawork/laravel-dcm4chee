<section>
    <header>
        <x-ui.heading level="h2" size="md">{{ __('Profile Information') }}</x-ui.heading>
        <x-ui.text size="sm" class="mt-1">{{ __("Update your account's profile information and email address.") }}</x-ui.text>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-4">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Name') }}</label>
            <x-ui.input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Email') }}</label>
            <x-ui.input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username" />
            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2">
                    <x-ui.text size="sm">{{ __('Your email address is unverified.') }}
                        <button form="send-verification" class="text-blue-600 hover:text-blue-800 underline">{{ __('Click here to re-send the verification email.') }}</button>
                    </x-ui.text>
                    @if (session('status') === 'verification-link-sent')
                        <x-ui.text size="sm" class="text-green-600">{{ __('A new verification link has been sent to your email address.') }}</x-ui.text>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-ui.button type="submit" variant="primary">{{ __('Save') }}</x-ui.button>
            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-green-600">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
