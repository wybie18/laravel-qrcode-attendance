<?php

namespace App\Services;

use App\Jobs\SendPersonnelOnboardingMail;
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

        SendPersonnelOnboardingMail::dispatch($personnel, base64_encode($qrImage));
    }
}
