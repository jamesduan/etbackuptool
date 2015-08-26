<?php

# Copyright (C) 2010-2015 Magima Co Ltd. All rights reserved.

# @description

# @file           BackupExecutor.php
# @author         0294
# @created date   2015-01-29 13:43
# @version        1.0

require_once('../CommonLib/GlobalLogger.php');
require_once('../CommonLib/metaData.php');
require_once('../CommonLib/Utils.php');
require_once("TaskManager.php");
require_once("Host.php");
require_once("HostManager.php");
require_once("DestHost.php");
require_once("BackupHandlerManager.php");
require_once("AuthenticationManager.php");

class BackupExecutor {

    public $backupDate;
    //public $backupConfig;
    public $backupLogger ;
    public $backupHandler;
    public $taskList = array();
    public $lifeCycle;
    //public $tasks = array();

    public function welcomInformations() {
        echo "\t\t####################################\t\t\n";
        echo "\t\t#\n";
        echo "\t\t#  " . Utils::red("This is a Enterprise Backup Tool!") . "\n";
        echo "\t\t#\n";
        echo "\t\t####################################\t\t\n\n";
    }

    public function __construct() {
        $this->welcomInformations();
        $backupTypeU = unserialize(BACKUP_TYPE);
        // init task manager.
        $this->backupLogger = GlobalLogger::getLoggerInstance();
        $this->backupDate = Utils::getDateInstance();
        $tmp_tasks = array();

        $taskManager = new TaskManager();

        $this->taskList = $taskManager->getTasks();

        $array_length = count($this->taskList);

        $num = 1;

        foreach($this->taskList as $key => $task) {

            $credentialInitInfo = "\n----------------task credential init begin--------------" . "\n";
            $this->backupLogger->addLog('credential init', 100, $credentialInitInfo, $task->target_host_spec->getIp());
            //echo $credentialInitInfo;
            $task = $this->initialize_credential($task);
                
            $this->backupLogger->addLog('credential init', 100, "init....", $task->target_host_spec->getIp());
            $tmp_tasks[$key] = $task;
            $credentialInitEndInfo = "\n----------------task credential init end----------------" . "\n\n\n\n";

            $this->backupLogger->addLog('credential init', 100, $credentialInitEndInfo, $task->target_host_spec->getIp());
            //echo $credentialInitEndInfo;
            $num ++;
            //print_r($tmp_tasks);
        }

        //print_r($tmp_tasks);
        if ($num < $array_length+1 or $num == 1) {
            exit("your credential is not complete, please try again!\n");
        }

        $task_num = 1;
        print_r($tmp_tasks);
        while(1) {
            $total_timer = Utils::getTimer();
            $total_timer->start();
            foreach($tmp_tasks as $task) {
            
                $timer = Utils::getTimer();
                $host_type = $task->target_host_spec->getHost_type();
                //echo $host_type . "\n";

                $hostManager = new HostManager();
                // srcHost

                $srcHost = $hostManager->get_host($host_type);
                //print_r($task->target_host_spec);
                $srcHost->hostSpec = $task->target_host_spec;

                $srcHost->hostCredential = $task->target_host_credential;

                // destHost
                $target_dest_ip = $task->target_host_spec->getBackup_dest_ip();
                $hostSpecManager = new HostSpecManager();

                $storageHostSpec = $hostSpecManager->querryHostSpecByHostIp($target_dest_ip);
                
                $storageHost = new DestHost();
                $storageHost->hostSpec = $storageHostSpec;
                $storageHost->storageCredential = $task->storage_credential;

                $backupTaskInfo = "\n\n-----------------backup task $task_num begin-----------------\n\n";
                echo "time: " . $timer->startTime;
                $this->backupLogger->addLog('backup task', 100, $backupTaskInfo, $srcHost->hostSpec->getIp());
                echo $backupTaskInfo;
                $backupHandlerManager = new BackupHandlerManager();
                $backupType = $this->getBackupType($srcHost);

                $db_inputs = array();
                if ($backupType == $backupTypeU[1]) {
                    echo "This is a MySQLdb backup ....\n";
                    $db_inputs = $this->getDBInput();
                    $srcHost->hostCredential->user_db = $db_inputs['username'];
                    $srcHost->hostCredential->password_db = $db_inputs['password'];
                }
                //"linux_mysqldb";
                $this->backupHandler = $backupHandlerManager->getBackupHandler($backupType);
                $createBackupInfo = "\n...backup handler create ok!...\n";
                $this->backupLogger->addLog("backup task", 100, $createBackupInfo, $srcHost->hostSpec->getIp());
                $beginBackupInfo = "\n...begin execute backup task!...\n";
                $this->backupLogger->addLog("backup task", 100, $beginBackupInfo, $srcHost->hostSpec->getIp());
                $begin_time = microtime(true);
                $timer->start();
                $this->backupHandler->execute($srcHost, $storageHost, $this->backupLogger);
                $timer->stop();
                echo "time: " . $timer->stopTime;
                $total_time = $timer->spent();
                echo "\nUsed time: " . $total_time . "\n\n";
                $endInfo = "\n-----------------backup task $task_num end-------------------\n\n\n\n\n";
                $this->backupLogger->addLog("backup task", 100, $endInfo, $srcHost->hostSpec->getIp());
                echo $endInfo;
                $task_num ++;
            }
            $total_timer->stop();
            $spent_time = $total_timer->total_time_H();
            echo "\n\n ############# all time spent $spent_time H #############\n\n";
            if ($spent_time > 24)  {
                echo "\nBigger than 24 hours.\n";
                $sleep_time = (48-$spent_time) * 3600;
                echo "sleep $spent_time s";
                sleep($sleep_time);
            } elseif($spent_time <= 24) {
                $sleep_time = (24 - $spent_time) * 3600;
                echo "sleep $sleep_time s";
                sleep($sleep_time);
            }
        }
    }

