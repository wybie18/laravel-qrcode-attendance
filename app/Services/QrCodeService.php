<?php

namespace App\Services;

use F9WebLtd\QrCode\Facades\QrCode;

use BaconQrCode\Renderer\GDLibRenderer;
use BaconQrCode\Writer;

class QrCodeService
{

    public function generateQrCodeImage(string $qr_data)
    {
        $renderer = new GDLibRenderer(1024, 1);

        $writer = new Writer($renderer);

        // 3. Generate the QR code using your $qr_data content.
        // writeString() tells the writer to output the generated image as a raw PNG binary string.
        $qrImage = $writer->writeString($qr_data);

        return $qrImage;
    }
}
