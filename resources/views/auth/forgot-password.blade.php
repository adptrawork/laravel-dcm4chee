<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">{{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link.') }}</div>

    @if (session('status'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="space-y-4">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Email') }}</label>
                <x-ui.input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="your@email.com" />
                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <x-ui.button type="submit" variant="primary" class="!w-full !justify-center">{{ __('Email Password Reset Link') }}</x-ui.button>
        </div>
    </form>
</x-guest-layout>
