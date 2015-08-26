<?php

# Copyright (C) 2010-2015 Magima Co Ltd. All rights reserved.

# @description

# @file           BackupHandler.php
# @author         0294
# @created date   2015-01-30 12:51
# @version        1.0.1

#require_once('TransferChannel.php');
require_once('BackupData.php');
require_once('../../lib/SSHHost.php');
require_once('../CommonLib/Utils.php');
require_once('../CommonLib/metaData.php');

class BackupHandler {
    
    public $backupDataRef;
    public $transferChannelRef;
    public $execTotalTime;
    public $ssh2_host;
    public $date;
    public $logger;
    public $enableMirror;
    public $handlerName;
    //public $transferFileName;
    
    //public $reporter;
    //public $backupStatus;
    
    public $cmds = array();

	public function __construct() {

        // target host to remote storage host transfer channel init 
        // include transfer channel check.
//        $this->transferChannelRef = new TransferChannel();
        $this->backupDataRef = new BackupData();
        $this->date = Utils::getDateInstance();
        $this->enableMirror = false;
    }

    public function createReportFile($srcHostTargetName) {

        $cmdstr = "";
        $dataSize = $this->backupDataRef->dataSize;
        $dataFileName = $this->backupDataRef->dataFileName;
        $backupDateTime = $this->backupDataRef->backupDateTime;
        $MD5sum = $this->backupDataRef->MD5sum;
        $appName = $this->backupDataRef->backupAppName;

        foreach( $this->cmds as $value) 
            $cmdstr .= "$value;";

        $summryFileName = "$appName" . str_replace("/" , "-" , $dataFileName) . ".properties";
        $this->backupDataRef->summryFileName = $summryFileName;
        $this->cmds['createReportFile'] = "echo '[$srcHostTargetName]\nAppName = $appName\nFileName = $dataFileName\nFileSize = $dataSize\nBackupDateTime = $backupDateTime\nMD5sum = $MD5sum\n' > $summryFileName";
        try {
            echo "\n\nDoing: create Summry file!\n";
            $this->ssh2_host->exec($this->cmds['createReportFile']);
            echo "fileName: " . $summryFileName . "\n";
            echo Utils::green("OK!\n");
            return true;
        } catch(Exception $e) {
            return false;
            echo $e->getMessage();
        }
    }

    public function printReportInfo() {
        $appName = $this->backupDataRef->backupAppName;
        $MD5sum = trim($this->backupDataRef->MD5sum, "\n");
        $dataFileName = $this->backupDataRef->dataFileName;
        $dataSize = $this->backupDataRef->dataSize;
        $backupDateTime = $this->backupDataRef->backupDateTime;
        
        echo "\n\n###########################################\n";
        echo "#\t" . "app name: " . $appName . "\n";
        echo "#\t" . "file name: " . $dataFileName . "\n";
        echo "#\t" . "file size: " . $dataSize . " Kb\n";
        echo "#\t" . "MD5 : " . $MD5sum . "\n";
        echo "#\t" . "backup Time: " . $backupDateTime . "\n";
        echo "###########################################\n";
    }

    public function initSsh2Host($ip, $user, $password) {
        try {

            $this->ssh2_host = new SSHHost($ip, $user, $password);

        } catch (Exception $e) {
            echo "Message: " . $e->getMessage();
            exit("exit.");
        }
    }

    public function getSrcHostBackupDirSize($srcHostBackupDirectory) {
        $this->cmds['getSrcBackupDirSize'] = "du -s $srcHostBackupDirectory | awk -F ' ' '{print $1}'";
        $size = (float)$this->ssh2_host->exec($this->cmds['getSrcBackupDirSize']);

        if ($size >= 1024 * 1024)
            echo "backup dir size: " . round($size/1024/1024, 2) . " Gb\n";
        elseif ($size >= 1024)
            echo "backup dir size: " . round($size/1024, 2) . " Mb\n";
        elseif($size > 0 and $size <= 1024)  
            echo "backup dir size: " . round($size, 2) . " Kb\n";
        else
            echo "backup dir size: " . round($size/1024/1024/1024, 2) . " Tb\n";

        return $size;
    }
        
