<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Fault extends Model
{
    use HasFactory;

    protected $casts = [
        'state' => 'array',
    ];

    public function site(): HasOne
    {
        return $this->hasOne(Site::class);
    }

    protected function processFault($timestamp, $site, $state, $type, $message)
    {
        $fault_state['fault_state'] = $site->settings['fault_check'][$this->fault_name];
        $fault_state['state'] = $state;
        $fault_state['message'] = $message;
        $this->site_id = $site->id;
        $this->source = 'cron';
        $this->status = 'pending';
        $this->type = $type;
        $this->state = $fault_state;
        $this->start = $timestamp;
        $this->save();
        $site_state = $site->state;
        $site_state['faults'][$type] = $message;
        $site->state = $site_state;
        $site->save();
    }

    protected function clearFault($timestamp)
    {
        /*
        $site = Site::find($this->site_id);
        $state = $site->state;
        unset($state['faults'][$this->type]);
        $site->state = $state;
        $site->save();
        */
        $this->status = 'resolved';
        $this->resolved = $timestamp;
        $this->save();
    }
}
