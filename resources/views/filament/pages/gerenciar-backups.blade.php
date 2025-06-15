<x-filament-panels::page>
    <x-filament::section>
        <div class="relative overflow-x-auto">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">Backup</th>
                        <th scope="col" class="px-6 py-3">Data</th>
                        <th scope="col" class="px-6 py-3">Tamanho</th>
                        <th scope="col" class="px-6 py-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->getBackups() as $backup)
                        <tr
                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $backup['label'] }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $backup['date'] }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $backup['size'] }}
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                @if (auth()->id() === 1)
                                    {{-- Download --}}
                                    <x-filament::button
                                        size="sm"
                                        icon="heroicon-o-arrow-down-tray"
                                        x-data="{}"
                                        x-on:click.prevent="
                                            const pin = prompt('Para fazer o download, digite seu PIN de segurança:');
                                            if (pin) $wire.downloadBackup('{{ $backup['filename'] }}', pin);
                                        ">
                                        Download
                                    </x-filament::button>

                                    {{-- Restaurar --}}
                                    <x-filament::button
                                        size="sm"
                                        icon="heroicon-o-arrow-path"
                                        color="warning"
                                        x-data="{}"
                                        x-on:click.prevent="
                                            const pin = prompt('CERTEZA ABSOLUTA? Para confirmar a restauração, digite seu PIN:');
                                            if (pin) $wire.restoreBackup('{{ $backup['filename'] }}', pin);
                                        ">
                                        Restaurar
                                    </x-filament::button>

                                    {{-- Excluir --}}
                                    <x-filament::button
                                        size="sm"
                                        icon="heroicon-o-trash"
                                        color="danger"
                                        x-data="{}"
                                        x-on:click.prevent="
                                            const pin = prompt('Para EXCLUIR este backup, digite seu PIN de segurança:');
                                            if (pin && confirm('Esta ação é permanente. Tem certeza?'))
                                                $wire.deleteBackup('{{ $backup['filename'] }}', pin);
                                        ">
                                        Excluir
                                    </x-filament::button>
                                @else
                                    <span class="text-gray-400">Apenas Admin Principal</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td colspan="4" class="px-6 py-4 text-center">
                                Nenhum backup encontrado. Clique em
                                <strong>"Gerar Novo Backup"</strong> para começar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-panels::page>
