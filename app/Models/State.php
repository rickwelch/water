<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;

    public $timestamps = false;

    public static function calculateDepth($level, $site)
    {
        switch ($site) {
            case 'LOC1':
                return ($level - 4.4) * .65;
            case 'LOC2':
                // return (($level - 5.0) * 12.2) / 12;
                return (($level - 4.2) * 12.2) / 12;
            case 'LOC4':
                // return (($this->level - 4.4) * 12.2) / 12; //  * .417 down below
                // return ($level - 4.7) * 0.664; //.587; //.424; //12.2) / 12;
                return ($level - 4.4) * 0.563; // .587; //.424; //12.2) / 12;
            case 'LOC4A':
                return ($level - 4.4) * 0.52; // 0.41; //0.424; //12.2) / 12;
            default:
                return 0;
        }

    }

    /*
    public function getDepthAttribute()
    {
        return SELF::calculateDepth($this->level, $this->site);
    }
*/
    public function getMetaAttribute()
    {
        return json_decode($this->metadata);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public static function pumptime($site, $date = 'today')
    {
        if ($date == 'today') {
            $date = date('Y-m-d');
        }
        $start = $date.' 00:00:00';
        $end = $date.' 23:59:59';

        $last_ts = 0;
        $main_avg = 0;
        $main_count = 0;
        $well_avg = 0;
        $well_count = 0;
        $log = '';
        $data = [];
        foreach (self::where('site', $site)->where('time', '>', $start)->where('time', '<=', $end)->orderby('id')->get() as $event) {
            $t_data = [];
            $ts = strtotime($event->time);
            $delta_ts = 0;
            $t_data['mainpump'] = $event->mainpump;
            $t_data['wellpump'] = $event->wellpump;
            if ($last_ts > 0) {
                $delta_ts = $ts - $last_ts;
            }
            $last_ts = $ts;
            $t_data['delta_ts'] = $delta_ts;

            if ($event->mainpump > 0) {
                $main_avg += $event->pump1;
                $main_count++;
                $data[$ts] = $t_data;
            }
            if ($event->wellpump > 0) {
                $well_avg += $event->pump2;
                $well_count++;
                $data[$ts] = $t_data;
            }
            // $log .= "time:".$event->time." -- mainpump:".$event->mainpump." -- wellpump:".$event->wellpump."\n";
        }
        // dd($data);
        $main_avg = ($main_count > 0) ? ($main_avg / $main_count) : 1;
        $main_threshold = $main_avg * 0.95;
        $well_avg = ($well_count > 0) ? ($well_avg / $well_count) : 1;
        $well_threshold = $well_avg * 0.95;
        // ////////
        $maintime = 0;
        $welltime = 0;
        foreach ($data as $ts => $event) {
            if ($event['mainpump'] > 0) {
                if ($event['mainpump'] > $main_threshold) {
                    $maintime += $event['delta_ts'];
                } else {
                    $maintime += ($event['delta_ts'] * $event['mainpump'] / $main_avg);
                }
            }
        }
        foreach ($data as $ts => $event) {
            if ($event['wellpump'] > 0) {
                if ($event['wellpump'] > $well_threshold) {
                    $welltime += $event['delta_ts'];
                } else {
                    $welltime += ($event['delta_ts'] * $event['wellpump'] / $main_avg);
                }
            }
        }
        $return = [];
        $return['mainpump'] = $maintime;
        $return['wellpump'] = $welltime;

        dd($return);

    }
}
