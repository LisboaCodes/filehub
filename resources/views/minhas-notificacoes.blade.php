{{-- IMPORTANTE: Altere 'layouts.app' para o nome do seu arquivo de layout principal do frontend --}}
@extends('layouts.app') 

{{-- Define o título da página que aparecerá na aba do navegador --}}
@section('title', 'Minhas Notificações')

{{-- Define o conteúdo principal da página --}}
@section('content')
<div class="container"> {{-- Use a classe container do seu layout --}}
    
    <h1 class="text-2xl font-bold mb-4">Minhas Notificações</h1>

    @if($notificacoes->isEmpty())
        <div class="p-4 text-center bg-gray-100 dark:bg-gray-800 rounded-lg">
            <p>Você não tem nenhuma notificação no momento.</p>
        </div>
    @else
        <div class="space-y-4">
            {{-- Loop para exibir cada notificação --}}
            @foreach($notificacoes as $notificacao)
                <div class="p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
                    <div class="flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $notificacao->title }}</h2>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $notificacao->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                        {{-- A sintaxe {!! !!} é essencial para renderizar o HTML do RichEditor --}}
                        {!! $notificacao->content !!}
                    </div>

                    {{-- Mostra o link para o anexo, se houver --}}
                    @if($notificacao->attachment_path)
                        <div class="mt-4">
                            <a href="{{ Storage::disk('public')->url($notificacao->attachment_path) }}" 
                               target="_blank" 
                               class="text-sm font-medium text-blue-600 dark:text-blue-500 hover:underline">
                                Baixar Anexo
                            </a>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection