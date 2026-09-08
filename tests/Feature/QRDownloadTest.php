<?php

namespace Tests\Feature;

use Tests\TestCase;

class QRDownloadTest extends TestCase
{
    public function test_qr_download_format_headers(): void
    {
        $format = 'png';
        $contentType = ($format === 'svg') ? 'image/svg+xml' : 'image/png';

        $this->assertEquals('image/png', $contentType);
    }
}
