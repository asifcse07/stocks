<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductData extends Model
{
    use HasFactory;

    protected $table = 'tblProductData';
    public $timestamps = false;

    protected $primaryKey = 'intProductDataId';
    protected $fillable = [
        'strProductName',
        'strProductDesc',
        'strProductCode',
        'decCost',
        'intStock',
        'dtmAdded',
        'dtmDiscontinued',
        'stmTimestamp',
    ];

    public static function add(array $product): ProductData
    {
        return ProductData::updateOrCreate(
            ['strProductCode' => $product['product_code']],
            [
                'strProductName' => $product['product_name'],
                'strProductDesc' => $product['product_description'],
                'strProductCode' => $product['product_code'],
                'decCost' => $product['cost_in_gbp'],
                'intStock' => $product['stock'],
                'dtmAdded' => date('Y-m-d H:i:s'),
                'dtmDiscontinued' => $product['discontinued'] == 'yes' ? date('Y-m-d H:i:s') : null,
                'stmTimestamp' => date('Y-m-d H:i:s'),
            ]);
    }
}
