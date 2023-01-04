<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
	
    protected $fillable = [
        'source',
        'title',
		'description',
		'image',
        'sku',
		'barcode',
		'price',
		'stock',
		'product_id',
		'variant_id',
		'status',
		'price_original',
		'product_data',
		'stock_data',
		'price_data'
    ];
}
