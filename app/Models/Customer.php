<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'vehicle_number',
        'vehicle_model',
    ];

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }
}
