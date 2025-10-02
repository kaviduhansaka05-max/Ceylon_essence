<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900 leading-tight">
            {{ __('My Profile') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Profile Information --}}
            @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                <div class="bg-white shadow rounded-2xl p-6 ring-1 ring-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Profile Information</h3>
                    <p class="text-sm text-gray-600 mb-5">
                        Update your account’s profile information and email address.
                    </p>
                    @livewire('profile.update-profile-information-form')
                </div>
            @endif

            {{-- Update Password --}}
            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                <div class="bg-white shadow rounded-2xl p-6 ring-1 ring-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Update Password</h3>
                    <p class="text-sm text-gray-600 mb-5">
                        Ensure your account is using a secure password.
                    </p>
                    @livewire('profile.update-password-form')
                </div>
            @endif

            {{-- Two Factor Authentication --}}
            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <div class="bg-white shadow rounded-2xl p-6 ring-1 ring-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Two Factor Authentication</h3>
                    <p class="text-sm text-gray-600 mb-5">
                        Add an extra layer of security to your account.
                    </p>
                    @livewire('profile.two-factor-authentication-form')
                </div>
            @endif

            {{-- Browser Sessions --}}
            <div class="bg-white shadow rounded-2xl p-6 ring-1 ring-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Browser Sessions</h3>
                <p class="text-sm text-gray-600 mb-5">
                    Manage and log out of your active sessions on other browsers and devices.
                </p>
                @livewire('profile.logout-other-browser-sessions-form')
            </div>

            {{-- Delete Account --}}
            @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                <div class="bg-white shadow rounded-2xl p-6 ring-1 ring-rose-200">
                    <h3 class="text-lg font-semibold text-rose-700 mb-1">Delete Account</h3>
                    <p class="text-sm text-gray-600 mb-5">
                        Permanently delete your account and all associated data.
                    </p>
                    @livewire('profile.delete-user-form')
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
