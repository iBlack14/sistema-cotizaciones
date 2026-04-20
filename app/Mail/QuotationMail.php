<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuotationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $quotation;

    public $pdfPath;

    public $body;

    /**
     * Create a new message instance.
     */
    public function __construct($quotation, $pdfPath, $body = null)
    {
        $this->quotation = $quotation;
        $this->pdfPath = $pdfPath;
        $this->body = $body;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Cotización - '.($this->quotation->client_company ?? $this->quotation->client_name),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.quotation',
            with: ['body' => $this->body],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->pdfPath)
                ->as('Cotizacion-'.$this->quotation->id.'.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
