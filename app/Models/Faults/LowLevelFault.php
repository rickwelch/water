<?php

namespace App\Models\Faults;

use App\Models\Fault;

class LowLevelFault extends Fault
{
    protected $fault_state = null;

    public $fault_name = 'low_threshold';

    protected $table = 'faults';

    protected $DEBUG = false;

    public function check($timestamp, $site, $state)
    {
        if ($this->DEBUG) {
            echo "in check: {$this->fault_name}\n";
        }
        if ($this->fault_state == null) {
            if ($this->DEBUG) {
                echo $this->fault_name.': Loading '.$this->fault_state." fault_state\n";
            }
            $this->fault_state = (isset($site->state['faults'][$this->fault_name])) ? $site->state['faults'][$this->fault_name] : false;
        }
        $f_state = ($this->fault_state) ? 'True' : 'False';
        if ($this->DEBUG) {
            echo $this->fault_name.": state: $f_state\n";
        }

        // if($this->DEBUG) echo $this->fault_name . ': ' . $site->settings['fault_check'][$this->fault_name]['level'] . "\n";

        if (isset($site->settings['fault_check'][$this->fault_name]['level'])) {
            if (! $this->fault_state) {
                if ($this->DEBUG) {
                    echo "{$this->fault_name} model not in fault state, new data level:{$state->level} threshold:{$site->settings['fault_check'][$this->fault_name]['level']}\n";
                }
                if ($state->level < $site->settings['fault_check'][$this->fault_name]['level']) {
                    if ($this->DEBUG) {
                        echo "{$this->fault_name}: setting fault state now\n";
                    }
                    $this->processFault($timestamp, $site, $state, $this->fault_name,
                        $this->fault_name.': Water level ('.$state->level.') is below threshold limit ('.$site->settings['fault_check'][$this->fault_name]['level'].')');
                }
            } else {
                if ($this->DEBUG) {
                    echo "{$this->fault_name} model is in fault state, new data level:{$state->level} threshold:{$site->settings['fault_check'][$this->fault_name]['level']}\n";
                }
                if ($state->level > ($site->settings['fault_check'][$this->fault_name]['level']) * 1.05) { // 5% hysteresis
                    if ($this->DEBUG) {
                        echo "{$this->fault_name}: Clearing fault state\n";
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
}
