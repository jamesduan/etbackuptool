<?php

# Copyright (C) 2010-2015 Magima Co Ltd. All rights reserved.

# @description

# @file           Utils.php
# @author         0294
# @created date   2015-01-29 17:41
# @version        1.0

require_once('Date.php');
require_once('Timer.php');

class Utils {
    private static $date;
    private static $timer;
    //public $config;
    const GRE_BF = "\033[32m";
    const BL = "\033[0m";
    const RED_BF = "\033[31m";

    public static function green($str) {
        return self::GRE_BF . $str . self::BL;
    }

    public static function red($str) {
        return self::RED_BF . $str . self::BL;
    }
    
    public static function getDateInstance() {
        if (self::$date == NULL ) {
            self::$date = new Date();
        }
        return self::$date;
    }

    public static function getTimer() {
        if(self::$timer == NULL ) {
            self::$timer = new Timer();
        }
        return self::$timer;
    }
}


//$pass = Utils::getInputPasswd('192.168.1.1');
