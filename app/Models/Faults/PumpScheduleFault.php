<?php

namespace App\Models\Faults;

use App\Models\Fault;

class PumpScheduleFault extends Fault
{
    protected $DEBUG = false;

    protected $table = 'faults';

    protected $fault_state = null;

    public $fault_name = 'pump_times';

    public function check($timestamp, $site, $state)
    {

        if ($this->DEBUG) {
            echo "in check: {$this->fault_name}\n";
        }
        if ($this->fault_state == null) {
            $this->fault_state = (isset($site->state['faults'][$this->fault_name])) ? $site->state['faults'][$this->fault_name] : false;
        }
        if ($this->DEBUG) {
            $f_state = ($this->fault_state) ? 'True' : 'False';
            echo $this->fault_name.": state: $f_state\n";
        }

        // if($this->DEBUG) echo $this->fault_name . ': ' . $site->settings['fault_check'][$this->fault_name]['level'] . "\n";

        if (isset($site->settings['fault_check'][$this->fault_name]['times'])) {
            $event_times = $site->settings['fault_check'][$this->fault_name]['seconds'];
            // dd($event_times);
            $event_time = date('H:i:s', strtotime($state->time));
            $event_seconds = $this->timeToSeconds($event_time);
            foreach ($event_times as $time => $seconds) {
                $time = (trim($time));
                $diff = abs($event_seconds - $seconds);
                if ($this->DEBUG) {
                    echo "time: $time seconds: $seconds - $event_seconds = $diff -- pump_current: ".$state->mainpump."\n";
                }
                if ($diff < 300) { // event within 5 minutes of check time
                    if ($this->fault_state == false) {
                        if ($state->mainpump < 5.0) { // pump not running - create a fault
                            if ($this->DEBUG) {
                                echo "{$this->fault_name}: >>>>>>Setting pump run time fault\n";
                            }
                            $this->fault_state = true;
                            $this->processFault($timestamp, $site, $state, $this->fault_name, "Pump not running during scheduled run time ($time).");
                        }
                    } else {
                        if ($state->mainpump > 5.0) { // pump is running - reset fault state
                            if ($this->DEBUG) {
                                echo "{$this->fault_name}: >>>>>>Clearing pump run time fault\n";
                            }
                            $this->fault_state = false;

                            $site_state = $site->state;
                            unset($site_state['faults'][$this->fault_name]);
                            $site->state = $site_state;
                            $site->save();

                            $fault = Fault::where('status', '<>', 'resolved')->where('type', $this->fault_name)->where('site_id', $site->id)->first();
                            if ($fault) {
                                $fault->clearFault($timestamp);
                            }
                        }
                    }
                }

            }
            // echo "event_time: $event_time event_seconds: $event_seconds\n";

            /*
                        if (!$this->fault_state) {


                            if($this->DEBUG) echo "{$this->fault_name} model not in fault state, new data level:{$state->level} threshold:{$site->settings['fault_check'][$this->fault_name]['level']}\n";
                            if( $state->level < $site->settings['fault_check'][$this->fault_name]['level']) {
                                if($this->DEBUG) echo "{$this->fault_name}: setting fault state now\n";
                                $this->processFault($timestamp, $site, $state, $this->fault_name,
                                    $this->fault_name . ": Water level (" . $state->level . ") is below threshold limit (". $site->settings['fault_check'][$this->fault_name]['level'] . ")" );
                            }
                        } else {
                            if($this->DEBUG) echo "{$this->fault_name} model is in fault state, new data level:{$state->level} threshold:{$site->settings['fault_check'][$this->fault_name]['level']}\n";
                            if ($state->level > ($site->settings['fault_check'][$this->fault_name]['level']) * 1.05) { // 5% hysteresis
                                if($this->DEBUG) echo "{$this->fault_name}: Clearing fault state\n";
                                $this->fault_state = false;
                                $fault = Fault::where('status', '<>', 'resolved')->where('type',$this->fault_name)->where('site_id', $site->id)->first();
                                if($fault) $fault->clearFault($timestamp);
                            }
                        }

                    }



                    $stateTime = parent::timeToSeconds(date('H:i:s',strtotime($state->time)));

                    if(empty($this->pumpCheckTimes)){
                        $times = explode(',',$this->settings['fault_check']['pump_times']['times']);
                        foreach($times as $time){
                            $this->pumpCheckTimes[]=$this->timeToSeconds($time);
                        }
                        $this->pumpChecktimes = array_unique($this->pumpChecktimes);
                        foreach($this->pumpCheckTimes as $time){
                            if($time - $stateTime < 3600){
                                if ($state->mainpump < 5.0){
                                    $faults = $this->processFault($state->site,"Pump not running during scheduled run time.");
                                } else {
                                    // clear fault
                                }
                            }
                        }
            */
        }
    }

    protected function timeToSeconds($time)
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

    /*
        public function pumpTimeCheck($faults,$state){
            $stateTime = $this->timeToSeconds(date('H:i:s',strtotime($state->time)));
            if(empty($this->pumpChecktimes)){
                $times = explode(',',$this->settings['fault_check']['pump_times']['times']);
                foreach($times as $time){
                    $this->pumpChecktimes[]=$this->timeToSeconds($time);
                }
                $this->pumpChecktimes = array_unique($this->pumpChecktimes);
                foreach($this->pumpChecktimes as $time){
                    if($time - $stateTime < 3600){
                        if ($state->mainpump < 5.0){
                            // add fault
                            $faults = $this->processFault($state->site,"Pump not running during scheduled run time.");
                        } else {
                            // clear fault
                        }
                    }
                }
            }
            return $faults;
        }
    */
}
