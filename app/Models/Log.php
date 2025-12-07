<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'task_id',
        'template_name',
        'type',
        'text',
        'error_id',
        'created_at',
    ];
}
