<x-filament-panels::page>
    @if(session()->has('message'))
        hi
    @endif
    <p class="text-xs text-gray-400">
        {{ __("Customize your system's appearance, branding and localization to reflect your orginzation's identity.") }}
    </p>
     {{-- @livewire('counter') --}}
     {{ $this->form }}
</x-filament-panels::page>
