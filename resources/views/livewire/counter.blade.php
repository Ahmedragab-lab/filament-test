<div>
    <x-filament::dropdown width="xs">
        <x-slot name="trigger">
            <button class="p-2 hover:text-gray-700 focus:outline-none">
                <x-filament::icon alias="filament-settings" icon="heroicon-o-cog-6-tooth" class="w-5 h-5" />
            </button>
        </x-slot>
        <div class="text-xs text-gray-500 m-2">Settings</div>
        <x-filament::dropdown.list>
            <x-filament::dropdown.list.item icon="heroicon-s-adjustments-horizontal"
                wire:click="goToSystemSettingManagement" class="text-xs" icon-size="sm">
                {{ __('System Customization') }}
            </x-filament::dropdown.list.item>

            <x-filament::dropdown.list.item icon="heroicon-o-users" wire:click="goToUserManagement()" class="text-xs"
                icon-size="sm">
                {{ __('User Management') }}
            </x-filament::dropdown.list.item>

            <x-filament::dropdown.list.item icon="heroicon-c-arrows-right-left" wire:click="openDeleteModal"
                class="text-xs" icon-size="sm">
                {{ __('Workflow Management') }}
            </x-filament::dropdown.list.item>
        </x-filament::dropdown.list>
    </x-filament::dropdown>
</div>
