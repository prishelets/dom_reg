<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'accounts';

    protected $fillable = [
        'email',
        'login',
        'password',
        'email_login',
        'email_password',
        'proxy',
        'first_name',
        'last_name',
        'city',
        'address',
        'zip',
        'phone',
        'security_qa',
        'status',
    ];
}
