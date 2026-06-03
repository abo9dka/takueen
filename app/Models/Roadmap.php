<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Field;
use App\Models\RoadmapStage;
use App\Models\User;

class Roadmap extends Model
{
    use HasFactory;
    protected $table = 'roadmaps';
    protected $fillable = [
        'title',
        'description',
        'ai_generated',
        'field_id',
        'supervisor_id'
    ];
    public function field()
    {
        return $this->belongsTo(Field::class, 'field_id');
    }
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }
    public function stages()
    {
        return $this->hasMany(RoadmapStage::class, 'roadmap_id');
    }
    public function projectSubmissions()
    {
        return $this->hasMany(ProjectSubmission::class);
    }
    public function projects()
    {
        return $this->hasMany(RoadmapProject::class);
    }
    public function progress()
    {
        return $this->hasMany(RoadmapProgress::class, 'roadmap_id');
    }
}
