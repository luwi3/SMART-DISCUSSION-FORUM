<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- START OF NEW CUSTOM FIELDS CARD -->
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('Additional Registration Details') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ __('View your system assigned attributes and custom account criteria.') }}
                            </p>
                        </header>

                        <!-- Registration Number Info -->
                        <div class="mt-6">
                            <x-input-label for="registrationNumber" :value="__('Registration Number')" />
                            <x-text-input id="registrationNumber" type="text" class="mt-1 block w-full bg-gray-100 dark:bg-gray-700 cursor-not-allowed" :value="$user->registrationNumber ?? 'N/A'" disabled />
                        </div>

                        <!-- Course Code Info -->
                        <div class="mt-4">
                            <x-input-label for="courseCode" :value="__('Course Code')" />
                            <x-text-input id="courseCode" type="text" class="mt-1 block w-full bg-gray-100 dark:bg-gray-700 cursor-not-allowed" :value="$user->courseCode ?? 'N/A'" disabled />
                        </div>

                        <!-- Username Info -->
                        <div class="mt-4">
                            <x-input-label for="username" :value="__('Username')" />
                            <x-text-input id="username" type="text" class="mt-1 block w-full bg-gray-100 dark:bg-gray-700 cursor-not-allowed" :value="$user->username" disabled />
                        </div>

                        <!-- Phone Info -->
                        <div class="mt-4">
                            <x-input-label for="phone" :value="__('Phone Number')" />
                            <x-text-input id="phone" type="text" class="mt-1 block w-full bg-gray-100 dark:bg-gray-700 cursor-not-allowed" :value="$user->phone ?? 'N/A'" disabled />
                        </div>

                        <!-- Student Category Info -->
                        <div class="mt-4">
                            <x-input-label for="studentCategory" :value="__('Student Category')" />
                            <x-text-input id="studentCategory" type="text" class="mt-1 block w-full bg-gray-100 dark:bg-gray-700 cursor-not-allowed" :value="$user->studentCategory ?? $user->student_category ?? 'N/A'" disabled />
                        </div>

                        <!-- Role Info -->
                        <div class="mt-4">
                            <x-input-label for="role" :value="__('Account Role')" />
                            <x-text-input id="role" type="text" class="mt-1 block w-full bg-gray-100 dark:bg-gray-700 cursor-not-allowed" :value="ucfirst($user->role)" disabled />
                        </div>
                    </section>
                </div>
            </div>
            <!-- END OF NEW CUSTOM FIELDS CARD -->

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>