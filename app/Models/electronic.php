<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class electronic extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'type_of_electronic',
        'brand',
        'model',
        'condition',
        'warranty_information',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
