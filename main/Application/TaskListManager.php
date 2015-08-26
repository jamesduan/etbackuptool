<?php

/************************************************************************
 *
 *  Copyright (C) 2010-2015 Magima Co Ltd. All rights reserved.
 *  
 *  @description
 *  @file           TaskListManager.php
 *  @author         0294
 *  @created date   2015-02-05 13:50
 *  @version        1.0
 *
 ************************************************************************
 */

require_once('BackupExecutor.php');
require_once('../CommonLib/metaData.php');

class TaskListManager {
    
    private $taskFilePath;
    private $taskArray = array();
           
    public function __construct($task_ini_file_path) {

        $this->taskFilePath = $task_ini_file_path;
    }

    private function getTaskArray() {
        if (file_exists($this->taskFilePath)) {
            $this->taskArray = parse_ini_file(TASK_PATH, true);
            return $this->taskArray;
        }
    }

    public function exec() {
        $executor = new BackupExecutor($this->getTaskArray());
        $executor->start();
    }
}

$task = new TaskListManager(TASK_PATH);
$task->exec();

