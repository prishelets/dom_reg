<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'task_id',
        'mode',
        'type',
        'text',
        'created_at',
    ];
}
