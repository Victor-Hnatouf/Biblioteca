<?php

namespace App\Mail;

use App\Models\Requisicao;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RequisicaoPendenteRelatorio extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Requisicao $requisicao)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Devolução na biblioteca — requisição #' . $this->requisicao->numero . ' por relatar',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.requisicao-pendente-relatorio',
            with: [
                'requisicao' => $this->requisicao->loadMissing(['livro', 'cidadao']),
            ],
        );
    }
}
