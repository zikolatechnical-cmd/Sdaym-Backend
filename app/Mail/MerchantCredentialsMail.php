<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MerchantCredentialsMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public string $email, public string $temporaryPassword) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'بيانات الدخول إلى لوحة التحكم');
    }

    public function content(): Content
    {
        return new Content(
            view: 'mails.merchant-credentials',
            with: ['email' => $this->email, 'temporaryPassword' => $this->temporaryPassword],
        );
    }
}
