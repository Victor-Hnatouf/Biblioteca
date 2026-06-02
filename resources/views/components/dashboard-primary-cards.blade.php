@php
    $isAdmin = auth()->user()?->isAdmin();
@endphp

<div class="p-6 lg:p-8 bg-base-200 border-b border-base-300 relative overflow-hidden">
    <div class="gothic-corner gothic-corner-tl"></div>
    <div class="gothic-corner gothic-corner-tr"></div>
    <div class="gothic-corner gothic-corner-bl"></div>
    <div class="gothic-corner gothic-corner-br"></div>

    <div class="text-center mb-2">
        <x-application-logo class="block h-16 w-auto mx-auto" />
    </div>

    <h1 class="mt-6 text-3xl font-medium text-center candle-glow" style="font-family: 'Cinzel Decorative', serif; color: #d4af37; text-shadow: 2px 2px 6px rgba(0,0,0,0.8), 0 0 20px rgba(212,175,55,0.15);">
        Bem-vindo ao Scriptorium
    </h1>

    <div class="ornate-divider mt-6"></div>

    <p class="mt-4 text-center leading-relaxed" style="font-family: 'Cormorant Garamond', serif; font-size: 1.2rem; color: #c9bcae; max-width: 42rem; margin: 0 auto; text-shadow: 1px 1px 2px rgba(0,0,0,0.6);">
        @if($isAdmin)
            Os portões do conhecimento estão sob a tua guarda. Gere o acervo, acompanha as encomendas dos cidadãos e mantém o scriptorium em ordem.
        @else
            Explora o catálogo, requisita tomos, deixa a tua opinião e adquire obras para a tua coleção pessoal.
        @endif
    </p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 p-6 lg:p-8" style="background: rgba(18,14,10,0.4);">
    <x-dashboard-card
        icon="📖"
        title="Catálogo de Livros"
        :href="route('catalogo')"
        description="Explora o acervo público, consulta detalhes e adiciona volumes ao teu carrinho de compras."
    />

    <x-dashboard-card
        icon="🛒"
        title="Carrinho de Compras"
        :href="route('carrinho')"
        description="Revisa os tomos selecionados, indica a morada de entrega e conclui o pagamento em segurança."
    />

    <x-dashboard-card
        icon="📦"
        title="Requisições"
        :href="route('requisicoes')"
        description="Requisita e devolve livros da biblioteca, acompanhando o estado de cada empréstimo."
    />

    <x-dashboard-card
        icon="⭐"
        title="Reviews"
        :href="route('reviews')"
        description="Partilha a tua opinião sobre as obras que leste e lê as avaliações de outros guardiões."
    />

    @if($isAdmin)
        <x-dashboard-card
            icon="🛍️"
            title="Encomendas"
            :href="route('admin.encomendas')"
            description="Consulta encomendas pagas e pendentes, com moradas e detalhes de cada compra."
        />

        <x-dashboard-card
            icon="📚"
            title="Acervo de Livros"
            :href="route('livros')"
            description="Regista, edita e exporta os tomos guardados nas prateleiras do scriptorium."
        />
    @endif
</div>
