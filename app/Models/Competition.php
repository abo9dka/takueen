<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Competition extends Model
{
    protected $fillable = [
        'title',
        'start_date',
        'end_date',
        'status',
        'link_register_competition',
        'user_id',
    ];
    protected $appends = ['status'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function supervisors()
    {
        return $this->belongsToMany(
            User::class,
            'competition_supervisor',
            'competition_id',
            'supervisor_id'
        );
    }
    public function getStatusAttribute()
    {
        if (now()->lt($this->start_date)) {
            return 'upcoming';
        }
        if (
            now()->between(
                $this->start_date,
                $this->end_date
            )
        ) {
            return 'active';
        }

        return 'past';
    }
    public function participants()
    {
        return $this->belongsToMany(
            User::class,
            'competition_user',
            'competition_id',
            'user_id'
        );
    }
}