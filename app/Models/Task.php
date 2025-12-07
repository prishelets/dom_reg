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
        'registrar_password',
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
        'domain_paid_price',
        'domain_paid_currency',
        'domain_paid_card_number',
        'cloudflare_email',
        'cloudflare_password',
        'api_key_global',
        'api_key_custom',
        'ns_servers',
        'ns_at_registrar',
        'ns_last_check_at',
        'account_created_at',
        'account_ready_at',
        'account_next_check_at',
    ];

    public function statusColorClass(): string
    {
        return match (strtolower((string) $this->status)) {

            'domain purchased' => 'badge-status badge-status-blue',

            'domain is taken' => 'badge-status badge-status-red',

            'creating registrar account' => 'badge-status badge-status-blue',
            'registrar account created' => 'badge-status badge-status-green',
            
            default => 'badge-status badge-status-gray',
        };
    }

    public function logs()
    {
        return $this->hasMany(Log::class);
    }
}
