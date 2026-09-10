<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceSite extends Model
{
    protected $fillable = [
        'address',
        'lot',
        'zone',
        'connected',
        'meta',
    ];

    protected $casts = [
        'connected' => 'boolean',
        'meta' => 'array',
    ];

    public function customer(){
        return $this->belongsTo(Customer::class);
    }
}
