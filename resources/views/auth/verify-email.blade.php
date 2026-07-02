<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">{{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}</div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ __('A new verification link has been sent to the email address you provided.') }}</div>
    @endif

    <div class="flex items-center justify-between gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-ui.button type="submit" variant="primary">{{ __('Resend Verification Email') }}</x-ui.button>
        </form>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-ui.button type="submit" variant="ghost">{{ __('Log Out') }}</x-ui.button>
        </form>
    </div>
</x-guest-layout>
