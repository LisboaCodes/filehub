<div class="space-y-4 px-4 py-2">
    <h3 class="text-lg font-medium dark:text-gray-100">Backups disponíveis</h3>

    @if (count($backups))
        <ul class="space-y-2">
            @foreach ($backups as $file)
                <li class="flex items-center justify-between">
                    <span>{{ $file }}</span>
                    <div class="space-x-2">
                        <button
                            wire:click="restoreBackup('{{ $file }}')"
                            class="text-sm hover:underline"
                        >🔄 Restaurar</button>
                        <button
                            wire:click="deleteBackup('{{ $file }}')"
                            class="text-sm text-red-500 hover:underline"
                        >❌ Excluir</button>
                    </div>
                </li>
            @endforeach
        </ul>
    @else
        <p class="text-sm text-gray-500 dark:text-gray-400">Nenhum backup encontrado.</p>
    @endif
</div>
