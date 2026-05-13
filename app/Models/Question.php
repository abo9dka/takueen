<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $guarded = [];
    public function field()
    {
        return $this->belongsTo(Field::class);
    }
    
}