<?php

# Copyright (C) 2010-2015 Magima Co Ltd. All rights reserved.

# @description

# @file           SrcHost.php
# @author         0294
# @created date   2015-01-30 11:26
# @version        1.0.1

require_once('Host.php');
require_once('SrcBackupData.php');
require_once('Credential.php');

class SrcHost extends Host {

    public $backupData;
    public $hostCredential;

    public function __construct() {
        $this->backupData = new SrcBackupData();
        $this->hostCredential = new Credential();
        parent::__construct();
    }
    
    public function getBackupData() {
        return $this->backupData;
    }
}

