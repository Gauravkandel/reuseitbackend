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
    public function homeAppliance()
    {
        return $this->hasOne(HomeAppliance::class);
    }
    public function electronic()
    {
        return $this->hasOne(electronic::class);
    }
    public function furniture()
    {
        return $this->hasOne(furniture::class);
    }
    public function clothings()
    {
        return $this->hasOne(clothing::class);
    }
    public function books()
    {
        return $this->hasOne(book::class);
    }
    public function antiques()
    {
        return $this->hasOne(antique::class);
    }
    public function cars()
    {
        return $this->hasOne(car::class);
    }
    public function motorcycles()
    {
        return $this->hasOne(motorcycle::class);
    }
    public function bicycles()
    {
        return $this->hasOne(bicycle::class);
    }
    public function scooters()
    {
        return $this->hasOne(scooter::class);
    }
    public function toys()
    {
        return $this->hasOne(toy::class);
    }
    public function musics()
    {
        return $this->hasOne(music::class);
    }
    public function sports()
    {
        return $this->hasOne(sport::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
