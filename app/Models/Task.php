<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = 'tasks';

    protected $fillable = [
        'registrar',
        'domain',
        'country',
        'brand',
        'completed',
        'status',
        'registrar_email',
        'registrar_login',
        'email_login',
        'email_password',
        'first_name',
        'last_name',
        'city',
        'address',
        'zip',
        'phone',
        'proxy',
        'security_qa',
        'domain_paid',
        'domain_paid_date',
        'domain_price',
        'cloudflare_email',
        'cloudflare_password',
        'api_key_global',
        'api_key_custom',
        'ns_servers',
        'ns_at_registrar',
        'ns_last_check_at',
    ];

    public function statusColorClass(): string
    {
        return match ($this->status) {
            'creating registrar account' => 'bg-blue-600',
            'completed'  => 'bg-green-600',
            'error'      => 'bg-red-600',
            default      => 'bg-gray-500',
        };
    }

    public function logs()
    {
        return $this->hasMany(Log::class);
    }
}
