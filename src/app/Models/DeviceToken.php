<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $token
 */
class DeviceToken extends Model
{
    protected $fillable = ['token'];

    protected $table = 'device_tokens';
}
