<?php

# Copyright (C) 2010-2015 Magima Co Ltd. All rights reserved.

# @description

# @file           WorkStationHost.php
# @author         0294
# @created date   2015-01-30 11:26
# @version        1.0.1

require_once('Host.php');
require_once('WorkStationBackupData.php');

class WorkStationHost extends Host {
       
    private $ftp_user;
    private $ftp_password;

    private $backupData;
    
    public function getBackupData() {
        return $this->backupData;
    }
    
    public function __construct() {
        $this->backupData = new WorkStationBackupData();
        parent::__construct();
    }
    
    public function setFtpUser($ftpUser) {
        $this->ftp_user = $ftpUser;
    }

    public function setFtpPassword($ftpPass) {
        $this->ftp_password = $ftpPass;
    }

    public function getFtpUser() {
        return $this->ftp_user;
    }

    public function getFtpPassword() {
        return $this->ftp_password;
    }
}

//$work = new WorkStationHost();
//print_r($work);

