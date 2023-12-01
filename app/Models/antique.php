<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class antique extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'type_of_item',
        'era_period',
        'material',
        'condition',
        'provenance_location',
        'rarity',
        'historical_significance',
        'certification',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
