<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proxy extends Model
{
    protected $table = 'proxies';

    protected $fillable = [
        'protocol',
        'login',
        'password',
        'ip',
        'port',
        'active',
        'last_used_at',
        'success_count',
        'error_count',
        'label',
    ];

    public $timestamps = true;
}
