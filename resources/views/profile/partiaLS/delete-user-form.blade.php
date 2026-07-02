<section class="space-y-6">
    <header>
        <x-ui.heading level="h2" size="md">{{ __('Delete Account') }}</x-ui.heading>
        <x-ui.text size="sm" class="mt-1">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}</x-ui.text>
    </header>

    <x-ui.button variant="danger" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">{{ __('Delete Account') }}</x-ui.button>

    <x-ui.modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <x-ui.heading level="h2" size="md" class="mb-2">{{ __('Are you sure you want to delete your account?') }}</x-ui.heading>
            <x-ui.text size="sm">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm.') }}</x-ui.text>

            <div class="mt-4">
                <label for="password" class="sr-only">{{ __('Password') }}</label>
                <x-ui.input id="password" name="password" type="password" class="w-3/4" placeholder="{{ __('Password') }}" />
                @error('password', 'userDeletion') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-ui.button type="button" variant="outline" x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-ui.button>
                <x-ui.button type="submit" variant="danger">{{ __('Delete Account') }}</x-ui.button>
            </div>
        </form>
    </x-ui.modal>
</section>
