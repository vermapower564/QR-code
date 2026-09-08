<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\QRProfile;
use App\Services\LinkValidationService;
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
    }

    public function test_valid_http_website_url_validation_passes(): void
    {
        $url = 'http://johnportfolio.com';
        $this->assertTrue($this->linkValidator->isValidUrl($url));
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

    public function test_editing_profile_website_does_not_change_profile_slug_or_qr(): void
    {
        $profile = new QRProfile([
            'user_id' => 1,
            'slug' => 'john-doe',
            'name' => 'John Doe',
            'website' => 'https://original-site.com',
            'status' => 'active',
        ]);

        $this->assertEquals('john-doe', $profile->slug);
        $this->assertEquals('https://original-site.com', $profile->website);

        // Edit website
        $profile->website = 'https://new-updated-site.com';

        // Assert slug is unchanged and website updated
        $this->assertEquals('john-doe', $profile->slug);
        $this->assertEquals('https://new-updated-site.com', $profile->website);
    }

    public function test_unauthorized_user_cannot_edit_another_users_profile(): void
    {
        $owner = new User(['id' => 10, 'role' => 'user']);
        $unauthorizedUser = new User(['id' => 20, 'role' => 'user']);
        $profile = new QRProfile(['id' => 100, 'user_id' => 10, 'slug' => 'owner-slug']);

        $policy = new \App\Policies\QRProfilePolicy();

        $this->assertTrue($policy->update($owner, $profile));
        $this->assertFalse($policy->update($unauthorizedUser, $profile));
    }
}
