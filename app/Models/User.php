<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // 关联文章 一对多关系
    public function statuses()
    {
        return $this->hasMany(Status::class);
    }

    /**
     * 创建用户时生成令牌
     */
    public static function boot()
    {
        parent::boot();
        static::creating(function ($user) {
            $user->activation_token = Str::random(10);
            $user->activated = false;
        });
    }

    public function gravatar($size = '100')
    {
        // $hash = md5(strtolower(trim($this->attributes['email'])));
        // return "https://www.gravatar.com/avatar/$hash?s=$size";
        return Storage::url('images/hashiqi.png');
    }

    /**
     * 获取用户关注的微博
     */
    public function feed()
    {
        return $this->statuses()->orderBy('created_at', 'desc');
    }

}
