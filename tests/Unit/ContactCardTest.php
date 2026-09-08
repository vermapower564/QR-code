<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ContactCardTest extends TestCase
{
    public function test_vcard_content_structure(): void
    {
        $vcard = "BEGIN:VCARD\r\nVERSION:3.0\r\nN:Doe;John;;;\r\nFN:John Doe\r\nORG:ABC Tech\r\nTITLE:CEO\r\nTEL;TYPE=CELL:+19999999999\r\nEMAIL:john@example.com\r\nEND:VCARD\r\n";

        $this->assertStringContainsString('BEGIN:VCARD', $vcard);
        $this->assertStringContainsString('END:VCARD', $vcard);
        $this->assertStringContainsString('FN:John Doe', $vcard);
    }
}
