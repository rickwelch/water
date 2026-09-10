<?php

namespace App\Models;

use App\Models\Events\CronPumpChangeEvent;
use App\Models\Faults\HighLevelFault;
use App\Models\Faults\LowLevelFault;
use App\Models\Faults\OfflineFault;
use App\Models\Faults\PumpScheduleFault;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;

    protected $pumpChecktimes = [];

    public $DEBUG = false;

    protected $casts = [
        'settings' => 'array',
        'state' => 'array',
    ];

    public function timeToSeconds($time)
    {
        $gs = explode(':', $time);
        if (count($gs) == 3) {
            return $gs[0] * 3600 + $gs[1] * 60 + intval($gs[2]);
        }
        if (count($gs) == 2) {
            return $gs[0] * 3600 + $gs[1] * 60;
        }

        return 0;
    }

    /**
     * @return float|int
     */
    protected function normalizeTime($time)
    {
        return intval($time / 300) * 300;
    }

    /**
     * Checks for faults within a given timestamp.
     *
     * This function analyzes States within a 15-minute time window prior to the provided timestamp.
     * It performs multiple checks (pump time, high level, and low level) to detect faults and processes them if any are found.
     *
     * @param  int  $timestamp  The base timestamp to check faults against.
     */
    public function fault_check($timestamp)
    {
        $start_time = date('Y-m-d H:i:s', strtotime('-15 minutes', $timestamp));
        $run_time = date('Y-m-d H:i:s', $timestamp);
        $low_level = new LowLevelFault;
        $high_level = new HighLevelFault;
        $pump_schedule = new PumpScheduleFault;
        // $offline = new OfflineFault();
        echo "start: $start_time  end_time:$run_time\n";
        $states = State::where('site', $this->name)->where('time', '>=', $start_time)->where('time', '<', $run_time)->orderBy('time')->get();
        foreach ($states as $state) {
            echo strtoupper($this->display_name).' - DB: '.$state->time.' ==> Level: '.$state->level.' ==> current '.$state->mainpump."\n";
            $low_level->check($run_time, $this, $state);
            $high_level->check($run_time, $this, $state);
            $pump_schedule->check($run_time, $this, $state);
            // $offline->check($run_time,$this,$state);
        }
        $pump_change = new CronPumpChangeEvent;
        $pump_change->check($run_time, $this, $states);
    }

    public function faults($count = 5)
    {
        return Faults::where('site_id', $this->id)->orderBy('id', 'desc')->limit($count)->get();
    }

    public function events($count = 5)
    {
        return Events::where('site_id', $this->id)->orderBy('id', 'desc')->limit($count)->get();
    }

    public function getCurrentFaultsAttribute()
    {
        // NEED to extract type and use that as an index
        return Fault::where('site_id', $this->id)->wherseeNull('resolved')->get();

        // dd($faults);
    }

    protected function processFaultNotifications() {}

    public function fetchScheduledPumpTimes() {}
}
