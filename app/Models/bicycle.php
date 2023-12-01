<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bicycle extends Model
{
    use HasFactory;
    protected $table = 'bicycles';
    protected $fillable = [
        'product_id',
        'brand',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
