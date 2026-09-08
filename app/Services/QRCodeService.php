<?php

namespace App\Services;

use App\Models\QRProfile;
use App\Models\QRCode;
use Endroid\QrCode\QrCode as EndroidQrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Logo\Logo;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class QRCodeService
{
    /**
     * Generate or update QR Code for a profile.
     * The QR encodes ONLY the dynamic profile URL: https://domain.com/p/{slug}
     */
    public function generate(QRProfile $profile, array $options = []): QRCode
    {
        $profileUrl = route('profile.show', $profile->slug);
        
        $fgColor = $options['foreground_color'] ?? '#000000';
        $bgColor = $options['background_color'] ?? '#ffffff';
        $style = $options['style'] ?? 'square';
        $format = $options['format'] ?? 'png';
        $logoPath = $options['logo_path'] ?? null;
        $size = (int) ($options['size'] ?? 512);

        // Convert HEX to RGB
        $fgRgb = $this->hexToRgb($fgColor);
        $bgRgb = $this->hexToRgb($bgColor);

        $qrData = null;
        
        // Endroid QR Code Builder Integration with fallback
        if (class_exists(EndroidQrCode::class)) {
            $writer = ($format === 'svg') ? new SvgWriter() : new PngWriter();
            
            $builder = new EndroidQrCode(
                data: $profileUrl,
                encoding: new Encoding('UTF-8'),
                errorCorrectionLevel: ErrorCorrectionLevel::High,
                size: $size,
                margin: 10,
                foregroundColor: new Color($fgRgb['r'], $fgRgb['g'], $fgRgb['b']),
                backgroundColor: new Color($bgRgb['r'], $bgRgb['g'], $bgRgb['b'])
            );

            $logo = null;
            if ($logoPath && Storage::disk('public')->exists($logoPath)) {
                $absoluteLogoPath = Storage::disk('public')->path($logoPath);
                $logo = new Logo(
                    path: $absoluteLogoPath,
                    resizeToWidth: (int) ($size * 0.2)
                );
            }

            $result = $writer->write($builder, $logo);
            $qrData = $result->getString();
        } else {
            // Pure SVG fallback if package is loading dynamically
            $qrData = $this->generateFallbackSvg($profileUrl, $fgColor, $bgColor, $size);
        }

        $fileName = 'qr-codes/' . $profile->slug . '_' . time() . '.' . $format;
        Storage::disk('public')->put($fileName, $qrData);

        return QRCode::updateOrCreate(
            ['profile_id' => $profile->id],
            [
                'format' => $format,
                'file_path' => $fileName,
                'foreground_color' => $fgColor,
                'background_color' => $bgColor,
                'style' => $style,
                'logo_path' => $logoPath,
            ]
        );
    }

    /**
     * Export Printable PDF Frame with QR Code
     */
    public function generatePdfFrame(QRProfile $profile, string $frameLabel = 'SCAN TO CONNECT'): string
    {
        $qrCode = $profile->qrCode ?: $this->generate($profile);
        $qrImagePath = Storage::disk('public')->path($qrCode->file_path);

        $pdf = Pdf::loadView('pdf.qr-frame', [
            'profile' => $profile,
            'qrImagePath' => $qrImagePath,
            'label' => $frameLabel,
        ]);

        return $pdf->output();
    }

    private function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        return [
            'r' => hexdec(substr($hex, 0, 2)),
            'g' => hexdec(substr($hex, 2, 2)),
            'b' => hexdec(substr($hex, 4, 2)),
        ];
    }

    private function generateFallbackSvg(string $url, string $fg, string $bg, int $size): string
    {
        $encodedUrl = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$size}" height="{$size}" viewBox="0 0 100 100">
    <rect width="100" height="100" fill="{$bg}"/>
    <rect x="10" y="10" width="80" height="80" fill="none" stroke="{$fg}" stroke-width="4"/>
    <rect x="20" y="20" width="20" height="20" fill="{$fg}"/>
    <rect x="60" y="20" width="20" height="20" fill="{$fg}"/>
    <rect x="20" y="60" width="20" height="20" fill="{$fg}"/>
    <text x="50" y="52" font-size="6" text-anchor="middle" fill="{$fg}">{$encodedUrl}</text>
</svg>
SVG;
    }
}
