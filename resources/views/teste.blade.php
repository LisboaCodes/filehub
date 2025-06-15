<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Página de Teste - {{ $config->nome_site }}</title>
    <style>
        body { font-family: sans-serif; padding: 2rem; line-height: 1.6; }
        img { border: 1px solid #ddd; padding: 5px; max-height: 80px; background: #f9f9f9; }
        h2 { border-bottom: 1px solid #eee; padding-bottom: 5px; margin-top: 2rem;}
        p { white-space: pre-wrap; }
        nav ul { list-style: none; padding: 0; display: flex; gap: 1rem; background: #333; padding: 1rem; border-radius: 5px;}
        nav a { color: white; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

    <h1>Teste de Configurações Globais</h1>

    {{-- =============================================== --}}
    {{--          MENU DINÂMICO ADICIONADO AQUI          --}}
    {{-- =============================================== --}}
    <nav>
        <ul>
            @if($menus->isNotEmpty())
                @foreach ($menus as $menu_item)
                    <li>
                        <a href="{{ $menu_item->url }}">{{ $menu_item->label }}</a>
                    </li>
                @endforeach
            @else
                <li>Nenhum item de menu cadastrado.</li>
            @endif
        </ul>
    </nav>
    
    <hr>

    <h2>Nome do Site:</h2>
    <p>{{ $config->nome_site }}</p>

    {{-- O resto do código continua igual... --}}
    <h2>Logo da Navbar:</h2>
    @if($config->logo_navbar && isset($config->logo_navbar[0]))
        <img src="{{ Storage::disk('public')->url($config->logo_navbar[0]) }}" alt="Logo da Navbar">
    @else
        <p>Nenhum logo da navbar cadastrado.</p>
    @endif

    <h2>Logo do Rodapé:</h2>
    @if($config->logo_footer && isset($config->logo_footer[0]))
        <img src="{{ Storage::disk('public')->url($config->logo_footer[0]) }}" alt="Logo do Rodapé">
    @else
        <p>Nenhum logo do rodapé cadastrado.</p>
    @endif

    <h2>Informações do Rodapé:</h2>
    <p>{!! nl2br(e($config->info_footer)) !!}</p>

</body>
</html>