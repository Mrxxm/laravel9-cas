<?php

namespace App\Http\Controllers;

class PathController
{
    public function short() {

        $locations = [
            ['id' => '起点',                 'x' => 120.25, 'y' => 30.46,         'time_mark' => '', 'time_window' => []],  // 起点 仓库
            ['id' => '杭州市临平区中医院',     'x' => 120.143512, 'y' => 30.385034,  'time_mark' => 'am', 'time_window' => [10, 11]],  // 杭州市临平区中医院
            ['id' => '杭州市余杭区第一人民医院', 'x' => 120.085854, 'y' => 30.44252, 'time_mark' => 'pm', 'time_window' => [14, 16]], // 杭州市余杭区第一人民医院
            ['id' => '安吉县人民医院',         'x' => 119.694734, 'y' => 30.628767, 'time_mark' => 'am', 'time_window' => [10, 10]], // 安吉县人民医院
            ['id' => '绍兴市上虞人民医院',      'x' => 120.882158, 'y' => 30.0347,  'time_mark' => 'pm', 'time_window' => [13, 17]], // 绍兴市上虞人民医院
        ];

        $time_priority_locations = [];
        $time_priority_locations[] = array_shift($locations);

        for ($i = 0; $i < count($locations) - 1; $i++) {
            for ($j = $i+1; $j < count($locations); $j++) {
                if ($locations[$j]['time_window'][0] < $locations[$i]['time_window'][0]) {
                    $temp = $locations[$i];
                    $locations[$i] = $locations[$j];
                    $locations[$j] = $temp;
                }
            }
        }

        $time_priority_locations = array_merge($time_priority_locations, $locations);

        $hour = date('H');
        $hour_mark = 'am';
        if ($hour > 12) {
            $hour_mark = 'pm';
        }

        $route = [];
        $start = array_shift($time_priority_locations);
        $route[] = $start;
        $am_points = [];
        $pm_points = [];

        foreach ($time_priority_locations as $time_location) {

            if ($time_location['time_mark'] == 'am') {
                $am_points[] = $time_location;
            } else {
                $pm_points[] = $time_location;
            }
        }

        $this->cal($am_points, $route, $start);
        $this->cal($pm_points, $route, $start);

        dd($route, $start, $am_points, $pm_points);

    }

    public function cal(array &$points, array &$route, array &$start)
    {
        while ($points) {
            $min_point = [];
            $min_distance = PHP_INT_MAX;
            foreach ($points as $location) {
                $distance = $this->calculateDistance($start, $location);
                if ($distance < $min_distance) {
                    $min_distance = $distance;
                    $min_point = $location;
                }
            }
            $route[] = $min_point;
            $start = $min_point;
            foreach ($points as $key => $location) {
                if ($location['id'] == $min_point['id']) {
                    unset($points[$key]);
                }
            }
        }
    }


    // 计算两点之间的欧几里得距离
    protected function calculateDistance($point1, $point2) {
        return sqrt(pow($point1['x'] - $point2['x'], 2) + pow($point1['y'] - $point2['y'], 2));
    }
}
