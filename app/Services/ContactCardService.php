<?php

namespace App\Services;

use App\Models\QRProfile;

class ContactCardService
{
    public function generateVCard(QRProfile $profile): string
    {
        $lines = [
            'BEGIN:VCARD',
            'VERSION:3.0',
            'FN:' . $this->escapeVCard($profile->name),
        ];

        if (!empty($profile->company)) {
            $lines[] = 'ORG:' . $this->escapeVCard($profile->company);
        }

        if (!empty($profile->designation)) {
            $lines[] = 'TITLE:' . $this->escapeVCard($profile->designation);
        }

        if (!empty($profile->phone)) {
            $lines[] = 'TEL;TYPE=CELL:' . $this->escapeVCard($profile->phone);
        }

        if (!empty($profile->email)) {
            $lines[] = 'EMAIL;TYPE=INTERNET:' . $this->escapeVCard($profile->email);
        }

        if (!empty($profile->website)) {
            $lines[] = 'URL:' . $this->escapeVCard($profile->website);
        }

        if (!empty($profile->whatsapp)) {
            $lines[] = 'TEL;TYPE=WORK,VOICE:' . $this->escapeVCard($profile->whatsapp);
        }

        if (!empty($profile->address)) {
            $lines[] = 'ADR;TYPE=WORK:;;' . $this->escapeVCard($profile->address) . ';;;;';
        }

        if (!empty($profile->bio)) {
            $lines[] = 'NOTE:' . $this->escapeVCard($profile->bio);
        }

        // Add specific social links
        foreach ($profile->socialLinks as $link) {
            $platform = strtolower($link->platform);
            if (in_array($platform, ['instagram', 'facebook', 'linkedin', 'youtube'])) {
                $lines[] = 'X-SOCIALPROFILE;type=' . $platform . ':' . $this->escapeVCard($link->url);
                // Also add as URL for better compatibility
                $lines[] = 'URL;type=' . ucfirst($platform) . ':' . $this->escapeVCard($link->url);
            }
        }

        $lines[] = 'END:VCARD';

        return implode("\r\n", $lines);
    }

    private function escapeVCard(string $value): string
    {
        return str_replace(['\\\\', ';', ',', "\n"], ['\\\\\\\\', '\;', '\,', '\n'], trim($value));
    }
}

