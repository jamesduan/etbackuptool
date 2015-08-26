<?php

# Copyright (C) 2010-2015 Magima Co Ltd. All rights reserved.

# @description

# @file           MySQLBackupHandler.php
# @author         0294
# @created date   2015-01-30 13:15
# @version        1.0.1

require_once('BackupHandler.php');
require_once("../CommonLib/metaData.php");

class MySQLBackupHandler extends BackupHandler {
    
	public function __construct() {
        parent::__construct();
    }

    public function execute($srcHost, $storageHost, $logger) {
        /* init var*/
        $apps_name = unserialize(APP_NAME);
        $this->logger = $logger;
        $srcHostIp = $srcHost->hostSpec->getIp();
        $srcHostUser = $srcHost->hostCredential->getUser();
        $srcHostPassword = $srcHost->hostCredential->getPassword();
        $srcHostTargetName = $srcHost->hostSpec->getBackup_target_name();
        //$srcHostBackupDirectory = $srcHost->hostSpec->getBackup_src_dir();
        $destBackupStorageDirectory = $srcHost->hostSpec->getBackup_dst_dir();
        $this->backupDataRef->backupAppName = $apps_name[0];

        /* init ssh2 object */
        $initSsh2Info = "\nDoing: init ssh2 connection...\n\n";
        $this->logger->addLog("init ssh2", 100, $initSsh2Info, $srcHostIp);
        Utils::green($initSsh2Info . ":");
        echo $initSsh2Info;
        $this->initSsh2Host($srcHostIp, $srcHostUser, $srcHostPassword);
        
        echo Utils::green("OK!\n");

        /* size of target backup directory and validating the size of directory isn't over the 
            target host free disk space.
        */
        echo "\nExecuting innobackupex tool to backup ...\n";
        $this->backup4mysql($srcHost);
        
        $srcHost->hostSpec->setBackup_src_dir(TMP_DIR);
        //print_r($srcHost->hostSpec);
        $srcHostBackupDirectory = $srcHost->hostSpec->getBackup_src_dir();
        //echo "\nDoing: Stat Target Host Free Disk Space...\n";
        //$freeDisk = $this->getTargetHostFreeDiskSpace();
        
        echo "\nDoing: fetch size of backup dir... \n\n";
        $srcHostBackupDirSize = $this->getSrcHostBackupDirSize($srcHostBackupDirectory);
        echo Utils::green("\033[32mOK! \033[0m\n");
        
        echo "\nDoing: validation of target Host free of disk...\n";
        if (!$this->validateDiskSpaceSize($srcHostBackupDirSize) ) {
            // mirror
            $this->backupDataRef->dataSize = $srcHostBackupDirSize;
            
            $this->backupDataRef->dataFileName =$this->getDataFileName();
            //$this->backupDataRef->summryFileName = "$srcHostTargetName.properties"; 
            if ($this->createReportFile($srcHostTargetName))
                echo "";
            if($this->transferDataToStorage($storageHost, $destBackupStorageDirectory, $srcHostBackupDirectory))
                echo "";
            echo "\nDoing: clean...\n";
            $this->clean();
            echo Utils::green("OK!\n");
            return;

        } else {
            /* pack backup data */
            $srcHostTargetName = trim(str_replace(',', '-', $srcHostTargetName));
            if ($this->packBackupData($srcHostBackupDirectory, $srcHostTargetName)) 
                echo "\n";

            /* create summary file */
            if ($this->createReportFile($srcHostTargetName))
                echo "\n";

            /* transfer data  to remote storage host */
            if ($this->transferDataToStorage($storageHost, $destBackupStorageDirectory, $srcHostBackupDirectory))
                $this->printReportInfo();
                echo "\n";
                /*clean */
            echo "\n\nDoing: clean ...\n";
            $this->clean(); 
            echo "Done.\n";
            return;
        }
    }

    private function backup4mysql($srcHost) {
        $backupType = unserialize(BACKUP_TYPE);
        $this->handlerName = $backupType[1];
        $workdir = TMP_DIR;
        
        $dbuser = $srcHost->hostCredential->user_db;
        $dbpassword = $srcHost->hostCredential->password_db;
        $backupTool = $srcHost->hostSpec->getBackup_tool();
        $dblist = str_replace(',', ' ', $srcHost->hostSpec->getBackup_target_name());
        $dbconf = $srcHost->hostSpec->getBackup_target_config();
       

        $this->cmds['createWorkDir'] = "mkdir -p $workdir";

        $this->cmds['backupMySQL'] = "$backupTool --user=$dbuser --password=$dbpassword --databases='$dblist' --defaults-file=$dbconf $workdir";

        try {
            if (file_exists($workdir)) 
                $this->ssh2_host->exec("rm -rf $workdir");
            echo "\n\nDoing: create work directory!\n";
            $this->ssh2_host->exec($this->cmds['createWorkDir']);
            echo "\nOK!\n";
            echo Utils::green("\n\nDoing: backup mysqldb ... \n");
            $this->ssh2_host->exec($this->cmds['backupMySQL']);
            echo Utils::green("OK!\n");
            return true;
        } catch(Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    private function getDataFileName() {
        $this->cmds['getDataFileName'] = "ls | awk -F ' ' '{print $1}'";
        echo "\nDoing: fetch Data File Name...\n" ; 
        try {
            $dataFileName = $this->ssh2_host->exec($this->cmds['getDataFileName']);     
            return $dataFileName;
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}


