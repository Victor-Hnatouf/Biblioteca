@php
    $livro = $requisicao->livro;
@endphp

<div style="font-family: Arial, sans-serif; line-height: 1.5; color: #111;">
    <h2 style="margin: 0 0 12px 0;">Lembrete de Entrega (amanhã)</h2>

    <p style="margin: 0 0 16px 0;">
        Olá {{ $requisicao->cidadao_nome }}, amanhã é a data prevista de entrega do livro requisitado.
    </p>

    <div style="border: 1px solid #ddd; padding: 12px; border-radius: 8px;">
        <p style="margin: 0 0 6px 0;"><strong>Requisição:</strong> #{{ $requisicao->numero }}</p>
        <p style="margin: 0 0 6px 0;"><strong>Livro:</strong> {{ $livro?->nome ?? '—' }}</p>
        <p style="margin: 0 0 6px 0;"><strong>Requisitado em:</strong> {{ optional($requisicao->requisitado_em)->format('d/m/Y H:i') }}</p>
        <p style="margin: 0;"><strong>Entrega prevista:</strong> {{ optional($requisicao->previsto_entrega_em)->format('d/m/Y') }}</p>
    </div>

    <p style="margin-top: 16px; color: #666; font-size: 12px;">
        Este email foi enviado automaticamente pela aplicação Biblioteca.
    </p>
</div>

