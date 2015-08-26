<?php

/************************************************************************
 *
 *  Copyright (C) 2010-2015 Magima Co Ltd. All rights reserved.
 *  
 *  @description
 *  @file           Task.php
 *  @author         0294
 *  @created date   2015-02-05 14:40
 *  @version        1.0
 *
 ************************************************************************
 */

require_once("BackupHandlerManager.php");

class Task {
    
    private $task_desc;
    public $target_host_spec;
    private $task_serial_no;
    public $target_host_credential;
    public $storage_credential;
    //private $task_host_account_ref;
    //private $task_app_account_ref;
    //private $task_flow_id;

    public function run() {
            
    }

    public function setTarget_host_credential($credential) {
        $this->target_host_credential = $credential;
    }

    public function getTarget_host_credential() {
        return $this->target_host_credential;
    }

    public function setStorage_credential($credential) {
        $this->storage_credential = $credential;
    }
    
    public function getStorage_credentail() {
        return $this->storage_credential;
    }

    public function setTask_serial_no($serial_no) {
        $this->task_serial_no = $serial_no;
    }

    public function getTask_serial_no() {
        return $this->task_serial_no;
    }

    public function setTask_desc($task_desc) {
        $this->task_desc = $task_desc;
    }
    
    public function getTask_desc() {
        return $this->task_desc;   
    }

    public function setTarget_host_spec($host_spec) {
        $this->target_host_spec = $host_spec;
    }

    public function getTarget_host_spec() {
        return $this->target_host_spec;
    }

    public function getHandlerByType($backupType) {
        $handlerManager = new BackupHandlerManager() ;
        $handler = $handlerManager->getBackupHandler($backupType);
        return $handler;
    }

    public function getHost($hostType) {
        $handlerManager = new BackupHandlerManager() ;
        $host = $handlerManager->getHost($hostType);
        return $host;
    }
}

//$t = new Task();
//$handler = $t->getHandlerByType('winfile');
//$host = $t->getHost('src');
//print_r($handler);
//print_r($host);
