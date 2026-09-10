<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Daily extends Model
{
    use HasFactory;

    /** fetches daily for site and date or creates one if in date range
     * @return void
     */
    public static function build($date, $site)
    {
        $model = null; // Daily::where('date',$date)->where('site',$site)->first();
        if ($model == null) {
            $last_ts = 0;
            $main_avg = 0;
            $main_count = 0;
            $well_avg = 0;
            $well_count = 0;
            $log = '';
            $data = [];
            $count = 0;
            $temperature = 0.0;
            $humidity = 0.0;
            $pressure = 0.0;
            $level = 0.0;
            foreach (State::where('site', $site)->where('time', '>', $date.' 00:00:00')->where('time', '<=', $date.' 23:59:59')->where('status', 'Update')->orderby('id')->get() as $event) {
                $t_data = [];
                $ts = strtotime($event->time);
                $delta_ts = 0;
                // dd($event->metadata);
                $t_data['mainpump'] = $event->mainpump;
                $t_data['wellpump'] = $event->wellpump;
                $temperature += intval($event->meta->temperature);
                $pressure += intval($event->meta->pressure);
                $humidity += intval($event->meta->humidity);
                $level += $event->level;
                $count++;
                if ($last_ts > 0) {
                    $delta_ts = $ts - $last_ts;
                }
                $last_ts = $ts;
                $t_data['delta_ts'] = $delta_ts;

                if ($event->mainpump > 0) {
                    $main_avg += $event->mainpump;
                    $main_count++;
                    $data[$ts] = $t_data;
                }
                if ($event->wellpump > 0) {
                    $well_avg += $event->wellpump;
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
                    $delta = ($event['delta_ts'] > 350) ? 300 : $event['delta_ts'];
                    if ($event['mainpump'] > $main_threshold) {
                        $maintime += $delta;
                    } else {
                        $maintime += ($delta * $event['mainpump'] / $main_avg);
                    }
                }
            }
            foreach ($data as $ts => $event) {
                if ($event['wellpump'] > 0) {
                    $delta = ($event['delta_ts'] > 350) ? 300 : $event['delta_ts'];
                    if ($event['wellpump'] > $well_threshold) {
                        $welltime += $delta;
                    } else {
                        $welltime += ($delta * $event['wellpump'] / $main_avg);
                    }
                    // echo "$welltime - $delta<br>";
                }
            }
            // /            dd($welltime);
            if ($count > 0) {
                $model = new Daily;
                $model->date = $date;
                $model->site = $site;
                $model->mainpumptime = $maintime;
                $model->wellpumptime = $welltime;
                $model->mainpumpcurrent = $main_avg;
                $model->wellpumpcurrent = $well_avg;
                $model->level = $level / $count;
                $model->temperature = $temperature / $count;
                $model->pressure = $pressure / $count;
                $model->humidity = $humidity / $count;
                $model->save();
            }
        }

        return $model;
    }
}