    public function getTargetHostFreeDiskSpace() {
        $this->cmds['freeDiskSpace'] = "df | awk 'NR==3 {print}' | awk -F ' ' '{print $3}'";
        $freeSize = (float)$this->ssh2_host->exec($this->cmds['freeDiskSpace']);

        if ($freeSize >= 10240)
            echo "\nHost free Disk Space size: " . round($freeSize/1024/1024, 2) . " Gb\n";
        elseif ($freeSize >= 1024)
            echo "\nHost free Disk Space size: " . round($freeSize/1024, 2) . " Mb\n";
        elseif($freeSize > 0 and $freeSize <= 1024)  
            echo "\nHost free Disk Space size: " . round($freeSize, 2) . " Kb\n";
        else
            echo "\nHost free Disk Space size: " . round($freeSize/1024/1024/1024, 2) . " Tb\n";
        return $freeSize;
    }

    public function validateDiskSpaceSize($srcHostBackupDirSize) {

        $freeSize = $this->getTargetHostFreeDiskSpace();

        if ($srcHostBackupDirSize > $freeSize ) {  // disk space not enough

            echo Utils::red("\n\ndisk space is not enough!!!\n\n");
            $this->enableMirror = true;
            echo Utils::green("\n\nEntering lftp mirror mode!!!\n\n");
            return 0;
        } else
            if(($srcHostBackupDirSize/1024) > 3072) {
                echo Utils::green("\n\nTarget directory is very big! Entering lftp mirror mode!\n\n");
                $this->enableMirror = true;
                return 0;
            } else {
                echo Utils::green("\nEntering common backup.\n");
                return 1;
            }
    }

