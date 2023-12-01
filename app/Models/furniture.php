<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class furniture extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'type_of_furniture',
        'material',
        'dimensions',
        'color',
        'style',
        'condition',
        'assembly_required',
    ];
    public $table = 'furnitures';
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
