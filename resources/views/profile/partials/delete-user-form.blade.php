<section class="space-y-6">
    <header>
        <x-ui.heading level="h2" size="md">{{ __('Delete Account') }}</x-ui.heading>
        <x-ui.text size="sm" class="mt-1">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}</x-ui.text>
    </header>

    <x-ui.button variant="danger"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', { id: 'confirm-user-deletion' })">{{ __('Delete Account') }}</x-ui.button>

    <x-ui.modal name="confirm-user-deletion" width="lg"
        x-init="if ({{ $errors->userDeletion->isNotEmpty() ? 'true' : 'false' }}) { isOpen = true; }">
        <form method="post" action="{{ route('profile.destroy') }}">
            @csrf
            @method('delete')

            <x-ui.heading level="h3" size="sm">{{ __('Are you sure you want to delete your account?') }}</x-ui.heading>

            <x-ui.text size="sm">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}</x-ui.text>

            <div class="mt-6">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1 sr-only">{{ __('Password') }}</label>
                <x-ui.input id="password" name="password" type="password" class="mt-1 block w-3/4" placeholder="{{ __('Password') }}" />
                @error('password', 'userDeletion') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-ui.button variant="outline" x-on:click="$dispatch('close-modal', { id: 'confirm-user-deletion' })">{{ __('Cancel') }}</x-ui.button>
                <x-ui.button type="submit" variant="danger">{{ __('Delete Account') }}</x-ui.button>
            </div>
        </form>
    </x-ui.modal>
</section>