    public function packBackupData($srcHostBackupDirectory, $srcHostTargetName) {

        $currentTime = $this->date->getCurrentTime();
        //echo "------------$srcHostTargetName ---------\n";
        $tarFileName = "";
        if ($srcHostBackupDirectory == TMP_DIR) {
            $tarFileName = "$srcHostTargetName-$currentTime.tar";
        } else {
            $tarFileName = "$srcHostTargetName-$currentTime" . str_replace("/", "-", $srcHostBackupDirectory) .  ".tar";
        }
        $this->cmds['packData'] = "tar cfp $tarFileName $srcHostBackupDirectory";
        try {
            echo "\n\nDoing: pack data...\n";
            $this->ssh2_host->exec($this->cmds['packData']);
            echo "packed file name: " . $tarFileName . "\n";
        } catch(Exception $e) {
            echo "packData: " . $e->getMessage();
            return false;
        }
        echo Utils::green("OK!");
        // get pack file size.
        $this->cmds['getPackFileSize'] = "du -s $tarFileName | awk -F ' ' '{print $1}'";
        try {
            echo "\n\nDoing: get Pack Data File Size\n";
            $tarFileSize = (float)$this->ssh2_host->exec($this->cmds['getPackFileSize']);
            if($tarFileSize >= 1024 ) {
                echo "tar file size: " . round($tarFileSize/1024 , 2) . "Mb\n";
            } elseif ($tarFileSize >= 10240 ) {
                echo "tar file size: " . round($tarFileSize/1024/1024 , 2) . "Gb\n";
            }
        } catch(Exception $e) {
            echo $e->getMessage();
        }

        echo Utils::green("OK!\n");

        $this->backupDataRef->dataSize = $tarFileSize;
        $this->backupDataRef->dataFileName = $tarFileName;
        $this->backupDataRef->backupDateTime = $currentTime;
        
        $this->cmds['getMD5'] = "md5sum $tarFileName | awk -F ' ' '{print $1}'";

        try {
            echo "\n\nDoing: get MD5 string of Pack Data File.\n";
            $md5sum = $this->ssh2_host->exec($this->cmds['getMD5']);
            echo "md5sum: $md5sum";
        } catch(Exception $e) {
            echo $e->getMessage();
        }
        echo Utils::green("OK!");
        $this->backupDataRef->MD5sum = $md5sum;
        return true;
    }
    /*
        backup execute function , exec it can start backup files.
    */   
    public function execute($srcHost, $storageHost, $logger) {
        
        /* init variables */
        $srcHostBackupDirectorys_arr = array();
        $this->logger = $logger;
        $srcHostIp = $srcHost->hostSpec->getIp();
        $srcHostUser = $srcHost->hostCredential->getUser();
        $srcHostPassword = $srcHost->hostCredential->getPassword();
        $srcHostTargetName = $srcHost->hostSpec->getBackup_target_name();
        $srcHostBackupDirectory = $srcHost->hostSpec->getBackup_src_dir();
        $srcHostBackupDirectorys_arr = split(',', $srcHostBackupDirectory);
        //print_r($srcHostBackupDirectorys_arr);
        $destBackupStorageDirectory = $srcHost->hostSpec->getBackup_dst_dir();
        $this->backupDataRef->backupAppName = $srcHostTargetName;
        
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
        $total_num = count($srcHostBackupDirectorys_arr);
        $flag = 0;
        foreach ($srcHostBackupDirectorys_arr as $srcHostBackupDirectory) {
            $srcHostBackupDirectory = trim($srcHostBackupDirectory);
            echo "\n\n-----------Begin Backup : $srcHostBackupDirectory--------------\n\n";
            echo "\nDoing: fetch size of backup dir... \n\n";
            $srcHostBackupDirSize = $this->getSrcHostBackupDirSize($srcHostBackupDirectory);
            echo Utils::green("\033[32mOK! \033[0m\n");

            echo "\nDoing: validation of target Host free of disk...\n";
            if (!$this->validateDiskSpaceSize($srcHostBackupDirSize) ) {

                $this->backupDataRef->dataSize = $srcHostBackupDirSize;
                $this->backupDataRef->dataFileName = $srcHostBackupDirectory;
                //$this->backupDataRef->summryFileName = "$srcHostTargetName.properties"; 
                if ($this->createReportFile($srcHostTargetName))
                    echo "";
                if($this->transferDataToStorage($storageHost, $destBackupStorageDirectory, $srcHostBackupDirectory))
                    $flag++;
                    echo "";
                echo "\nDoing: clean...\n";
                $this->clean();
                echo Utils::green("OK!\n");
                if ($flag == $total_num) {
                    return;
                }

            } else {
                /* pack backup data */
                if ($this->packBackupData($srcHostBackupDirectory, $srcHostTargetName)) 
                    echo "\n";
                else
                    exit("\n\nExit.");

                /* create summary file */
                if ($this->createReportFile($srcHostTargetName))
                    echo "\n";

                /* transfer data  to remote storage host */
                if ($this->transferDataToStorage($storageHost, $destBackupStorageDirectory, $srcHostBackupDirectory))
                    $flag++;
                    $this->printReportInfo();
                    echo "\n";
                /*clean */
                echo "Done.\n";
                echo "\nDoing: cleaning...\n";
                $this->clean(); 
                echo Utils::green("OK!\n");
                if ($flag == $total_num ) {
                    return;
                }
            }
        }
    }
    
