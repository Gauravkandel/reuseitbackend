<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sport extends Model
{
    use HasFactory;
    protected $table = 'sports';

    protected $fillable = [
        'product_id',
        'type_of_equipment',
        'brand',
        'condition',
        'size_weight',
        'features',
        'suitable_sport_activity',
        'warranty_information',
        'usage_instructions',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
