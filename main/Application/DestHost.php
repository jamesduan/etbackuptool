<?php

# Copyright (C) 2010-2015 Magima Co Ltd. All rights reserved.

# @description

# @file           DestHost.php
# @author         0294
# @created date   2015-01-30 11:26
# @version        1.0.1

require_once('Host.php');
require_once('DestBackupData.php');
require_once('Credential.php');

class DestHost extends Host {
 
    private $destBackupData;
    public $storageCredential;

    public function __construct() {
        $this->destBackupData = new DestBackupData();
        $this->storageCredential = new Credential();
        parent::__construct();
    }

    public function getDestBackupData() {
        return $this->destBackupData;
    }
}

//$d = new DestHost();
//print_r($d);

