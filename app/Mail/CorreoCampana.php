<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class CorreoCampana extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $asunto,
        public readonly string $cuerpoHtml,
        public readonly int $correoId = 0,
    ) {}

    public function headers(): Headers
    {
        return new Headers(
            messageId: 'correo-' . $this->correoId . '@berzoti-crm',
        );
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->asunto);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.correo-campana');
    }

    public function attachments(): array
    {
        return [];
    }
}
