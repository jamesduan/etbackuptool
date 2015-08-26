<?php

# Copyright (C) 2010-2015 Magima Co Ltd. All rights reserved.

# @description

# @file           BackupHandlerManager.php
# @author         0294
# @created date   2015-01-30 09:36
# @version        1.0

require_once('../CommonLib/metaData.php');
require_once('FileBackupHandler.php');
require_once('MySQLBackupHandler.php');
require_once('WinFileBackupHandler.php');

class BackupHandlerManager {

    //private $srcHost;
    //private $destHost;
    //private $workstation;
    //private $hosts = array();
    
    // handlers
    private $backupType = array();
    private $handlers = array();
    //private $fileBackupHandler;
    //private $mysqldbBackupHandler;
    //private $winFileBackupHandler;

    public function __construct() {

        $this->backupType = unserialize(BACKUP_TYPE);
        
        $fileBackupHandler = new FileBackupHandler();
        $mysqldbBackupHandler = new MySQLBackupHandler();
        $winFileBackupHandler = new WinFileBackupHandler();
        // [0] => linux_file
        // [1] => linux_mysqldb
        // [2] => win_file

        $this->registerBackupHandler($this->backupType[0] , $fileBackupHandler);
        $this->registerBackupHandler($this->backupType[1] , $mysqldbBackupHandler);
        $this->registerBackupHandler($this->backupType[2] , $winFileBackupHandler);
    }
    
    public function registerBackupHandler($backupType, $handler) {
        $this->handlers[$backupType] = $handler;
    }

    public function getBackupHandler($backupType) {

        if ($backupType == $this->backupType[0]) {

            return $this->handlers[$this->backupType[0]];

        } elseif ($backupType == $this->backupType[1]) {

            return $this->handlers[$this->backupType[1]];

        } elseif ($backupType == $this->backupType[2]) {

            return $this->handlers[$this->backupType[2]];

        }
    }
}

//$manager = new BackupHandlerManager();
//print_r($manager);

