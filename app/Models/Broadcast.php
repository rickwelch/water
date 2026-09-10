<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Broadcast extends Model
{
    use HasFactory;

    public function sender(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'sent_by');
    }
}
