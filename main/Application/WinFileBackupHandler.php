<?php

# Copyright (C) 2010-2015 Magima Co Ltd. All rights reserved.

# @description

# @file           WinFileBackupHandler.php
# @author         0294
# @created date   2015-01-30 15:38
# @version        1.0.1

require_once('BackupHandler.php');
#require_once('WorkStationHost.php');

class WinFileBackupHandler extends BackupHandler {

	public function __construct() {
        parent::__construct();
    }

    /* (1) */
    public function execute($srcHost, $storageHost, $logger) {
        echo "\n===============This is Windows server backup!===============\n";
        //print_r($this);
        $srcHostTargetName = $srcHost->hostSpec->getBackup_target_name();
        $srcHostDestBackupDir = $srcHost->hostSpec->getBackup_dst_dir();

        //echo "\n -------------begin download backup files--------------\n";
        $this->downloadBackupFiles($srcHost);

        //echo "\n ----------------------pack data-----------------------\n";
        $this->packData($srcHost);

        //echo "\n ... createReportFile ... \n";
        $this->createReportFile($srcHostTargetName);

        ////echo "\n ... compress data ...\n";
        ////$this->compressData($srcHostTargetName);

        //echo "\n ... upload backup file ...\n";
        $this->transferData($storageHost, $srcHostTargetName, $srcHostDestBackupDir);
        $this->printReportInfo();
        $this->clean();

    }

    public function createReportFile($srcHostTargetName) {
        //echo ".....................1......................\n";
        $tarFileName = $this->backupDataRef->dataFileName;
        echo "\n$tarFileName\n";
        $workdir = TMP_DIR;
        $appName = "$srcHostTargetName";
        $this->cmds['getDataSize'] = "du -s $workdir/$tarFileName | awk -F ' ' '{print $1}'";
        echo "\nGet Size Of Packed File...\n";
        $dataSize = (float)exec($this->cmds['getDataSize']);
        $this->backupDataRef->dataSize = $dataSize;
        if($dataSize >= 1024 ) {
            echo "tar file size: " . round($dataSize/1024 , 2) . "Mb\n";
        } elseif ($dataSize >= 10240 ) {
            echo "tar file size: " . round($dataSize/1024/1024 , 2) . "Gb\n";
        } else {
            echo "tar file size: " . $dataSize . "Kb\n";
        }
        echo Utils::green("\nOK!\n");

        $backupDateTime = $this->backupDataRef->backupDateTime;
        $summryFileName = "$srcHostTargetName.properties";
        $this->cmds['getMD5sum']= "cd $workdir; md5sum $tarFileName | awk -F ' ' '{print $1}'";
        //echo "\n exec: " . $this->cmds['getMD5sum'] . "\n";
        echo "\nDoing : get md5sum of tar file...\n";
        $md5sum = exec($this->cmds['getMD5sum']);
        echo "\nmd5sum: " . $md5sum . "\n";
        echo Utils::green("OK!\n\n");
        
        $this->backupDataRef->summryFileName = $summryFileName;
        $this->cmds['createReportFile'] = "echo '[$srcHostTargetName]\nAppName = $appName\nFileName = $tarFileName\nFileSize = $dataSize\nMD5 = $md5sum\nBackupDateTime = $backupDateTime\n' > $workdir/$summryFileName";
        //echo "exec: " . $this->cmds['createReportFile'];
        echo "Doing: create summry file...\n";
        echo exec($this->cmds['createReportFile']);
        echo Utils::green("OK!\n");
    }
    
    public function compressData($targetName) {
        $workdir = TMP_DIR;
        $currentTime = $this->date->getCurrentTime();
        $zipFileName = "$srcHostTargetName- $currentTime.zip";
        $this->backupDataRef->transferFileName = $zipFileName;
        $tarFileName = $this->backupDataRef->dataFileName;

        $this->cmds['compressData'] = "cd $workdir;zip $zipFileName $tarFileName $srcHostTargetName.properties";
        $this->backupDataRef->transferFileName = $zipFileName;
        echo "exec: " . $this->cmds['compressData'];
        echo exec($this->cmds['compressData']);
    }

