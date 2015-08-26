<?php

# Copyright (C) 2010-2015 Magima Co Ltd. All rights reserved.

# @description

# @file           Date.php
# @author         0294
# @created date   2015-01-29 17:53
# @version        1.0


class Date {
    public $year;
    public $month;
    public $day;
    
    public $currentTime;
    public $hour;
    public $minute;
    public $second;
    public $timePoint;

    public function __construct() {
        
    }
    
    public function getCurrentTime() {
        $this->currentTime = str_replace(' ', '-', date("Y-m-d H:i"));
        $this->currentTime = str_replace(':', '-', $this->currentTime);
        return $this->currentTime;
    }
} 

