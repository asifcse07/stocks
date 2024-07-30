<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Exception\RuntimeException;
use Tests\TestCase;

class ImportProductsTest extends TestCase
{


    public function test_importing_without_a_file()
    {
        $this->expectException(RuntimeException::class);
        $this->artisan('app:import-product-data');
    }

    public function test_the_file_not_exist()
    {
        $this->artisan('app:import-product-data test.csv')
            ->expectsOutput('The specified file does not exist.')
            ->assertExitCode(0);;
    }

    public function test_import_products_command()
    {
        $this->artisan('app:import-product-data testStock.csv')
            ->assertExitCode(0);

        $this->assertDatabaseHas('tblProductData', [
            'strProductCode' => 'P0781'
        ]);
        DB::delete("DELETE FROM tblProductData WHERE strProductCode = 'P0781'");

        $this->assertDatabaseHas('tblProductData', [
            'strProductCode' => 'P0782'
        ]);
        DB::delete("DELETE FROM tblProductData WHERE strProductCode = 'P0782'");

        $this->assertDatabaseMissing('tblProductData', [
            'strProductCode' => 'P0783'
        ]);
    }
}
