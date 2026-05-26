@php
    $capaUrl = $livro?->imagem_capa ? url('storage/'.$livro->imagem_capa) : null;
@endphp

<div style="font-family: Arial, sans-serif; line-height: 1.5; color: #111;">
    <h2 style="margin: 0 0 12px 0;">📚 Livro Disponível!</h2>

    <p style="margin: 0 0 16px 0;">
        O livro que solicitaste estar notificado já se encontra disponível para requisição.
    </p>

    <div style="border: 1px solid #ddd; padding: 12px; border-radius: 8px;">
        <p style="margin: 0 0 6px 0;"><strong>Livro:</strong> {{ $livro?->nome ?? '—' }}</p>
        <p style="margin: 0 0 6px 0;"><strong>ISBN:</strong> {{ $livro?->isbn ?? '—' }}</p>
        <p style="margin: 0 0 6px 0;"><strong>Editora:</strong> {{ $livro?->editora?->nome ?? '—' }}</p>
        @if($livro?->bibliografia)
            <p style="margin: 0;"><strong>Descrição:</strong> {{ $livro->bibliografia }}</p>
        @endif
    </div>

    @if($capaUrl)
        <div style="margin-top: 16px;">
            <p style="margin: 0 0 8px 0;"><strong>Capa:</strong></p>
            <img src="{{ $capaUrl }}" alt="Capa do livro" style="max-width: 220px; border: 1px solid #eee; border-radius: 6px;">
        </div>
    @endif

    <div style="margin-top: 16px;">
        <a href="{{ route('catalogo') }}" style="background-color: #2563eb; color: white; padding: 10px 20px; text-decoration: none; border-radius: 6px; display: inline-block;">Ver Catálogo</a>
        <a href="{{ route('requisicoes', ['livro' => $livro->id]) }}" style="background-color: #16a34a; color: white; padding: 10px 20px; text-decoration: none; border-radius: 6px; display: inline-block; margin-left: 8px;">Requisitar Agora</a>
    </div>

    <p style="margin-top: 16px; color: #666; font-size: 12px;">
        Este email foi enviado automaticamente pela aplicação Biblioteca.
    </p>
</div>
