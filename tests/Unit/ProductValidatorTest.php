<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Tests\TestCase;
use App\Validation\ProductValidator;

final class ProductValidatorTest extends TestCase
{
    public function testValidProductPassesValidation(): void
    {
        $errors = (new ProductValidator())->validate([
            'name' => 'Test ürünü',
            'sku' => 'EKB-TEST-001',
            'category_id' => '1',
            'unit' => 'adet',
            'critical_threshold' => '5',
            'quantity' => '12',
            'description' => 'Demo açıklaması',
        ]);

        $this->assertCount(0, $errors, 'Geçerli ürün doğrulamadan geçmeli.');
    }

    public function testRequiredAndNumericFieldsAreValidated(): void
    {
        $errors = (new ProductValidator())->validate([
            'name' => '',
            'sku' => 'x',
            'category_id' => 'not-a-number',
            'unit' => '',
            'critical_threshold' => '-1',
            'quantity' => '1.5',
        ]);

        $this->assertArrayHasKey('name', $errors);
        $this->assertArrayHasKey('sku', $errors);
        $this->assertArrayHasKey('category_id', $errors);
        $this->assertArrayHasKey('unit', $errors);
        $this->assertArrayHasKey('critical_threshold', $errors);
        $this->assertArrayHasKey('quantity', $errors);
    }
}
