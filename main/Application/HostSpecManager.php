<?php

# Copyright (C) 2010-2015 Magima Co Ltd. All rights reserved.

# @description

# @file           HostSpecManager.php
# @author         0294
# @created date   2015-01-30 11:04
# @version        1.0.1

//require_once('../../lib/PHPExcel/IOFactory.php');

require_once('../CommonLib/metaData.php');
require_once('HostSpec.php');

class HostSpecManager {
    
    //private $description ;
    //private $storageFormat;
    private $specFilePath;
    private $hostsSpec = array();
    
    public function __construct() {
        
        $this->specFilePath = SPEC_PATH;
        #$this->description = $this->parseSpecDoc();
        $specArr = $this->parseSpecDoc($this->specFilePath);

        //print_r($specArr);
        foreach ($specArr as $key => $value ) {
             
            $hostSpec = new HostSpec();
            
            $hostSpec->setHost_name($value['host_name']);
            $hostSpec->setHost_type($value['host_type']);
            $hostSpec->setOs_type($value['os_type']);
            $hostSpec->setVersion($value['version']);
            $hostSpec->setIp($value['ip']);
            $hostSpec->setHdd($value['hdd']);
            $hostSpec->setMemory($value['memory']);
            $hostSpec->setCpu($value['cpu']);
            $hostSpec->setBackup_target_type($value['backup_target_type']);
            $hostSpec->setBackup_target_subtype($value['backup_target_subtype']);
            $hostSpec->setBackup_src_dir($value['backup_src_dir']);
            $hostSpec->setBackup_dst_dir($value['backup_dst_dir']);
            $hostSpec->setBackup_target_name($value['backup_target_name']);
            $hostSpec->setBackup_target_config($value['backup_target_configuration']);
            $hostSpec->setBackup_dest_ip($value['backup_dest_ip']);
            $hostSpec->setBackup_tool($value['backup_tool']);
            $hostSpec->setBackup_tool_version($value['backup_tool_version']);
            $hostSpec->setTransferProtocol($value['transfer_protocol']);
            $hostSpec->setTransfer_client_tool($value['transfer_client_tool']);
            $hostSpec->setTransfer_server_tool($value['transfer_server_tool']);
            $hostSpec->setTransfer_server_tool_version($value['transfer_server_tool_version']);

            $this->hostsSpec[$key] = $hostSpec;
        }
        //print_r($this->hostsSpec);
    }

    public function parseSpecDoc($path) {
        if (file_exists($path))  {
            $spec_arr = parse_ini_file($path, true);
            return $spec_arr;
        }
    }

    public function getHostsSpec() {
        return $this->hostsSpec;
    }

    public function querryHostSpecByHostIp($ip) {

        foreach ($this->hostsSpec as $key => $value ) {
            if ($ip == $value->getIp())  {
                return $value;
            }
        }
    }

    public function querryHostSpecByKey($key) {
        foreach ($this->hostsSpec as $specName => $value ) {
            if ( $key == $specName) {
                return $value;
            }
        }
    }

}

//$spec = new HostSpecManager();
//print_r($spec->querryHostSpecByKey("spec9"));
