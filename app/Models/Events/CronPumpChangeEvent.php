<?php

namespace App\Models\Events;

use App\Models\Event;

class CronPumpChangeEvent extends Event
{
    public function check($run_time, $site, $states)
    {
        $well = 0;
        $main = 0;
        foreach ($states as $state) {
            $well = $state->wellpump;
            $main = $state->mainpump;
        }
        $stateA = $site->state;
        if (isset($stateA['pumps']['wellpump']) == false) {
            $stateA['pumps']['wellpump'] = ($well > 5) ? 1 : 0;
            $stateA['pumps']['mainpump'] = ($main > 5) ? 1 : 0;
            $site->state = $stateA;
            $site->save();
        } else {
            //          \Log::debug("in CronPumpChangeEvent for site:" . $site->name. "  - StateA:".print_r($stateA,true));

            if (isset($stateA['wellPumpStartEvent']) == false and $well < 5 and $stateA['pumps']['wellpump'] == 1) { // well pump appears to be off
                $this->logEvent($run_time, 'cron15', $site, 'Well Pump Stopped');
                $stateA['pumps']['wellpump'] = 0;
            }
            if (isset($stateA['wellPumpStartEvent'])) {
                unset($stateA['wellPumpStartEvent']);  // ignore the next 5 mintutes average data
                $site->state = $stateA;
                $site->save();
            }
            if (isset($stateA['wellPumpStopEvent']) == false and $well > 5 and $stateA['pumps']['wellpump'] == 0) {
                $this->logEvent($run_time, 'cron15', $site, 'Well Pump Started');
                $stateA['pumps']['wellpump'] = 1;
            }
            if (isset($stateA['wellPumpStopEvent'])) {
                unset($stateA['wellPumpStopEvent']);
                $site->state = $stateA;
                $site->save();
            }
            if (isset($stateA['mainPumpStartEvent']) == false and $main < 5 and $stateA['pumps']['mainpump'] == 1) {
                $this->logEvent($run_time, 'cron15', $site, 'Main Pump Stopped');
                $stateA['pumps']['mainpump'] = 0;
            }
            if (isset($stateA['mainPumpStartEvent'])) {
                unset($stateA['mainPumpStartEvent']);  // ignore the next 5 mintutes average data
                $site->state = $stateA;
                $site->save();
            }
            if (isset($stateA['mainPumpStopEvent']) == false and $main > 5 and $stateA['pumps']['mainpump'] == 0) {
                $this->logEvent($run_time, 'cron15', $site, 'Main Pump Started');
                $stateA['pumps']['mainpump'] = 1;
            }
            if (isset($stateA['mainPumpStopEvent'])) {
                unset($stateA['mainPumpStopEvent']);
                $site->state = $stateA;
                $state->save();
            }
            if ($state->isDirty()) {
                \Log::debug('CronPumpChangeEvent: changing stateA:'.print_r($stateA, true));
                $site->save();
                //            } else {
                //                \Log::debug("StateA remains ".print_r($stateA, true));
            }
        }
    }

    public function logEvent($run_time, $source, $site, $type)
    {
        $event = new Event;
        $event->site_id = $site->id;
        $event->type = $type;
        $event->source = $source;
        $event->event_time = $run_time;
        $event->state = $site->state;
        $event->save();
    }
}
