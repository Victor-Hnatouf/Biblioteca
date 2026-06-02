@php
    $isAdmin = auth()->user()?->isAdmin();
    $cartCount = auth()->check()
        ? \App\Models\CarrinhoItem::where('user_id', auth()->id())->sum('quantidade')
        : 0;
@endphp

<x-dropdown align="left" width="60" dropdownClasses="!start-0">
    <x-slot name="trigger">
        <button
            type="button"
            class="btn btn-ghost btn-sm ml-1 border border-[#8b5a2b]/40 text-[#d4af37] hover:bg-[#8b5a2b]/20 font-cinzel"
            aria-label="Abrir menu de navegação"
        >
            ☰ Menu
        </button>
    </x-slot>

    <x-slot name="content">
        <div class="px-3 py-2 text-xs uppercase tracking-widest font-cinzel text-[#8b5a2b]">
            Navegação
        </div>

        <x-dropdown-link href="{{ route('catalogo') }}">📖 Catálogo</x-dropdown-link>
        <x-dropdown-link href="{{ route('requisicoes') }}">📦 Requisições</x-dropdown-link>
        <x-dropdown-link href="{{ route('reviews') }}">⭐ Reviews</x-dropdown-link>
        <x-dropdown-link href="{{ route('carrinho') }}">
            🛒 Carrinho
            @if($cartCount > 0)
                <span class="badge badge-primary badge-xs ml-1">{{ $cartCount }}</span>
            @endif
        </x-dropdown-link>
        <x-dropdown-link href="{{ route('profile.show') }}">⚔️ Perfil</x-dropdown-link>

        @if($isAdmin)
            <div class="border-t border-[#8b5a2b]/30 my-1"></div>
            <div class="px-3 py-2 text-xs uppercase tracking-widest font-cinzel text-[#8b5a2b]">
                Gestão do acervo
            </div>
            <x-dropdown-link href="{{ route('gestao') }}">⚙️ Resumo da gestão</x-dropdown-link>
            <x-dropdown-link href="{{ route('livros') }}">📚 Acervo de livros</x-dropdown-link>
            <x-dropdown-link href="{{ route('autores') }}">✒️ Autores</x-dropdown-link>
            <x-dropdown-link href="{{ route('editoras') }}">🏰 Editoras</x-dropdown-link>
            <x-dropdown-link href="{{ route('admin.encomendas') }}">🛍️ Encomendas</x-dropdown-link>
            <x-dropdown-link href="{{ route('utilizadores') }}">👤 Utilizadores</x-dropdown-link>
        @endif
    </x-slot>
</x-dropdown>
