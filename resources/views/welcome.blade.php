<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="medieval">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>A Grande Biblioteca Medieval</title>

    <!-- Google Fonts for Medieval Aesthetics -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative:wght@400;700;900&family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        /* Base Styling & Background */
        body {
            background-color: #120e0a;
            /* Using a public domain / high quality Unsplash image for a dark medieval library background */
            background-image: 
                linear-gradient(rgba(18, 14, 10, 0.88), rgba(18, 14, 10, 0.95)),
                url('https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?q=80&w=2070&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: 'Cormorant Garamond', serif;
            color: #e8dcca;
            margin: 0;
            overflow-x: hidden;
        }

        /* Typography */
        .font-cinzel {
            font-family: 'Cinzel Decorative', serif;
        }
        
        .title-medieval {
            font-family: 'Cinzel Decorative', serif;
            text-shadow: 3px 3px 6px rgba(0,0,0,0.9), 0 0 25px rgba(212, 175, 55, 0.3);
            color: #d4af37;
        }

        .parchment-text {
            color: #e8dcca;
            line-height: 1.8;
            font-size: 1.3rem;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.9);
        }

        /* Elaborate Medieval Borders */
        .gothic-border {
            border: 4px solid #d4af37;
            padding: 4px;
            position: relative;
            background: rgba(26, 22, 20, 0.85);
            box-shadow: inset 0 0 30px rgba(0,0,0,0.9), 0 0 20px rgba(212, 175, 55, 0.15);
        }

        .gothic-border::before {
            content: '';
            position: absolute;
            top: -14px; left: -14px; right: -14px; bottom: -14px;
            border: 2px solid #8b5a2b;
            box-shadow: 0 0 10px rgba(0,0,0,0.8);
            pointer-events: none;
        }

        /* Corner Ornaments using SVG data URIs */
        .corner-ornament {
            position: absolute;
            width: 45px;
            height: 45px;
            background-image: url('data:image/svg+xml;utf8,<svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><path d="M0,0 L100,0 L100,15 L15,15 L15,100 L0,100 Z" fill="%23d4af37"/><circle cx="30" cy="30" r="8" fill="%238b5a2b"/></svg>');
            background-size: cover;
            z-index: 10;
        }
        
        .corner-tl { top: -8px; left: -8px; }
        .corner-tr { top: -8px; right: -8px; transform: rotate(90deg); }
        .corner-bl { bottom: -8px; left: -8px; transform: rotate(-90deg); }
        .corner-br { bottom: -8px; right: -8px; transform: rotate(180deg); }

        /* Ornate Divider */
        .divider {
            width: 85%;
            height: 2px;
            background: linear-gradient(to right, transparent, #d4af37, #8b5a2b, #d4af37, transparent);
            margin: 2.5rem auto;
            position: relative;
        }

        .divider::after {
            content: '❖';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #d4af37;
            background: transparent;
            text-shadow: 0 0 10px #000;
            padding: 0 15px;
            font-size: 1.8rem;
        }

        /* Buttons */
        .btn-medieval {
            background: linear-gradient(to bottom, #8b5a2b, #4a0e0e);
            color: #e8dcca;
            border: 2px solid #d4af37;
            font-family: 'Cinzel Decorative', serif;
            text-transform: uppercase;
            padding: 14px 35px;
            font-size: 1.15rem;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 6px 12px rgba(0,0,0,0.7), inset 0 0 12px rgba(0,0,0,0.6);
            text-decoration: none;
            display: inline-block;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .btn-medieval::before {
            content: '';
            position: absolute;
            top: 0; left: -100%; width: 50%; height: 100%;
            background: linear-gradient(to right, rgba(255,255,255,0), rgba(212,175,55,0.3), rgba(255,255,255,0));
            transform: skewX(-25deg);
            transition: all 0.7s ease;
        }

        .btn-medieval:hover::before {
            left: 200%;
        }

        .btn-medieval:hover {
            background: linear-gradient(to bottom, #a36a32, #5a1212);
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.5), inset 0 0 15px rgba(0,0,0,0.4);
            text-shadow: 0 0 8px rgba(212, 175, 55, 0.9);
            transform: translateY(-3px);
            border-color: #fceea7;
        }

        .btn-alt {
            background: linear-gradient(to bottom, #2a221f, #120e0a);
            border-color: #8b5a2b;
            color: #c9bcae;
        }
        
        .btn-alt:hover {
            background: linear-gradient(to bottom, #3a302c, #1a1512);
            border-color: #d4af37;
            color: #e8dcca;
        }

        /* Top Navigation */
        .medieval-nav {
            background: linear-gradient(to bottom, rgba(20, 16, 14, 0.98), rgba(20, 16, 14, 0.85));
            border-bottom: 3px solid #4a0e0e;
            box-shadow: 0 4px 20px rgba(0,0,0,0.9), 0 2px 2px rgba(212, 175, 55, 0.2);
            position: relative;
        }
        
        .medieval-nav::after {
            content: '';
            position: absolute;
            bottom: -3px; left: 0; width: 100%; height: 1px;
            background: #d4af37;
        }

        .nav-link {
            color: #e8dcca;
            font-family: 'Cinzel Decorative', serif;
            text-transform: uppercase;
            font-size: 0.95rem;
            letter-spacing: 1px;
            transition: all 0.3s;
            text-decoration: none;
            padding: 8px 12px;
            border-bottom: 1px solid transparent;
        }

        .nav-link:hover {
            color: #d4af37;
            text-shadow: 0 0 10px rgba(212, 175, 55, 0.5);
            border-bottom: 1px solid #d4af37;
        }

        /* Cards / Info boxes */
        .feature-box {
            padding: 2rem;
            border: 1px solid #8b5a2b;
            background: rgba(18, 14, 10, 0.8);
            box-shadow: inset 0 0 20px rgba(0,0,0,1);
            transition: transform 0.4s ease, border-color 0.4s;
            position: relative;
        }
        
        .feature-box::before {
            content: '';
            position: absolute;
            top: 4px; left: 4px; right: 4px; bottom: 4px;
            border: 1px solid rgba(212, 175, 55, 0.2);
            pointer-events: none;
        }

        .feature-box:hover {
            transform: scale(1.03);
            border-color: #d4af37;
            box-shadow: inset 0 0 20px rgba(0,0,0,1), 0 0 15px rgba(212, 175, 55, 0.15);
        }

        .feature-icon {
            font-size: 3rem;
            color: #d4af37;
            margin-bottom: 1rem;
            text-shadow: 0 0 15px rgba(0,0,0,0.8);
        }
        
        /* Flex & Grid Utilities (Fallback if tailwind isn't built) */
        .flex { display: flex; }
        .flex-col { flex-direction: column; }
        .items-center { align-items: center; }
        .justify-center { justify-content: center; }
        .justify-between { justify-content: space-between; }
        .gap-4 { gap: 1rem; }
        .gap-6 { gap: 1.5rem; }
        .gap-8 { gap: 2rem; }
        .w-full { width: 100%; }
        .max-w-5xl { max-width: 64rem; }
        .mx-auto { margin-left: auto; margin-right: auto; }
        .px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }
        .py-4 { padding-top: 1rem; padding-bottom: 1rem; }
        .p-10 { padding: 2.5rem; }
        .mt-8 { margin-top: 2rem; }
        .mt-12 { margin-top: 3rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mb-6 { margin-bottom: 1.5rem; }
        .mb-10 { margin-bottom: 2.5rem; }
        .text-center { text-align: center; }
        .min-h-screen { min-height: 100vh; }
        .flex-grow { flex-grow: 1; }
        .pt-32 { padding-top: 8rem; }
        .pb-16 { padding-bottom: 4rem; }
        
        @media (min-width: 768px) {
            .md\:flex-row { flex-direction: row; }
            .md\:grid-cols-3 { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .md\:text-7xl { font-size: 4.5rem; line-height: 1; }
            .md\:text-3xl { font-size: 1.875rem; line-height: 2.25rem; }
            .md\:p-16 { padding: 4rem; }
        }
        
        .text-4xl { font-size: 2.25rem; line-height: 2.5rem; }
        .text-2xl { font-size: 1.5rem; line-height: 2rem; }
        .text-xl { font-size: 1.25rem; line-height: 1.75rem; }
        .text-sm { font-size: 0.875rem; line-height: 1.25rem; }
        .opacity-80 { opacity: 0.8; }
        .font-bold { font-weight: 700; }
        .uppercase { text-transform: uppercase; }
        .tracking-wider { letter-spacing: 0.05em; }
        .fixed { position: fixed; }
        .top-0 { top: 0; }
        .z-50 { z-index: 50; }
    </style>
</head>
<body class="min-h-screen flex flex-col">

    <header class="medieval-nav fixed w-full top-0 z-50">
        <div class="max-w-5xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="text-2xl title-medieval font-bold tracking-wider">
                A Grande Biblioteca
            </div>
            
            @if (Route::has('login'))
                <nav class="flex items-center gap-6">
                    <a href="{{ route('catalogo') }}" class="nav-link">Catálogo</a>
                    @auth
                        <a href="{{ url('/dashboard') }}" class="nav-link">Entrar no Castelo</a>
                    @else
                        <a href="{{ route('login') }}" class="nav-link">Identifique-se</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="nav-link">Junte-se à Guilda</a>
                        @endif
                    @endauth
                </nav>
            @endif
        </div>
    </header>

    <main class="flex-grow flex items-center justify-center pt-32 pb-16 px-6">
        <div class="max-w-5xl w-full text-center">
            
            <div class="gothic-border md:p-16 p-10 mt-8">
                <!-- Decorative Corners -->
                <div class="corner-ornament corner-tl"></div>
                <div class="corner-ornament corner-tr"></div>
                <div class="corner-ornament corner-bl"></div>
                <div class="corner-ornament corner-br"></div>

                <h1 class="text-4xl md:text-7xl mb-6 title-medieval uppercase tracking-wider">
                    Scriptorium
                </h1>
                
                <h2 class="text-2xl md:text-3xl font-cinzel mb-4" style="color: #8b5a2b; font-weight: 700; text-shadow: 1px 1px 2px #000;">
                    Repositório de Sabedoria Ancestral
                </h2>

                <div class="divider"></div>

                <p class="parchment-text mb-10 mx-auto" style="max-width: 48rem;">
                    Saudações, viajante! Adentras agora o sacrossanto salão do conhecimento. Aqui, os pergaminhos do passado, os grimórios esquecidos e os tratados arcanos aguardam aqueles dignos de lê-los. Nossa guilda de escribas tem meticulosamente catalogado cada tomo para vossa exploração.
                </p>

                <div class="flex flex-col md:flex-row justify-center gap-6 mt-8">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-medieval">Explorar os Arquivos</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-medieval">Consultar os Registros</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-medieval btn-alt">Solicitar Acesso</a>
                        @endif
                    @endauth
                </div>

                <div class="mt-12 md:grid-cols-3 gap-8" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">
                    <div class="feature-box">
                        <div class="feature-icon">📚</div>
                        <h3 class="font-cinzel text-xl mb-2" style="color: #d4af37;">Tomos Raros</h3>
                        <p class="text-sm opacity-80" style="color: #e8dcca;">Uma coleção inestimável de manuscritos iluminados e livros encadernados em couro de dragão.</p>
                    </div>
                    <div class="feature-box">
                        <div class="feature-icon">📜</div>
                        <h3 class="font-cinzel text-xl mb-2" style="color: #d4af37;">Pergaminhos</h3>
                        <p class="text-sm opacity-80" style="color: #e8dcca;">Registros históricos, mapas de terras longínquas e éditos reais selados com cera.</p>
                    </div>
                    <div class="feature-box">
                        <div class="feature-icon">🕯️</div>
                        <h3 class="font-cinzel text-xl mb-2" style="color: #d4af37;">Arcana Obscura</h3>
                        <p class="text-sm opacity-80" style="color: #e8dcca;">Salas restritas iluminadas apenas por chamas eternas, contendo os mistérios do cosmos.</p>
                    </div>
                </div>
            </div>
            
            <div class="mt-12 font-cinzel text-sm tracking-wider" style="color: #8b5a2b; text-shadow: 1px 1px 2px #000;">
                &copy; {{ date('Y') }} A Guilda dos Escribas. Todos os direitos reservados no Reino.
            </div>
        </div>
    </main>

</body>
</html>
