<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Validator;

class PhoneValidationTest extends TestCase
{
    public function test_phone_validation_accepts_exactly_10_digits(): void
    {
        $validator = Validator::make(
            ['phone' => '9876543210'],
            ['phone' => ['nullable', 'regex:/^[0-9]{10}$/']]
        );

        $this->assertFalse($validator->fails());
    }

    public function test_phone_validation_rejects_fewer_than_10_digits(): void
    {
        $validator = Validator::make(
            ['phone' => '987654321'],
            ['phone' => ['nullable', 'regex:/^[0-9]{10}$/']]
        );

        $this->assertTrue($validator->fails());
    }

    public function test_phone_validation_rejects_more_than_10_digits(): void
    {
        $validator = Validator::make(
            ['phone' => '98765432101'],
            ['phone' => ['nullable', 'regex:/^[0-9]{10}$/']]
        );

        $this->assertTrue($validator->fails());
    }

    public function test_phone_validation_rejects_country_code_plus_prefix(): void
    {
        $validator = Validator::make(
            ['phone' => '+919876543210'],
            ['phone' => ['nullable', 'regex:/^[0-9]{10}$/']]
        );

        $this->assertTrue($validator->fails());
    }

    public function test_phone_validation_rejects_spaces_and_hyphens(): void
    {
        $validatorWithSpaces = Validator::make(
            ['phone' => '98765 43210'],
            ['phone' => ['nullable', 'regex:/^[0-9]{10}$/']]
        );
        $this->assertTrue($validatorWithSpaces->fails());

        $validatorWithHyphen = Validator::make(
            ['phone' => '98765-43210'],
            ['phone' => ['nullable', 'regex:/^[0-9]{10}$/']]
        );
        $this->assertTrue($validatorWithHyphen->fails());
    }

    public function test_phone_validation_rejects_alphanumeric_characters(): void
    {
        $validator = Validator::make(
            ['phone' => '98765abc210'],
            ['phone' => ['nullable', 'regex:/^[0-9]{10}$/']]
        );

        $this->assertTrue($validator->fails());
    }
}
