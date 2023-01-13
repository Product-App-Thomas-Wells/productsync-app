<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    use HasFactory;
	
    protected $fillable = [
        'name',
        'products_updated',
        'stocks_updated',
		'prices_updated',
		'field_mapping'
    ];
}
