<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\QRProfile;
use App\Services\ContactCardService;

class VCardTest extends TestCase
{
    public function test_vcard_generation_contains_valid_vcard_format(): void
    {
        $profile = new QRProfile([
            'name' => 'Jane Smith',
            'company' => 'Tech Corp',
            'designation' => 'CTO',
            'phone' => '+15551234567',
            'email' => 'jane@techcorp.com',
            'website' => 'https://techcorp.com',
        ]);

        $service = new ContactCardService();
        $vcard = $service->generateVCard($profile);

        $this->assertStringContainsString('BEGIN:VCARD', $vcard);
        $this->assertStringContainsString('VERSION:3.0', $vcard);
        $this->assertStringContainsString('FN:Jane Smith', $vcard);
        $this->assertStringContainsString('ORG:Tech Corp', $vcard);
        $this->assertStringContainsString('TEL;TYPE=CELL:+15551234567', $vcard);
        $this->assertStringContainsString('END:VCARD', $vcard);
    }
}
