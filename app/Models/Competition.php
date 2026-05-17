<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Competition extends Model
{
    // protected $primaryKey = 'competition_id';

    protected $fillable = [
        'title',
        'start_date',
        'end_date',
        'status',
        'link_register_competition',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}