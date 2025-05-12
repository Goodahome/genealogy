<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'title', 'description', 'team_id'];

    protected $casts = [
        'date' => 'date', // 自动将 date 字段转换为 Carbon 实例
    ];  
    /**
     * 获取事件所属的团队
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