    // clean all the tmp files.
    public function clean() {
        $backupType = unserialize(BACKUP_TYPE);
        
        $tarFileName = $this->backupDataRef->dataFileName;
        $zipFileName = $this->backupDataRef->transferFileName;
        $summryFileName = $this->backupDataRef->summryFileName;
        $ftpTransferCmdsListFileName = $this->backupDataRef->ftpTransferCmdsListFileName;
        $workdir = TMP_DIR;

        if ($this->handlerName == $backupType[1]) {
            //echo "\n" . $this->handlerName;
            $this->cmds['clean'] = "rm -rf $workdir";
        } else {
            if ($this->enableMirror == true ) {
                $this->cmds['clean'] =  "rm -rf $summryFileName $ftpTransferCmdsListFileName ";        
            } else {
                $this->cmds['clean'] = "rm -rf $tarFileName $summryFileName $ftpTransferCmdsListFileName";
            }
        }

        //echo $this->handlerName . "1111111\n";
            
        try {
    
            $this->ssh2_host->exec($this->cmds['clean']);

        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /*
    $createCmdsListFile = "echo 'user $ftpUser $ftp_password\nbinary\ncd $destBackupDir\nput $compresse    dFile\n' > $cmdsListFile";
    */

    public function transferDataToStorage($storageHost, $backupDestDirectory, $srcHostBackupDirectory) {
        
        //print_r($storageHost);
        $storageHostIp = $storageHost->hostSpec->getIp();
        $storageFtpUser = $storageHost->storageCredential->getUser();
        $storageFtpPassword = $storageHost->storageCredential->getPassword();

        //$backupDestDirectory = $storageHost->hostSpec->getBackup_dst_dir();
        $cmdsListFileName = "FtpUploadCmds.list";
        $this->backupDataRef->ftpTransferCmdsListFileName = $cmdsListFileName;
        $appName = $this->backupDataRef->backupAppName;
        $dateTime = $this->date->getCurrentTime();
        
        $backupDestSubDir = "$appName-$dateTime";
        //echo "\n--------------$appName , $dateTime ,  $backupDestSubDir\n";
        $summryFileName = $this->backupDataRef->summryFileName;

        if ($this->enableMirror == false) {
            // common
            //$zipFileName = $this->backupDataRef->transferFileName;
            $tarFileName = $this->backupDataRef->dataFileName;
            $this->cmds['createFtpCmdsFile'] = "echo 'user $storageFtpUser $storageFtpPassword\nbinary\ncd $backupDestDirectory\nmkdir $backupDestSubDir\ncd $backupDestSubDir\nprompt\nmput $tarFileName $summryFileName\n' > $cmdsListFileName";
            $this->cmds['transferData']  = "ftp -nv $storageHostIp < $cmdsListFileName";
            
            try {
                echo "\n\nDoing: Create Ftp protocol cmds File...\n";
                $this->ssh2_host->exec($this->cmds['createFtpCmdsFile']);
                echo "fileName: " . $cmdsListFileName . "\n";
                echo Utils::green("OK!\n");
                echo "\n\nDoing: upload begin...\n";
                $this->ssh2_host->exec($this->cmds['transferData']);
                echo Utils::green("OK!\n");
                return true;
            } catch(Exception $e) {
                echo $e->getMessage();
                return false;
            }
        } else {
            // mirror
            $backupDir = "$appName-$dateTime";
            $summryFileName = $this->backupDataRef->summryFileName;
            $this->cmds['createFtpCmdsFile'] = "echo 'lftp $storageHostIp\nuser $storageFtpUser $storageFtpPassword\ncd $backupDestDirectory\nmkdir $backupDir\ncd $backupDir\nput $summryFileName\nmirror -R $srcHostBackupDirectory\nexit' > $cmdsListFileName";
            $this->cmds['transferData'] = "lftp -f $cmdsListFileName";
            try{
                echo "\nDoing: create ftp cmds list file...\n";
                $this->ssh2_host->exec($this->cmds['createFtpCmdsFile']);
                echo Utils::green("OK!\n");
                echo "\nUpload backup datas...\n";
                $this->ssh2_host->exec($this->cmds['transferData']);
                echo Utils::green("OK!\n");
                return true;
            } catch(Exception $e) {
                echo $e->getMessage();
                return false;
            }
        }
    }

    // compress data
    public function compressData($srcHostTargetName) {

        $currentTime = $this->date->getCurrentTime();
        $zipFileName = "$srcHostTargetName-$currentTime.zip";
        $this->backupDataRef->transferFileName = $zipFileName;
        $tarFileName = $this->backupDataRef->dataFileName;
        $this->cmds['compressData'] = "zip $zipFileName $tarFileName $srcHostTargetName.properties";

        $freeDiskSpace = (float)$this->ssh2_host->exec($this->cmds['freeDiskSpace']);
        
        if ((2*$this->backupDataRef->dataSize) < $freeDiskSpace) {
            try {
                $this->ssh2_host->exec($this->cmds['compressData']);
                return true;
            } catch(Exception $e) {
                echo $e->getMessage();
                return false;
            }
        }
    }
}

