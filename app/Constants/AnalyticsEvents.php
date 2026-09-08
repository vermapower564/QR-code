<?php

namespace App\Constants;

class AnalyticsEvents
{
    public const QR_SCAN = 'qr_scan';
    public const PROFILE_VIEW = 'profile_view';
    public const LINK_CLICK = 'link_click';
    public const CONTACT_SAVE = 'contact_save';
    public const PHONE_CLICK = 'phone_click';
    public const EMAIL_CLICK = 'email_click';
    public const WHATSAPP_CLICK = 'whatsapp_click';
    public const WEBSITE_CLICK = 'website_click';
    public const SHARE = 'share';

    /**
     * Get all supported event types.
     */
    public static function all(): array
    {
        return [
            self::QR_SCAN,
            self::PROFILE_VIEW,
            self::LINK_CLICK,
            self::CONTACT_SAVE,
            self::PHONE_CLICK,
            self::EMAIL_CLICK,
            self::WHATSAPP_CLICK,
            self::WEBSITE_CLICK,
            self::SHARE,
        ];
    }
}
