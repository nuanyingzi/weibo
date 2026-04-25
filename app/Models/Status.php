<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $fillable = ['content'];

    // 关联用户 一对多关系
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
