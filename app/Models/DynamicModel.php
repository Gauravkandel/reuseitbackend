<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DynamicModel extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $fillable = [];
    protected $table;

    public function setTableBasedOnCondition(string $table, array $fillableAttributes)
    {
        for ($i = 0; $i < count($fillableAttributes); $i++) {

            $this->fillable[$i] = $fillableAttributes[$i];
        }
        $this->table = $table;
    }
    public function product()
    {
        return $this->belongsTo(product::class);
    }
}
