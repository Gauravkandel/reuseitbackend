<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class product extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'category_id',
        'pname',
        'description',
        'Province',
        'District',
        'Municipality',
        'price',
        'extra_features'
    ];
    public function setPnameAttribute($value)
    {
        $this->attributes['pname'] = ucfirst($value);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function EngagementRecord()
    {
        return $this->hasMany(EngagementRecord::class);
    }
    public function image()
    {
        return $this->hasMany(product_image::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
