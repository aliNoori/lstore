<?php

namespace Tests\Feature;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test JSON response for existing products in the database.
     *
     * @return void
     * @throws RequestException
     */
    public function test_products_list_from_database()
    {
        // ارسال درخواست به سرور با استفاده از Http facade
        $response = Http::get('https://nemoonehshow.ir/store/api/product/list');

        // بررسی وضعیت پاسخ (200 = موفقیت)
        $this->assertEquals(200, $response->status()); // بررسی موفقیت درخواست

        // دریافت داده‌ها از کلید 'data'
        $responseData = $response->json()['data']; // دسترسی به کلید 'data'

        // بررسی اینکه پاسخ یک آرایه است و خالی نیست
        $this->assertIsArray($responseData);
        $this->assertNotEmpty($responseData);

        // بررسی ساختار خاص داده‌های محصول
        foreach ($responseData as $product) {
            $this->assertArrayHasKey('id', $product);
            $this->assertArrayHasKey('name', $product);
            $this->assertArrayHasKey('price', $product);
            $this->assertArrayHasKey('description', $product);
        }
    }
}
