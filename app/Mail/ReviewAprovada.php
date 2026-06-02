<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReviewAprovada extends Mailable
{
    use Queueable, SerializesModels;

    public $review;

    public function __construct($review)
    {
        $this->review = $review;
    }

    
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Review Aprovada - ' . $this->review->livro->nome,
        );
    }

    
    public function content(): Content
    {
        return new Content(
            view: 'emails.review-aprovada',
        );
    }

    
    public function attachments(): array
    {
        return [];
    }
}
