<?php


namespace Tests\Unit;

use App\Models\GadsSite;
use App\Models\ProductData;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;


class ProductTest extends TestCase
{

    private array $products = [];
    private array $updateProduct = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->products = [
            [
                'product_name' => fake()->name,
                'product_description' => fake()->sentence,
                'product_code' => 'P999999',
                'cost_in_gbp' => fake()->randomDigit(),
                'stock' => fake()->randomDigit(),
                'discontinued' => '',
            ], [
                'product_name' => fake()->name,
                'product_description' => fake()->sentence,
                'product_code' => 'P999998',
                'cost_in_gbp' => fake()->randomDigit(),
                'stock' => fake()->randomDigit(),
                'discontinued' => 'yes',
            ], [
                'product_name' => fake()->name,
                'product_description' => fake()->sentence,
                'product_code' => 'P999997',
                'cost_in_gbp' => fake()->randomDigit(),
                'stock' => fake()->randomDigit(),
                'discontinued' => 'yes',
            ]
        ];

        $this->updateProduct = [
            'product_name' => fake()->name . '  test',
            'product_description' => fake()->sentence,
            'product_code' => 'P999997',
            'cost_in_gbp' => fake()->randomDigit(),
            'stock' => fake()->randomDigit(),
            'discontinued' => 'yes',
        ];
    }

    public function testAddProduct(): void
    {
        foreach ($this->products as $test_param) {
            $product = ProductData::add($test_param);
            $this->assertDatabaseHas('tblProductData', $product->toArray());
        }
//        $this->assertEquals(1, DB::table('tblProductData')->count(), 'Wrong number of records');
    }


    public function testProductUpdate(): void
    {
        $product = ProductData::add($this->updateProduct);
        $this->assertDatabaseHas('tblProductData', $product->toArray());
    }


    public function testInvalidProduct(): void
    {
        $this->assertDatabaseMissing('tblProductData', ['intProductDataId' => 99999]);
    }

    protected function tearDown(): void
    {
        foreach ($this->products as $test_param) {
            $code = $test_param['product_code'];
            DB::delete("DELETE FROM tblProductData WHERE strProductCode = '$code'");
        }

        parent::tearDown();
    }
}
