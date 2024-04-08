<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class wantedad extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'adname',
        'description',
        'Province',
        'District',
        'Municipality'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