    public function transferData($storageHost, $srcHostTargetName, $srcHostDestBackupDir) {
        $workdir = TMP_DIR;
        $ftpCmdsListFileName = "$srcHostTargetName.list";
        $ftpIp = $storageHost->hostSpec->getIp();
        $ftpUser = $storageHost->storageCredential->getUser();
        $ftpPassword = $storageHost->storageCredential->getPassword();
        //$zipFileName = $backupDataRef->transferFileName;
        $tarFileName = $this->backupDataRef->dataFileName;
        $srcHostDestBackupSubDir = $this->backupDataRef->backupAppName . "-" . $this->backupDataRef->backupDateTime;
        $summryFileName = $this->backupDataRef->summryFileName;

        $this->cmds['ftpCmds'] = "echo 'lftp $ftpIp\nuser $ftpUser $ftpPassword\ncd $srcHostDestBackupDir\nmkdir $srcHostDestBackupSubDir\ncd $srcHostDestBackupSubDir\nmput $tarFileName $summryFileName' > $workdir/$ftpCmdsListFileName";
        $this->cmds['transferData'] = "cd $workdir;lftp -f $ftpCmdsListFileName";
        echo "\nDoing: create cmds list file...\n";
        echo exec($this->cmds['ftpCmds']);
        echo "file name: $ftpCmdsListFileName";
        echo Utils::green("\nOK!\n");

        echo "\nDoing: transfer data...\n";
        echo exec($this->cmds['transferData']);
        echo Utils::green("\nOK!\n");
    }

    public function packData($srcHost) {
        $appName = $srcHost->hostSpec->getBackup_target_name();
        $this->backupDataRef->backupAppName = $appName;
        $currentTime = $this->date->getCurrentTime();
        $tarFileName = "$appName-$currentTime.tar";
        $this->backupDataRef->dataFileName = $tarFileName;
        $workdir = TMP_DIR; 
        $this->backupDataRef->backupDateTime = $currentTime;
        $this->cmds['packData'] = "cd $workdir;tar cfp $tarFileName *.bak *.config;";

        echo "\n\nDoing: pack data to a tar file.\n";
        echo exec($this->cmds['packData']);
        echo "Tar file Name: " . $tarFileName . "\n\n";
        echo Utils::green("OK!\n");
    }
  
    private function downloadBackupFiles($srcHost) {
        
        $ftpIp = $srcHost->hostSpec->getIp();
        $ftpUser = $srcHost->hostCredential->getUser();
        $ftpPassword = $srcHost->hostCredential->getPassword();
        //echo "$ftpIp , $ftpUser , $ftpPassword";

        $appName = $srcHost->hostSpec->getBackup_target_name();
        $workdir = TMP_DIR;
        $this->backupDataRef->backupAppName = $appName;

        //print_r($srcHost);
        $ftpCmdListFileName = "$appName-download.list";
        
        $this->cmds['createWorkDirectory'] = "mkdir $workdir";
        
        $this->cmds['createFtpCmdListFile'] = "echo 'lftp $ftpIp\nuser $ftpUser $ftpPassword\nmirror' > $workdir/$ftpCmdListFileName";
        // download
        $this->cmds['downloadBackupFiles'] = "cd $workdir; lftp -f $ftpCmdListFileName";
        echo "\n...begin...downloading...\n";
        
        if (file_exists($workdir))
            echo exec("rm -R $workdir");

        echo "\nDoing: create workdir...\n";
        shell_exec($this->cmds['createWorkDirectory']);
        echo Utils::green("OK!\n");

        echo "\nDoing: create lftp cmds list file...\n";
        shell_exec($this->cmds['createFtpCmdListFile']);
        echo Utils::green("OK!\n");
        
        echo "\nDoing: download backup files\n";
        echo exec($this->cmds['downloadBackupFiles']);
        
        echo "\nThe downloaded files: \n\n";
        echo fread(popen("ls $workdir", 'r'), 4096);
        echo Utils::green("\nOK!\n");
        
    }

    public function clean() {
        $workdir = TMP_DIR;
        echo "cleaning...\n";
        `rm -R $workdir`;
    }
}

