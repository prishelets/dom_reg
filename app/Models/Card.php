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
        'bank',
        'active',
    ];

    public $timestamps = true;
}
