<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $table = 'cards';

    protected $fillable = [
        'holder',
        'number',
        'exp_month',
        'exp_year',
        'cvv',
        'label',
    ];

    public $timestamps = true;

    protected $casts = [
        'card_last_used_at' => 'datetime',
    ];
}
