<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cellphone extends Model
{
    use HasFactory;

    protected $casts = [
        'metadata' => 'array',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'number', 'number');
    }

    public function sendMessage($message)
    {
        $this->messages()->create([
            'message' => $message,

        ]);
    }

    public function getDisplayNumberAttribute()
    {
        return '('.substr($this->number, 2, 3).') '.substr($this->number, 5, 3).'-'.substr($this->number, -4);
    }
}
