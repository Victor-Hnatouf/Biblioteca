@php
    $livro = $requisicao->livro;
@endphp

<div style="font-family: Arial, sans-serif; line-height: 1.5; color: #111;">
    <h2 style="margin: 0 0 12px 0;">Livro devolvido — falta o teu relatório</h2>

    <p style="margin: 0 0 16px 0;">
        O cidadão indicou que já entregou o exemplar na biblioteca. Regista a condição do livro na área de requisições (separador <strong>Por relatar</strong>).
    </p>

    <div style="border: 1px solid #ddd; padding: 12px; border-radius: 8px;">
        <p style="margin: 0 0 6px 0;"><strong>Requisição:</strong> #{{ $requisicao->numero }}</p>
        <p style="margin: 0 0 6px 0;"><strong>Livro:</strong> {{ $livro?->nome ?? '—' }}</p>
        <p style="margin: 0 0 6px 0;"><strong>Cidadão:</strong> {{ $requisicao->cidadao_nome }} ({{ $requisicao->cidadao_email }})</p>
        <p style="margin: 0 0 6px 0;"><strong>Entrega indicada pelo cidadão:</strong> {{ optional($requisicao->cidadao_entregou_em)->format('d/m/Y H:i') }}</p>
        <p style="margin: 0;"><strong>Requisitado em:</strong> {{ optional($requisicao->requisitado_em)->format('d/m/Y H:i') }}</p>
    </div>

    <p style="margin-top: 16px; color: #666; font-size: 12px;">
        Este email foi enviado automaticamente pela aplicação Biblioteca.
    </p>
</div>
