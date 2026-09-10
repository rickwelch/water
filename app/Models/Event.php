<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    // public $event_name = 'pump';
    protected $table = 'events';

    protected $casts = [
        'state' => 'array',
    ];

    /**
     * @return HasOne
     *                TODO - modifiy event table for index to Site table
     */
    public function site(): HasOne
    {
        return $this->hasOne(Site::class);
    }
}
