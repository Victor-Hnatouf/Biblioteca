<?php

namespace App\Mail;

use App\Models\Requisicao;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RequisicaoConfirmacao extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Requisicao $requisicao)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmação de Requisição #' . $this->requisicao->numero,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.requisicao-confirmacao',
            with: [
                'requisicao' => $this->requisicao->loadMissing(['livro.editora', 'livro.autores', 'cidadao']),
            ],
        );
    }
}

