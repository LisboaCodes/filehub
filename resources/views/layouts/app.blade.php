<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- A variável $config vem do nosso AppServiceProvider --}}
    <title>@yield('title', $config->nome_site ?? 'FileHub')</title>

    {{-- Aqui você pode adicionar seus links de CSS --}}
    <style>
        body { font-family: sans-serif; }
        nav { background: #333; padding: 1rem; }
        nav a { color: white; margin-right: 1rem; text-decoration: none; }
        .container { padding: 2rem; }
    </style>
</head>
<body class="antialiased">

    <header>
        <nav>
            {{-- Renderiza o menu dinâmico que também vem do AppServiceProvider --}}
            @foreach ($menus as $menu_item)
                <a href="{{ $menu_item->url }}">{{ $menu_item->label }}</a>
            @endforeach
            {{-- Links de Login/Logout/Admin --}}
            @auth
                <a href="{{ route('filament.admin.pages.dashboard') }}">Painel</a>
                <form method="POST" action="{{ route('filament.admin.auth.logout') }}" style="display: inline;">
                    @csrf
                    <a href="#" onclick="event.preventDefault(); this.closest('form').submit();">Sair</a>
                </form>
            @else
                <a href="{{ route('filament.admin.auth.login') }}">Login</a>
            @endauth
        </nav>
    </header>

    <main class="container">
        {{-- @yield('content') é o espaço onde o conteúdo de cada página será injetado --}}
        @yield('content')
    </main>

    <footer>
        {{-- Exemplo de como usar dados da configuração global no rodapé --}}
        <p style="text-align: center; margin-top: 2rem; font-size: 0.9em; color: #777;">
            &copy; {{ date('Y') }} {{ $config->nome_site ?? 'FileHub' }}. Todos os direitos reservados.
        </p>
    </footer>

</body>
</html>