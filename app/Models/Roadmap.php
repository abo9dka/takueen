<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Field;
use App\Models\RoadmapStage;

class Roadmap extends Model
{
    use HasFactory;

    protected $table = 'roadmaps';

    protected $fillable = [
        'title',
        'description',
        'ai_generated',
        'field_id'
    ];
    
    public function field()
    {
        return $this->belongsTo(Field::class, 'field_id');
    }
   
    public function stages()
    {
        return $this->hasMany(RoadmapStage::class, 'roadmap_id');
    }
}