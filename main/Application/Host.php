<?php

# Copyright (C) 2010-2015 Magima Co Ltd. All rights reserved.

# @description

# @file           Host.php
# @author         0294
# @created date   2015-01-30 10:09
# @version        1.0.1

require_once('OsEnv.php');
require_once('BackupData.php');
require_once('HostSpec.php');

class Host {
    
    //public $backupData;
    public $osEnv;
    public $hostSpec;
    
    public function __construct() {
        //$this->backupData = new BackupData(); 
        $this->osEnv = new OsEnv(); 
        $this->hostSpec = new HostSpec();
    }
}

