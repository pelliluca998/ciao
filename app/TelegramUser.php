<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TelegramUser extends Model
{
    protected $table = 'telegram_user';
	protected $fillable = ['id_user', 'chat_id'];
}
