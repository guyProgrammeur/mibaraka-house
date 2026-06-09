<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use chillerlan\QRCode\QRCode as QRCodeLib;
use chillerlan\QRCode\QROptions;

class QrRedirectController extends Controller
{
    /**
     * Rediriger vers la destination du QR code
     */
    public function redirect(string $code)
    {
        $qrCode = QrCode::where('code', $code)
            ->where('is_active', true)
            ->firstOrFail();
        
        if (!$qrCode->isValid()) {
            abort(404, 'Ce QR code n\'est plus actif.');
        }
        
        $qrCode->incrementScanCount();
        
        return match($qrCode->type) {
            'catalog' => redirect()->route('client.catalog'),
            'category' => redirect()->route('client.category', $qrCode->category->slug),
            'product' => redirect()->route('client.product', $qrCode->product->slug),
            default => redirect()->route('client.catalog'),
        };
    }
    
    /**
     * Générer l'image du QR code (pour affichage externe)
     */
    public function generateImage(string $code, string $format = 'svg')
    {
        $qrCode = QrCode::where('code', $code)->firstOrFail();
        
        $scale = match($qrCode->size) {
            'small' => 4,
            'large' => 8,
            default => 6
        };
        
        $options = new QROptions([
            'outputType' => $format === 'svg' ? QRCodeLib::OUTPUT_MARKUP_SVG : QRCodeLib::OUTPUT_IMAGE_PNG,
            'eccLevel' => QRCodeLib::ECC_H,
            'scale' => $scale,
            'imageBase64' => false,
        ]);
        
        $color = $qrCode->qr_color;
        
        if ($color !== '#000000') {
            $options->moduleValues = [
                0 => '#FFFFFF',
                1 => $color,
            ];
        }
        
        $qrCodeLib = new QRCodeLib($options);
        $image = $qrCodeLib->render($qrCode->destination_url);
        
        $mimeType = $format === 'svg' ? 'image/svg+xml' : 'image/png';
        
        return response($image)->withHeaders(['Content-Type' => $mimeType]);
    }
}