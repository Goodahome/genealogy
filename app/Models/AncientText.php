<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AncientText extends Model
{
    protected $fillable = ['team_id', 'title', 'content', 'volume', 'page'];

    /**
     * 获取关联的团队
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}