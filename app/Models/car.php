<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class car extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'brand',
        'model',
        'year',
        'mileage',
        'condition',
        'km_driven',
        'color',
        'used_time',
        'fuel_type',
        'owner',
        'transmission_type',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
