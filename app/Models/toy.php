<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class toy extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'type_of_toy_game',
        'age_group',
        'brand',
        'condition',
        'safety_information',
        'assembly_required',
        'recommended_use',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
