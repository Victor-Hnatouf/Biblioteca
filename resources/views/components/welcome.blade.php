<div class="p-6 lg:p-8 bg-base-200 border-b border-base-300 relative" style="overflow: hidden;">
    {{-- Decorative corner ornaments --}}
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
        Os portões do conhecimento abrem-se perante ti, guardião. Neste salão sagrado repousam os registos de todos os tomos, escribas e casas editoras do nosso reino. Que a tua busca pelo saber seja frutífera e iluminada pelas chamas eternas da sabedoria.
    </p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 p-6 lg:p-8" style="background: rgba(18,14,10,0.4);">
    {{-- Acervo de Livros --}}
    <div class="p-5 rounded-lg relative" style="background: rgba(26,22,20,0.7); border: 1px solid rgba(139,90,43,0.2); transition: all 0.3s ease;" onmouseover="this.style.borderColor='rgba(212,175,55,0.4)'; this.style.boxShadow='inset 0 0 20px rgba(0,0,0,0.3), 0 0 8px rgba(212,175,55,0.06)';" onmouseout="this.style.borderColor='rgba(139,90,43,0.2)'; this.style.boxShadow='none';">
        <div class="flex items-center">
            <span style="font-size: 2rem; filter: drop-shadow(0 0 4px rgba(0,0,0,0.6));">📚</span>
            <h2 class="ms-3 text-xl font-semibold" style="font-family: 'Cinzel', serif; color: #d4af37;">
                <a href="{{ route('livros') }}">Acervo de Livros</a>
            </h2>
        </div>

        <p class="mt-4 text-sm leading-relaxed" style="color: #c9bcae; font-family: 'Cormorant Garamond', serif; font-size: 1.05rem;">
            Explora a vasta colecção de tomos e grimórios que se encontram guardados nas nossas prateleiras encantadas. Cada obra foi cuidadosamente catalogada pelos escribas da guilda.
        </p>

        <p class="mt-4 text-sm">
            <a href="{{ route('livros') }}" class="inline-flex items-center font-semibold" style="color: #d4af37; font-family: 'Cinzel', serif; font-size: 0.85rem; letter-spacing: 0.04em;">
                Consultar o Acervo →
            </a>
        </p>
    </div>

    {{-- Escribas & Autores --}}
    <div class="p-5 rounded-lg relative" style="background: rgba(26,22,20,0.7); border: 1px solid rgba(139,90,43,0.2); transition: all 0.3s ease;" onmouseover="this.style.borderColor='rgba(212,175,55,0.4)'; this.style.boxShadow='inset 0 0 20px rgba(0,0,0,0.3), 0 0 8px rgba(212,175,55,0.06)';" onmouseout="this.style.borderColor='rgba(139,90,43,0.2)'; this.style.boxShadow='none';">
        <div class="flex items-center">
            <span style="font-size: 2rem; filter: drop-shadow(0 0 4px rgba(0,0,0,0.6));">✒️</span>
            <h2 class="ms-3 text-xl font-semibold" style="font-family: 'Cinzel', serif; color: #d4af37;">
                <a href="{{ route('autores') }}">Escribas & Autores</a>
            </h2>
        </div>

        <p class="mt-4 text-sm leading-relaxed" style="color: #c9bcae; font-family: 'Cormorant Garamond', serif; font-size: 1.05rem;">
            Conhece os mestres da escrita cujas palavras deram vida a mundos inteiros. Cada autor é um farol de sabedoria que ilumina as trevas da ignorância.
        </p>

        <p class="mt-4 text-sm">
            <a href="{{ route('autores') }}" class="inline-flex items-center font-semibold" style="color: #d4af37; font-family: 'Cinzel', serif; font-size: 0.85rem; letter-spacing: 0.04em;">
                Ver os Escribas →
            </a>
        </p>
    </div>

    {{-- Casas Editoras --}}
    <div class="p-5 rounded-lg relative" style="background: rgba(26,22,20,0.7); border: 1px solid rgba(139,90,43,0.2); transition: all 0.3s ease;" onmouseover="this.style.borderColor='rgba(212,175,55,0.4)'; this.style.boxShadow='inset 0 0 20px rgba(0,0,0,0.3), 0 0 8px rgba(212,175,55,0.06)';" onmouseout="this.style.borderColor='rgba(139,90,43,0.2)'; this.style.boxShadow='none';">
        <div class="flex items-center">
            <span style="font-size: 2rem; filter: drop-shadow(0 0 4px rgba(0,0,0,0.6));">🏰</span>
            <h2 class="ms-3 text-xl font-semibold" style="font-family: 'Cinzel', serif; color: #d4af37;">
                <a href="{{ route('editoras') }}">Casas Editoras</a>
            </h2>
        </div>

        <p class="mt-4 text-sm leading-relaxed" style="color: #c9bcae; font-family: 'Cormorant Garamond', serif; font-size: 1.05rem;">
            As nobres casas que forjaram e trouxeram ao mundo as obras que preenchem esta biblioteca. Cada editora carrega consigo um legado de excelência.
        </p>

        <p class="mt-4 text-sm">
            <a href="{{ route('editoras') }}" class="inline-flex items-center font-semibold" style="color: #d4af37; font-family: 'Cinzel', serif; font-size: 0.85rem; letter-spacing: 0.04em;">
                Explorar as Casas →
            </a>
        </p>
    </div>

    {{-- O Teu Perfil --}}
    <div class="p-5 rounded-lg relative" style="background: rgba(26,22,20,0.7); border: 1px solid rgba(139,90,43,0.2); transition: all 0.3s ease;" onmouseover="this.style.borderColor='rgba(212,175,55,0.4)'; this.style.boxShadow='inset 0 0 20px rgba(0,0,0,0.3), 0 0 8px rgba(212,175,55,0.06)';" onmouseout="this.style.borderColor='rgba(139,90,43,0.2)'; this.style.boxShadow='none';">
        <div class="flex items-center">
            <span style="font-size: 2rem; filter: drop-shadow(0 0 4px rgba(0,0,0,0.6));">⚔️</span>
            <h2 class="ms-3 text-xl font-semibold" style="font-family: 'Cinzel', serif; color: #d4af37;">
                <a href="{{ route('profile.show') }}">Perfil do Guardião</a>
            </h2>
        </div>

        <p class="mt-4 text-sm leading-relaxed" style="color: #c9bcae; font-family: 'Cormorant Garamond', serif; font-size: 1.05rem;">
            Gere a tua identidade dentro dos muros desta fortaleza do saber. Atualiza as tuas credenciais e personaliza o teu brasão de guardião.
        </p>

        <p class="mt-4 text-sm">
            <a href="{{ route('profile.show') }}" class="inline-flex items-center font-semibold" style="color: #d4af37; font-family: 'Cinzel', serif; font-size: 0.85rem; letter-spacing: 0.04em;">
                Editar Perfil →
            </a>
        </p>
    </div>
</div>
