<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceType extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
    ];

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }
}
