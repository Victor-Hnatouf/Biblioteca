@php
    $isAdmin = auth()->user()?->isAdmin();
    $cartCount = auth()->check()
        ? \App\Models\CarrinhoItem::where('user_id', auth()->id())->sum('quantidade')
        : 0;

    $linkClass = fn (bool $active) => $active
        ? 'active bg-primary/20 text-[#d4af37] font-semibold'
        : '';
@endphp

<nav class="guardian-sidebar min-h-full w-full p-4 text-base-content">
    <p class="font-cinzel text-xs uppercase tracking-widest text-[#8b5a2b] mb-3 px-2">Navegação</p>

    <ul class="menu menu-sm rounded-box gap-0.5 p-0">
        <li>
            <a href="{{ route('dashboard') }}" class="{{ $linkClass(request()->routeIs('dashboard')) }}">
                📜 Painel
            </a>
        </li>
        <li>
            <a href="{{ route('catalogo') }}" class="{{ $linkClass(request()->routeIs('catalogo')) }}">
                📖 Catálogo Público
            </a>
        </li>
        <li>
            <a href="{{ route('requisicoes') }}" class="{{ $linkClass(request()->routeIs('requisicoes')) }}">
                📦 Requisições
            </a>
        </li>
        <li>
            <a href="{{ route('reviews') }}" class="{{ $linkClass(request()->routeIs('reviews')) }}">
                ⭐ Reviews
            </a>
        </li>
        <li>
            <a href="{{ route('carrinho') }}" class="{{ $linkClass(request()->routeIs('carrinho*')) }}">
                🛒 Carrinho
                @if($cartCount > 0)
                    <span class="badge badge-primary badge-sm">{{ $cartCount }}</span>
                @endif
            </a>
        </li>
        <li>
            <a href="{{ route('profile.show') }}" class="{{ $linkClass(request()->routeIs('profile.show')) }}">
                ⚔️ Perfil
            </a>
        </li>
    </ul>

    @if($isAdmin)
        <p class="font-cinzel text-xs uppercase tracking-widest text-[#8b5a2b] mb-3 mt-6 px-2">Gestão do Acervo</p>
        <ul class="menu menu-sm rounded-box gap-0.5 p-0">
            <li>
                <a href="{{ route('livros') }}" class="{{ $linkClass(request()->routeIs('livros')) }}">
                    📚 Acervo de Livros
                </a>
            </li>
            <li>
                <a href="{{ route('autores') }}" class="{{ $linkClass(request()->routeIs('autores')) }}">
                    ✒️ Escribas & Autores
                </a>
            </li>
            <li>
                <a href="{{ route('editoras') }}" class="{{ $linkClass(request()->routeIs('editoras')) }}">
                    🏰 Casas Editoras
                </a>
            </li>
            <li>
                <a href="{{ route('admin.encomendas') }}" class="{{ $linkClass(request()->routeIs('admin.encomendas')) }}">
                    🛍️ Encomendas
                </a>
            </li>
            <li>
                <a href="{{ route('utilizadores') }}" class="{{ $linkClass(request()->routeIs('utilizadores')) }}">
                    👤 Utilizadores
                </a>
            </li>
        </ul>
    @endif
</nav>
