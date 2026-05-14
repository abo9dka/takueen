<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    protected $guarded = [];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'field_user');
    }

    public function supervisors()
    {
        return $this->belongsToMany(User::class, 'field_user');
    }
}
