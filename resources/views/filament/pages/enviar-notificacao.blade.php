<x-filament-panels::page>
    <form wire:submit="send">
        {{-- O Filament renderiza o formulário que definimos na classe PHP --}}
        {{ $this->form }}

        {{-- Renderiza o botão "Enviar" que definimos na classe PHP --}}
        <x-filament-panels::form.actions 
            :actions="$this->getFormActions()"
        /> 
    </form>
</x-filament-panels::page>