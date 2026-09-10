<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\QRProfile;
use App\Services\LinkValidationService;
use App\Services\QRCodeService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class RegistrationWebsiteTest extends TestCase
{
    protected LinkValidationService $linkValidator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->linkValidator = new LinkValidationService();
    }

    public function test_valid_https_website_url_validation_passes(): void
    {
        $url = 'https://example.com';
        $this->assertTrue($this->linkValidator->isValidUrl($url));
        $this->assertEquals('https://example.com', $this->linkValidator->normalizeUrl($url));
    }

    public function test_valid_http_website_url_validation_passes(): void
    {
        $url = 'http://johnportfolio.com';
        $this->assertTrue($this->linkValidator->isValidUrl($url));
        $this->assertEquals('http://johnportfolio.com', $this->linkValidator->normalizeUrl($url));
    }

    public function test_url_without_scheme_auto_prepends_https(): void
    {
        $url1 = 'example.com';
        $url2 = 'www.example.com';

        $this->assertTrue($this->linkValidator->isValidUrl($url1));
        $this->assertEquals('https://example.com', $this->linkValidator->normalizeUrl($url1));

        $this->assertTrue($this->linkValidator->isValidUrl($url2));
        $this->assertEquals('https://www.example.com', $this->linkValidator->normalizeUrl($url2));
    }

    public function test_scheme_casing_is_normalized_to_lowercase(): void
    {
        $url = 'HTTPS://EXAMPLE.COM/PROFILE';
        $this->assertTrue($this->linkValidator->isValidUrl($url));
        $this->assertEquals('https://EXAMPLE.COM/PROFILE', $this->linkValidator->normalizeUrl($url));
    }

    public function test_whitespace_is_trimmed_during_normalization(): void
    {
        $url = '   https://example.com/test   ';
        $this->assertTrue($this->linkValidator->isValidUrl($url));
        $this->assertEquals('https://example.com/test', $this->linkValidator->normalizeUrl($url));
    }

    public function test_dangerous_javascript_url_scheme_is_rejected(): void
    {
        $url = 'javascript:alert(1)';
        $this->assertFalse($this->linkValidator->isValidUrl($url));
    }

    public function test_dangerous_data_url_scheme_is_rejected(): void
    {
        $url = 'data:text/html;base64,PHNjcmlwdD5hbGVydCgxKTwvc2NyaXB0Pg==';
        $this->assertFalse($this->linkValidator->isValidUrl($url));
    }

    public function test_dangerous_file_url_scheme_is_rejected(): void
    {
        $url = 'file:///etc/passwd';
        $this->assertFalse($this->linkValidator->isValidUrl($url));
    }

    public function test_dangerous_vbscript_url_scheme_is_rejected(): void
    {
        $url = 'vbscript:msgbox(1)';
        $this->assertFalse($this->linkValidator->isValidUrl($url));
    }

    public function test_malformed_url_without_valid_host_is_rejected(): void
    {
        $url = 'http://';
        $this->assertFalse($this->linkValidator->isValidUrl($url));
    }

    public function test_empty_and_whitespace_only_urls_are_rejected(): void
    {
        $this->assertFalse($this->linkValidator->isValidUrl(''));
        $this->assertFalse($this->linkValidator->isValidUrl('   '));
    }

    public function test_user_creation_with_valid_website_stores_url(): void
    {
        $user = new User([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('Roushan@123'),
            'website' => 'https://mybusiness.com',
        ]);

        $this->assertEquals('https://mybusiness.com', $user->website);
    }

    public function test_dynamic_qr_preservation_when_editing_website_destination(): void
    {
        // 1. Create initial profile with Website A
        $profile = new QRProfile([
            'id' => 42,
            'user_id' => 1,
            'slug' => 'roushan',
            'name' => 'Roushan',
            'website' => 'https://example.com',
            'status' => 'active',
        ]);

        // 2. Compute dynamic QR target URL (always points ONLY to /p/{slug})
        $expectedQrTargetUrl = route('profile.show', 'roushan');
        $this->assertStringContainsString('/p/roushan', $expectedQrTargetUrl);

        // Assert initial website destination is Website A
        $this->assertEquals('https://example.com', $profile->website);

        // 3. Edit website destination to Website B
        $newWebsite = 'https://google.com';
        $normalizedNewWebsite = $this->linkValidator->normalizeUrl($newWebsite);
        $this->assertTrue($this->linkValidator->isValidUrl($normalizedNewWebsite));

        $profile->website = $normalizedNewWebsite;

        // 4. Verify profile slug and dynamic QR target URL remain EXACTLY identical
        $this->assertEquals('roushan', $profile->slug);
        $this->assertEquals($expectedQrTargetUrl, route('profile.show', $profile->slug));

        // 5. Verify stored website destination is now Website B
        $this->assertEquals('https://google.com', $profile->website);
    }

    public function test_unauthorized_user_cannot_edit_another_users_profile(): void
    {
        $owner = new User(['role' => 'user']);
        $owner->id = 10;
        $unauthorizedUser = new User(['role' => 'user']);
        $unauthorizedUser->id = 20;
        
        $profile = new QRProfile(['user_id' => 10, 'slug' => 'owner-slug']);
        $profile->id = 100;

        $policy = new \App\Policies\QRProfilePolicy();

        $this->assertTrue($policy->update($owner, $profile));
        $this->assertFalse($policy->update($unauthorizedUser, $profile));
    }
}
