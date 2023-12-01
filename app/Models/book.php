<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class book extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'title',
        'author_artist',
        'genre',
        'format',
        'condition',
        'edition',
        'isbn_upc',
        'description',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
