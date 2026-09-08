<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\QRCodeService;
use App\Models\QRProfile;

class QRCodeServiceTest extends TestCase
{
    public function test_qr_code_service_hex_color_converter(): void
    {
        $service = new QRCodeService();
        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('hexToRgb');
        $method->setAccessible(true);

        $rgb = $method->invoke($service, '#0284c7');
        $this->assertEquals(['r' => 2, 'g' => 132, 'b' => 199], $rgb);
    }
}
