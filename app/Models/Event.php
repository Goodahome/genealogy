<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'title', 'description'];

    protected $casts = [
        'date' => 'date', // 自动将 date 字段转换为 Carbon 实例
    ];  
}
