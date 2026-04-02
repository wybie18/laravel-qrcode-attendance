<?php

namespace App\Mail;

use App\Models\Personnel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class PersonnelOnboardingMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Personnel $personnel,
        private readonly string $qrImage,
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                (string) config('mail.from.address'),
                'SDO Ormoc Admin Team',
            ),
            cc: [
                new Address(
                    (string) config('mail.personnel_onboarding_cc.address'),
                    (string) config('mail.personnel_onboarding_cc.name'),
                ),
            ],
            subject: 'Check-In/Out System Registration Confirmation',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.personnel-onboarding',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(fn (): string => $this->qrImage, 'qrcode.png')
                ->withMime('image/png'),
        ];
    }
}
