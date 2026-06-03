<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function competitions()
    {
        return $this->hasMany(Competition::class);
    }

    public function supervisedCompetitions()
    {
        return $this->hasMany(Competition::class, 'supervisor_id');
    }

    public function projectSubmissions()
    {
        return $this->hasMany(ProjectSubmission::class);
    }

    public function fieldLevels()
    {
        return $this->hasMany(UserFieldLevel::class);
    }


    public function fields()
    {
        return $this->belongsToMany(Field::class);
    }

    public function roadmaps()
    {
        return $this->hasMany(Roadmap::class, 'supervisor_id');
    }
    public function trainees()
    {
        return $this->hasMany(User::class, 'supervisor_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'description',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
