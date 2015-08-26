
<?php

# file name:	Timer.php
# author:		jamesduan
# create date:	2015-03-10 10:52:23
# version:		1.0

class Timer {
	public $startTime;
	public $stopTime;

    function start() {
        $this->startTime = microtime(true);
    }

    function stop() {
        $this->stopTime = microtime(true);
    }

    function spent() {
        $total_time = round(($this->stopTime - $this->startTime), 3);

        if ($total_time > 60) {
            return round($total_time/60, 2) . " min";
        } elseif($total_time > 3600) {
            return round($total_time/3600, 2) . " H";
        } else 
            return $total_time . " s";
    }
    function total_time_H() {
        $total_time = round(($this->stopTime - $this->startTime), 3);
        return round($total_time/3600, 3);
    }
}

