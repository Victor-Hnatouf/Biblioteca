<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
class LivroDisponivel extends Mailable
{
    use Queueable, SerializesModels;
    public $livro;
    public $alerta;
    public function __construct($livro, $alerta)
    {
        $this->livro = $livro;
        $this->alerta = $alerta;
    }
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Livro Disponível - ' . $this->livro->nome,
        );
    }
    public function content(): Content
    {
        return new Content(
            view: 'emails.livro-disponivel',
        );
    }
    public function attachments(): array
    {
        return [];
    }
}
