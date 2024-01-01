<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class music extends Model
{
    use HasFactory;
    protected $table = "musics";
    protected $fillable = [
        'product_id',
        'type_of_instrument',
        'brand',
        'condition',
        'material',
        'accessories_included',
        'sound_characteristics',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
