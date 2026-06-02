<div style="font-family: 'Cinzel', 'Georgia', serif; line-height: 1.6; color: #e8dcca; background-color: #120e0a; padding: 30px; border: 2px solid #d4af37; border-radius: 8px; max-width: 600px; margin: 0 auto; box-shadow: 0 4px 15px rgba(0,0,0,0.6);">
    <div style="text-align: center; border-bottom: 2px solid #6b1010; padding-bottom: 15px; margin-bottom: 20px;">
        <h1 style="color: #d4af37; margin: 0; font-family: 'Cinzel Decorative', serif; font-size: 24px; letter-spacing: 0.05em; text-transform: uppercase;">
            📖 Biblioteca de Alcantâra
        </h1>
        <p style="color: #8b5a2b; font-style: italic; margin: 5px 0 0 0; font-size: 14px;">
            Saudações, nobre Guardião do Conhecimento
        </p>
    </div>

    <p style="font-size: 16px; margin: 0 0 16px 0;">
        Reparámos que visitou o nosso Grande Acervo de Livros e selecionou alguns tomos sagrados para o seu carrinho de compras, mas a sua jornada foi interrompida antes de selar o pergaminho de aquisição.
    </p>

    <p style="font-size: 16px; margin: 0 0 20px 0; font-weight: bold; color: #d4af37;">
        Deixou os seguintes manuscritos sob o seu carrinho:
    </p>

    <div style="background-color: #1c1815; border: 1px solid rgba(139,90,43,0.3); padding: 15px; border-radius: 6px; margin-bottom: 25px;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid #d4af37; text-align: left;">
                    <th style="padding: 8px 4px; color: #d4af37; font-size: 12px; text-transform: uppercase;">Tomo</th>
                    <th style="padding: 8px 4px; color: #d4af37; font-size: 12px; text-transform: uppercase; text-align: center; width: 60px;">Quant.</th>
                    <th style="padding: 8px 4px; color: #d4af37; font-size: 12px; text-transform: uppercase; text-align: right; width: 80px;">Preço</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr style="border-bottom: 1px solid rgba(139,90,43,0.15);">
                        <td style="padding: 10px 4px; font-size: 14px; font-weight: bold; color: #e8dcca;">
                            {{ $item->livro->nome }}
                            @if($item->livro->isbn)
                                <br><span style="font-size: 11px; color: rgba(232,220,202,0.6); font-weight: normal;">ISBN: {{ $item->livro->isbn }}</span>
                            @endif
                        </td>
                        <td style="padding: 10px 4px; font-size: 14px; text-align: center; color: #e8dcca;">
                            {{ $item->quantidade }}
                        </td>
                        <td style="padding: 10px 4px; font-size: 14px; text-align: right; color: #d4af37; font-weight: bold;">
                            €{{ number_format((float)$item->livro->preco * $item->quantidade, 2, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <p style="font-size: 15px; margin: 0 0 25px 0;">
        Precisas de ajuda com o pagamento, com a entrega ou com a seleção dos manuscritos? O nosso salão está de portas abertas para lhe prestar todo o auxílio e garantir que a sua busca por sabedoria continue tranquila.
    </p>

    <div style="text-align: center; margin-bottom: 30px;">
        <a href="{{ route('carrinho') }}" style="background: linear-gradient(180deg, #8b5a2b, #6b1010); color: #e8dcca; padding: 12px 30px; text-decoration: none; border-radius: 4px; border: 1px solid #d4af37; font-weight: bold; font-family: 'Cinzel', serif; letter-spacing: 0.05em; display: inline-block; text-transform: uppercase; box-shadow: 0 4px 10px rgba(0,0,0,0.5);">
            🏰 Retomar Minha Compra
        </a>
    </div>

    <div style="border-top: 1px solid rgba(139,90,43,0.25); padding-top: 15px; text-align: center; font-size: 11px; color: rgba(232,220,202,0.5);">
        Este lembrete espiritual foi enviado automaticamente pelo escriba-mor da Biblioteca Medieval.
        <br>&copy; 2026 Biblioteca de Alcantâra. Todos os direitos de sabedoria reservados.
    </div>
</div>
