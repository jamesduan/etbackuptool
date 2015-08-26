<?php

/************************************************************************
 *
 *  Copyright (C) 2010-2015 Magima Co Ltd. All rights reserved.
 *  
 *  @description
 *  @file           TaskManager.php
 *  @author         0294
 *  @created date   2015-02-13 14:37
 *  @version        1.0.1
 *
 ************************************************************************
 */

require_once("../CommonLib/metaData.php");
require_once("Task.php");
require_once("HostSpecManager.php");
require_once("CredentialManager.php");

class TaskManager {
    
    public $tasks = array();

    public function __construct() {

        try {
            $conf_arr = $this->readTaskConfig(TASK_PATH);
        } catch (Exception $e) {
            echo "except: " . $e->getMessage();
        }

        foreach( $conf_arr as $key => $value) {
            $task = new Task();
         
            $task->setTask_desc($value["task_desc"]);   
            $task->setTask_serial_no($value["task_serial_no"]);
            
            $hostSpecManager = new HostSpecManager();
            //print_r($value['target_host_spec']);
            //print_r($hostSpecManager->querryHostSpecByKey($value['target_host_spec']));
            $task->setTarget_host_spec($hostSpecManager->querryHostSpecByKey($value["target_host_spec"]));

            $credManager = new CredentialManager();
            $task->setTarget_host_credential($credManager->querryCredentialByKey($value['target_host_credential']));
            $task->setStorage_credential($credManager->querryCredentialByKey($value['storage_credential']));

            $this->tasks[$key] = $task;
        }
    }

    public function readTaskConfig($config_path) {

        if (file_exists($config_path)) {
            return parse_ini_file($config_path, true);
        } else {
            throw new Exception(__CLASS__ . " : $config_path not exists!");
            return false;
        }
    }

    public function getTasks() {
        return $this->tasks;
    }
}

//$taskManager = new TaskManager();
//print_r($taskManager->getTasks());

