<?php

namespace App\Services;

use App\Mail\PersonnelOnboardingMail;
use App\Models\Personnel;
use Illuminate\Support\Facades\Mail;

class PersonnelOnboardingEmailService
{
    public function __construct(
        private readonly QrCodeService $qrCodeService,
    ) {}

    public function sendWelcomeEmail(Personnel $personnel): void
    {
        if (blank($personnel->email) || blank($personnel->qr_code)) {
            return;
        }

        $qrImage = $this->qrCodeService->generateQrCodeImage($personnel->qr_code);

        Mail::to($personnel->email)
            ->send(new PersonnelOnboardingMail($personnel, $qrImage));
    }
}
