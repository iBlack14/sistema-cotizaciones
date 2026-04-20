<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RenewalEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;

    public $clientName;

    public $domainName;

    public $price;

    public $totalPrice;

    public $daysUntilExpiration;

    public $expirationDate;

    public $customMessage;

    public $items;

    public $logoData;

    public $phone;

    public $address;

    public $website;

    /**
     * Create a new message instance.
     */
    public function __construct(
        $subject,
        $clientName = null,
        $domainName = null,
        $price = 0,
        $daysUntilExpiration = null,
        $expirationDate = null,
        $customMessage = null,
        $items = [],
        $totalPrice = null,
        $logoData = null,
        $phone = null,
        $address = null,
        $website = null
    ) {
        $this->subject = $subject;
        $this->clientName = $clientName;
        $this->domainName = $domainName;
        $this->price = $price;
        $this->totalPrice = $totalPrice ?? $price;
        $this->daysUntilExpiration = $daysUntilExpiration;
        $this->expirationDate = $expirationDate;
        $this->customMessage = $customMessage;
        $this->items = $items;
        $this->logoData = $logoData;
        $this->phone = $phone;
        $this->address = $address;
        $this->website = $website;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.renewal-email',
            with: [
                'subject' => $this->subject,
                'clientName' => $this->clientName,
                'domainName' => $this->domainName,
                'price' => $this->price,
                'totalPrice' => $this->totalPrice,
                'daysUntilExpiration' => $this->daysUntilExpiration,
                'expirationDate' => $this->expirationDate,
                'customMessage' => $this->customMessage,
                'items' => $this->items,
                'logoData' => $this->logoData,
                'phone' => $this->phone,
                'address' => $this->address,
                'website' => $this->website,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
