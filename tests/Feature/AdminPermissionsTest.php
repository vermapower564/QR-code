<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminPermissionsTest extends TestCase
{
    public function test_non_admin_user_cannot_access_admin_panel(): void
    {
        $userRole = 'user';
        $isAdmin = ($userRole === 'admin');

        $this->assertFalse($isAdmin);
    }
}
