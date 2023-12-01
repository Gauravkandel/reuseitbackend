<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class product_image extends Model
{
    use HasFactory;
    protected $fillable = ['product_id', 'image_url'];
    public function product()
    {
        return $this->hasOne(product::class);
    }
}
