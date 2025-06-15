{{-- O ideal é você estender o seu layout principal aqui --}}
{{-- Exemplo: @extends('layouts.app') --}}
{{-- Se você fizer isso, só precisará das seções de 'title' e 'content' --}}

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    {{-- O título da aba do navegador será o título da sua página --}}
    <title>{{ $pagina->title }} - {{ $config->nome_site ?? 'FileHub' }}</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 800px; margin: 2rem auto; padding: 0 1rem; }
        .page-content img { max-width: 100%; height: auto; }
        .unpublished-warning { padding: 1rem; background: #fffbe6; border: 1px solid #ffe58f; text-align: center; margin-bottom: 2rem; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        {{-- Exibe um aviso se a página não estiver publicada (visível apenas para admins) --}}
        @if(!$pagina->is_published)
            <div class="unpublished-warning">
                <strong>Atenção:</strong> Você está visualizando uma página não publicada.
            </div>
        @endif

        <h1>{{ $pagina->title }}</h1>
        <hr>

        <div class="page-content">
            {{-- A expressão {!! ... !!} é ESSENCIAL para renderizar o HTML gerado pelo RichEditor do Filament --}}
            {!! $pagina->content !!}
        </div>
    </div>
</body>
</html>