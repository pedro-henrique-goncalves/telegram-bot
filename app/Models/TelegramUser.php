<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'telegram_id',
        'first_name',
        'username',
        'is_vip',
        'email',
    ];

    protected $casts = [
        'is_vip' => 'boolean',
    ];
}
