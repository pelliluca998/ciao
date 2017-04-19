<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TelegramUserLogin extends Model{
	protected $table = 'telegram_user_login';
	protected $fillable = ['id', 'id_user', 'token', 'expiration'];
}
