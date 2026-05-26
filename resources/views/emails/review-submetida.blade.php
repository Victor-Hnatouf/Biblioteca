@php
    $livro = $review->livro;
    $capaUrl = $livro?->imagem_capa ? url('storage/'.$livro->imagem_capa) : null;
@endphp

<div style="font-family: Arial, sans-serif; line-height: 1.5; color: #111;">
    <h2 style="margin: 0 0 12px 0;">Nova Review Submetida</h2>

    <p style="margin: 0 0 16px 0;">
        Um cidadão submeteu uma nova review que requer a tua aprovação.
    </p>

    <div style="border: 1px solid #ddd; padding: 12px; border-radius: 8px;">
        <p style="margin: 0 0 6px 0;"><strong>Livro:</strong> {{ $livro?->nome ?? '—' }}</p>
        <p style="margin: 0 0 6px 0;"><strong>ISBN:</strong> {{ $livro?->isbn ?? '—' }}</p>
        <p style="margin: 0 0 6px 0;"><strong>Cidadão:</strong> {{ $review->cidadao_nome }} ({{ $review->cidadao_email }})</p>
        <p style="margin: 0 0 6px 0;"><strong>Classificação:</strong> {{ $review->classificacao }}/5</p>
        <p style="margin: 0 0 6px 0;"><strong>Comentário:</strong></p>
        <p style="margin: 0 0 6px 0; font-style: italic;">{{ $review->comentario }}</p>
        <p style="margin: 0;"><strong>Estado:</strong> {{ ucfirst($review->estado) }}</p>
    </div>

    @if($capaUrl)
        <div style="margin-top: 16px;">
            <p style="margin: 0 0 8px 0;"><strong>Capa:</strong></p>
            <img src="{{ $capaUrl }}" alt="Capa do livro" style="max-width: 220px; border: 1px solid #eee; border-radius: 6px;">
        </div>
    @endif

    <div style="margin-top: 16px;">
        <a href="{{ route('reviews') }}" style="background-color: #2563eb; color: white; padding: 10px 20px; text-decoration: none; border-radius: 6px; display: inline-block;">Gerir Reviews</a>
    </div>

    <p style="margin-top: 16px; color: #666; font-size: 12px;">
        Este email foi enviado automaticamente pela aplicação Biblioteca.
    </p>
</div>
