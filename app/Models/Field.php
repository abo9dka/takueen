<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    //protected $fillable = [];
    protected $guarded = [];
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}