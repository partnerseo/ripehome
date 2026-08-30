<?php

declare(strict_types=1);

namespace App\Mail;

use App\Services\OtpService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly string $code) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Giriş kodunuz');
    }

    public function content(): Content
    {
        return new Content(
            text: 'emails.otp',
            with: ['minutes' => OtpService::CODE_TTL_MINUTES],
        );
    }
}
