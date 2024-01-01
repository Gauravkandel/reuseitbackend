<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EngagementRecord extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'month',
        'year',
        'engagement_count'
    ];
}
