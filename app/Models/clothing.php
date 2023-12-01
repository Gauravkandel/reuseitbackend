<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class clothing extends Model
{
    use HasFactory;
    protected $table = 'clothings';
    protected $fillable = [
        'product_id',
        'type_of_clothing_accessory',
        'size',
        'color',
        'brand',
        'material',
        'condition',
        'care_instructions',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
