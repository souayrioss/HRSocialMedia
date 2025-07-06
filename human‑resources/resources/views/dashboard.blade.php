<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-2xl mx-auto mt-8">
        <h2 class="text-2xl font-bold mb-4">CivilEngineerConnect</h2>
        <div class="bg-white shadow rounded-lg p-6">
            <p>Welcome to CivilEngineerConnect! Use the navigation bar to access your feed, friends, AI prompts, and profile.</p>
        </div>
    </div>
</x-app-layout>
