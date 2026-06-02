<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CarrinhoAbandonadoEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $items;

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📖 Guardião, precisas de ajuda com os teus livros?',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.carrinho-abandonado',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
