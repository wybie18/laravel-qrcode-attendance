<?php

namespace App\Services;

use F9WebLtd\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

class QrCodeService
{

    public function generateQrCodeImage(string $qr_data)
    {
        $qrImage = QrCode::format('png')
            ->size(1024)
            ->errorCorrection('H')
            ->margin(1)
            ->generate($qr_data);

        return $qrImage;
    }
}
