<?php

# Copyright (C) 2010-2015 Magima Co Ltd. All rights reserved.

# @description

# @file           GlobalLogger.php
# @author         0294
# @created date   2015-01-29 16:32
# @version        1.0

require_once('Logger.php');

class GlobalLogger {

    private static $logger = NULL;
    
    public function getLoggerInstance() {
        if (self::$logger == NULL) {
            self::$logger = new Logger();       
        }
        return self::$logger;
    }
}

