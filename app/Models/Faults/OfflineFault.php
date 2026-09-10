<?php

namespace App\Models\Faults;

use App\Models\Fault;
use App\Models\State;

class OfflineFault extends Fault
{
    protected $fault_state = null;

    public $fault_name = 'offline';

    protected $table = 'faults';

    protected $DEBUG = false;

    public function check($timestamp, $site, $state)
    {

        if ($this->DEBUG) {
            echo "in check: {$this->fault_name}\n";
        }

        if ($this->fault_state == null) {
            $this->fault_state = (isset($site->state['faults'][$this->fault_name])) ? $site->state['faults'][$this->fault_name] : false;
            $f_state = ($this->fault_state) ? 'True' : 'False';
            if ($this->DEBUG) {
                echo $this->fault_name.': Loading '.$this->fault_state." fault_state: $f_state\n";
            }
        } else {
            $f_state = ($this->fault_state) ? 'True' : 'False';
            if ($this->DEBUG) {
                echo $this->fault_name.": fault_state: $f_state\n";
            }
        }

        // if($this->DEBUG) echo $this->fault_name . ': ' . $site->settings['fault_check'][$this->fault_name]['level'] . "\n";

        if (isset($site->settings['fault_check'][$this->fault_name]['minutes'])) {
            if (isset($site->state['last_contact'])) {
                $last_contact_ts = $site->state['last_contact'];
            } else {
                if ($this->DEBUG) {
                    echo $this->fault_name.": pulling last_contact from states table.\n";
                }
                $last_state = State::where('site', $site->name)->orderBy('id', 'desc')->first();
                $last_contact_ts = strtotime($last_state->time);
            }
            $event_ts = strtotime($timestamp);
            $delta = $event_ts - $last_contact_ts;
            if ($this->DEBUG) {
                echo $this->fault_name.": event:$event_ts last_contact:$last_contact_ts delta:$delta\n";
            }
            if ($delta > $site->settings['fault_check'][$this->fault_name]['minutes']) {
                if (! $this->fault_state) {
                    if ($this->DEBUG) {
                        echo "{$this->fault_name}: processing new fault\n";
                    }
                    $this->processFault($timestamp, $site, $state, $this->fault_name,
                        $this->fault_name.': No contact from site over '.$site->settings['fault_check'][$this->fault_name]['minutes'].' minutes.');
                    $this->fault_state = true;
                }
            } else {
                if ($this->fault_state) {
                    if ($this->DEBUG) {
                        echo "{$this->fault_name}: Clearing fault state\n";
                    }
                    $this->fault_state = false;
                    $site_state = $site->state;
                    unset($site_state['faults'][$this->fault_name]);
                    $site->state = $site_state;
                    $site->save();
                    $fault = Fault::where('status', '<>', 'resolved')->where('type',
                        $this->fault_name)->where('site_id', $site->id)->first();
                    if ($fault) {
                        $fault->clearFault($timestamp);
                    }
                }
            }
        }
    }
}
