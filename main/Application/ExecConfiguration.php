<?php

# Copyright (C) 2010-2015 Magima Co Ltd. All rights reserved.

# @description

# @file           ExecConfiguration.php
# @author         0294
# @created date   2015-01-29 16:11
# @version        1.0

require_once "../CommonLib/metaData.php";
require_once "../CommonLib/Utils.php";
require_once "../CommonLib/GlobalLogger.php";

class ExecConfiguration {

    private $execConfigFilePath;
    public $configArray;
    private $globalData;
    private $isAvailable;
    
    // 1
    public function __construct() {
        $this->configArray = $this->readConfig();
        $this->configCheck($this->configArray);
    }
    
    public function getData() {
        return $this->allData;
    }
    
    public function getIsAvailable() {
        return $this->isAvailable;
    }

    public function configCheck($iniData) {
        
        $logger = GlobalLogger::getLoggerInstance();
        $config_content = unserialize(CONFIG_CONTENT);
        $host_type = unserialize(HOST_TYPE);
        $key1_flag = 0;

        foreach ( $iniData as $key1 => $value1) {

            foreach ($value1 as $key2 => $value2 ) {
                $key2_flag = 0;
                $value2_flag = 0;

                // check special characters
                if ($this->checkIsSp($key1) == False )
                    exit("'$key1' exit.");
                if ($this->checkIsSp($key2) == False)
                    exit("'$key2' exit.");
                if($this->checkIsSp($value2) == False)
                    exit("'$value2' exit.");

                // check content
                if ($key2 == $config_content[0])
                    $key2_flag++;
                if ($key2 == $config_content[1])
                    $key2_flag++;
                if ($key2 == $config_content[2])
                    $key2_flag++;
                 
                // check hosttype
                if ($key2 == $config_content[0]) {
                    if (!in_array($value2, $host_type))  {
                        $check_host_type_err = "section: [$key1] $value2 dosen't in (src, dest, work)";
                        $logger->addLog("check hosttype key", 0, $check_host_type_err, NULL);
                        $logger::close();
                        echo Utils::red($check_host_type_err);
                        exit();
                    }
                }

                // check hostip
                if ($key2 == $config_content[1]) {
                    if (filter_var($value2, FILTER_VALIDATE_IP) == false) {
                        $checkip_err = "section: [$key1] ipaddress: $value2 is not passed!";
                        $logger->addLog("check hostip", 0, $checkip_err, NULL);
                        $logger::close();
                        echo Utils::red($checkip_err);
                    }
                }
                
                // contain keywords []
                if ($key2_flag != 3 ) {
                    $error_info = "section [$key1] : have not find all the keywords, please check again!";
                }
            }

            if ($key1 == $config_content[5])
                $key1_flag++;
            
            if ($key1 == $config_content[6])
                $key1_flag++;
        }

        $sp_passed = "ini file special characters check passed!";
        $logger->addLog("SP", 100, $sp_passed, NULL);
        $logger::close();
        echo Utils::green($sp_passed . "\n");

        // check content

        if ($key1_flag == 2 ) {
            $content_passed = "ini file content is passed";
            echo Utils::green($content_passed);
        }
    }

    private function checkIsSp($str) {

        $logger = GlobalLogger::getLoggerInstance();

        $reg = "/\~|\!|\@|\#|\\$|\%|\^|\&|\*|\(|\)|\+|\{|\}|\:|\<|\>|\?|\[|\]|\,|\;|\'|\`|\-|\=|\\\|\|/";

        if (preg_match($reg, $str)) {
            $error_info = "[ini file] : '$str' have special characters!";
            $logger->addLog("SP", 0, $error_info, NULL);
            $logger::close();
            echo Utils::red($error_info . "\n");
            return False;
        } else
            return True;
    }

    // load xml document
    public function readConfig() {
        // if config file is not exists throw error.
        if (file_exists(CONFIG_PATH)) {
            try {
                //$xml = simplexml_load_file(CONFIG_PATH);
                $ini_arr = parse_ini_file(CONFIG_PATH, true);

            } catch (Exception $e) {
                $this->$isAvailable = false;
                print $e->getMessage();
            }
            return $ini_arr;
        } else {
            $this->isAvailable = false;
            throw new Exception(CONFIG_PATH . " dose not exists!");
        }
    }
    
    public function queryHostConfigByIp($ip) {
        foreach ($this->configArray as $key => $value) {
            if ($value['host_ip'] == $ip) {
                return $value;
            }
        }
    }
        
    public function querryHostByType($type) {
        $host_configs = array();
        foreach($this->configArray as $value) {
            if ($type == $value['host_type']) {
                array_push($host_configs, $value);
            }
        }
        return $host_configs;
    }
}

