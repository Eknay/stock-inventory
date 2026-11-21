<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Tests\TestCase;
use App\Validation\CategoryValidator;

final class CategoryValidatorTest extends TestCase
{
    public function testCategoryNameIsRequired(): void
    {
        $errors = (new CategoryValidator())->validate([
            'name' => '  ',
            'description' => '',
        ]);

        $this->assertArrayHasKey('name', $errors);
    }

    public function testValidCategoryPassesValidation(): void
    {
        $errors = (new CategoryValidator())->validate([
            'name' => 'Elektronik',
            'description' => 'Ofis ürünleri',
        ]);

        $this->assertCount(0, $errors);
    }
}
