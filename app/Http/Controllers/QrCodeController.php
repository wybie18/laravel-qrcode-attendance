<?php

namespace App\Http\Controllers;

use App\Services\QrCodeService;
use Illuminate\Http\Request;

class QrCodeController extends Controller
{
    public function __construct(public QrCodeService $qrCodeService) {}

    public function download(Request $request)
    {
        $validated = $request->validate([
            'qr_data' => 'required|string',
            'filename' => 'nullable|string'
        ]);

        $qrImage = $this->qrCodeService->generateQrCodeImage($validated['qr_data']);
        $filename = $validated['filename'] ?? 'personnel-qr';

        return response($qrImage)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}.png\"");
    }
}
