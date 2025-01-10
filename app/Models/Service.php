<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    protected $fillable = [
        'customer_id',
        'service_date',
        'next_service_date',
        'service_type_id',
        'notes',
        'welcome_sent',
        'reminder_sent',
    ];

    protected $casts = [
        'service_date' => 'datetime',
        'next_service_date' => 'datetime',
        'welcome_sent' => 'boolean',
        'reminder_sent' => 'boolean',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }
}
