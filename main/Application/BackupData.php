<?php

# Copyright (C) 2010-2015 Magima Co Ltd. All rights reserved.

# @description

# @file           BackupData.php
# @author         0294
# @created date   2015-01-30 11:35
# @version        1.0.1

#require_once('');

class BackupData {

    public $backupAppName;
    public $dataType;
    public $dataSize;
    public $dataFileName;
    public $backupDateTime;
    public $summryFileName;
    public $MD5sum;
    public $cmds;
    public $transferFileName;
    public $ftpTransferCmdsListFileName;
    
    public function __construct() { 
    
    }  

    public function checkDigest() {
        
    }
    
    /**
     * @return the $dataType
     */
    public function getDataType() {
        return $this->dataType;
    }

    /**
     * @return the $dataSize
     */
    public function getDataSize() {
        return $this->dataSize;
    }

    /**
     * @return the $dataOwner
     */
    public function getDataOwner() {
        return $this->dataOwner;
    }

    /**
     * @return the $dataDir
     */
    public function getDataDir() {
        return $this->dataDir;
    }

    /**
     * @return the $dataFileName
     */
    public function getDataFileName() {
        return $this->dataFileName;
    }

    /**
     * @return the $backupDateTime
     */
    public function getBackupDateTime() {
        return $this->backupDateTime;
    }

    /**
     * @return the $MD5sum
     */
    public function getMD5sum() {
        return $this->MD5sum;
    }

    /**
     * @param field_type $dataType
     */
    public function setDataType($dataType) {
        $this->dataType = $dataType;
    }

    /**
     * @param field_type $dataSize
     */
    public function setDataSize($dataSize) {
        $this->dataSize = $dataSize;
    }

    /**
     * @param field_type $dataOwner
     */
    public function setDataOwner($dataOwner) {
        $this->dataOwner = $dataOwner;
    }

    /**
     * @param field_type $dataDir
     */
    public function setDataDir($dataDir) {
        $this->dataDir = $dataDir;
    }

    /**
     * @param field_type $dataFileName
     */
    public function setDataFileName($dataFileName) {
        $this->dataFileName = $dataFileName;
    }

    /**
     * @param field_type $backupDateTime
     */
    public function setBackupDateTime($backupDateTime) {
        $this->backupDateTime = $backupDateTime;
    }

    /**
     * @param field_type $MD5sum
     */
    public function setMD5sum($MD5sum) {
        $this->MD5sum = $MD5sum;
    }
}


