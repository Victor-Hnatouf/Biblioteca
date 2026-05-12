@php
    $livro = $requisicao->livro;
    $capaUrl = $livro?->imagem_capa ? url('storage/'.$livro->imagem_capa) : null;
@endphp

<div style="font-family: Arial, sans-serif; line-height: 1.5; color: #111;">
    <h2 style="margin: 0 0 12px 0;">Confirmação de Requisição #{{ $requisicao->numero }}</h2>

    <p style="margin: 0 0 16px 0;">
        A tua requisição foi registada com sucesso.
    </p>

    <div style="border: 1px solid #ddd; padding: 12px; border-radius: 8px;">
        <p style="margin: 0 0 6px 0;"><strong>Livro:</strong> {{ $livro?->nome ?? '—' }}</p>
        <p style="margin: 0 0 6px 0;"><strong>ISBN:</strong> {{ $livro?->isbn ?? '—' }}</p>
        <p style="margin: 0 0 6px 0;"><strong>Cidadão:</strong> {{ $requisicao->cidadao_nome }} ({{ $requisicao->cidadao_email }})</p>
        <p style="margin: 0 0 6px 0;"><strong>Data da requisição:</strong> {{ optional($requisicao->requisitado_em)->format('d/m/Y H:i') }}</p>
        <p style="margin: 0;"><strong>Entrega prevista:</strong> {{ optional($requisicao->previsto_entrega_em)->format('d/m/Y') }}</p>
    </div>

    @if($capaUrl)
        <div style="margin-top: 16px;">
            <p style="margin: 0 0 8px 0;"><strong>Capa:</strong></p>
            <img src="{{ $capaUrl }}" alt="Capa do livro" style="max-width: 220px; border: 1px solid #eee; border-radius: 6px;">
        </div>
    @endif

    <p style="margin-top: 16px; color: #666; font-size: 12px;">
        Este email foi enviado automaticamente pela aplicação Biblioteca.
    </p>
</div>

