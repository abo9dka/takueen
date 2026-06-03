<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Roadmap;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoadmapStage extends Model
{
    use HasFactory;
    protected $table = 'roadmap_stages';
    protected $fillable = ['stage_description', 'stage_order', 'requirements', 'roadmap_id'];
    public function roadmap()
    {
        return $this->belongsTo(Roadmap::class, 'roadmap_id');
    }
}