    public function getDBInput() {
        echo "\n\nPlease input MySQLdb's credential informations:\n\n";
        $user_db = $this->getInputStr("UserName");
        $password_db = $this->getInputStr("Password:");
        $db_passwds = array();
        $db_passwds['username'] = $user_db;
        $db_passwds['password'] = $password_db;
        return $db_passwds;
    }

    /******
      get backup type function
    ***/

    public function getBackupType($srcHost) {

        $backupType = unserialize(BACKUP_TYPE);
        $os_type_arr = unserialize(OS_TYPE);
        $app_type = unserialize(APP_TYPE);
        $app_name = unserialize(APP_NAME);
        //print_r($backupType);
        //print_r($os_type_arr);
        //print_r($app_name);
        //print_r($app_type);
        $os_type = $srcHost->hostSpec->getOs_type();
        $backup_target_type = $srcHost->hostSpec->getBackup_target_type();
        $backup_target_subtype = $srcHost->hostSpec->getBackup_target_subtype();

        if ($os_type == $os_type_arr[1] or $os_type == $os_type_arr[2] or $os_type == $os_type_arr[0]) {
            if ( $backup_target_type == $app_type[1] ) {
                if ($backup_target_subtype == $app_name[0])
                    return $backupType[1];
            } elseif ($backup_target_type == $app_type[0]) {
                if ($backup_target_subtype == $app_name[6])
                    return $backupType[0];
            }
        } elseif ($os_type == $os_type_arr[3]) {
            return $backupType[2];  
        }
    }

    public function initialize_credential($task) {

        $target_hostname = $task->target_host_spec->getHost_name();
        $target_host_ip = $task->target_host_spec->getIp();
        $storage_ip = $task->target_host_spec->getBackup_dest_ip();
        //
        $am = new AuthenticationManager();

        if ($task->target_host_credential->getType() == CREDENTIAL_TYPE0) {
            //echo "\nPlease Input\033[31m Source Host\033[0m :$target_hostname($target_host_ip) credential information:" . "\n";
            $auth = $am->querryAuthByIp($target_host_ip);
            $username = $auth->username;
            $password = $auth->password;
            $task->target_host_credential->setUser($username);
            $task->target_host_credential->setPassword($password);
        }

        //echo "\nplease input \033[31mStorage host\033[0m ($storage_ip) credential information\n";

        if ($task->storage_credential->getType() == CREDENTIAL_TYPE0) {
            $auth2 = $am->querryAuthByIp($storage_ip);
            $username = $auth2->username;
            $password = $auth2->password;
            $task->storage_credential->setUser($username);
            $task->storage_credential->setPassword($password);
        }

        return $task;
        //print_r($task);
    }

    private function getInputStr($str) {

        if ($str != "" and $str != null and $str != "Password:") {
            fwrite(STDOUT, "please input $str:");
            $input = trim(fgets(STDIN));
            return $input;
        } else {
            return $this->prompt_silent($str);
        }
    }

    private function prompt_silent($prompt) {
        if (preg_match('/^win/i', PHP_OS)) {
            $vbscript = sys_get_temp_dir() . 'prompt_password.vbs';
            file_put_contents(
              $vbscript, 'wscript.echo(InputBox("'
              . addslashes($prompt)
              . '", "", "password here"))');
            $command = "cscript //nologo " . escapeshellarg($vbscript);
            $password = rtrim(shell_exec($command));
            unlink($vbscript);
            return $password;
        } else {
            $command = "/usr/bin/env bash -c 'echo OK'";
            if (rtrim(shell_exec($command)) !== 'OK') {
                trigger_error("Can't invoke bash");
                return;
            }
            $command = "/usr/bin/env bash -c 'read -s -p \""
              . addslashes($prompt)
              . "\" mypassword && echo \$mypassword'";
            $password = rtrim(shell_exec($command));
            echo "\n";
            return $password;
        }
    }

    public function stop() {
        exit("backup exit.");
    }
}

$exec = new BackupExecutor();

