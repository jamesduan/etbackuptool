<?php

/************************************************************************
 *
 *  Copyright (C) 2010-2015 Magima Co Ltd. All rights reserved.
 *  
 *  @description
 *  @file           Log.php
 *  @author         0294
 *  @created date   2015-02-02 15:25
 *  @version        1.1
 *
 ************************************************************************
 */

require_once "metaData.php";

class Logger {

    /* static variables instance for function get_instance*/
    private static $instance = NULL;
    /* fopen return value is a file pointer */
    private static $handle = NULL;
    /* log switch control the written of log file*/
    private $log_switch = NULL;
    private $log_file_path = NULL;  
    private $log_max_len = NULL; 
    /* log file name's prefix */
    private $log_file_pre = "log_";
    private $currentTime = NULL;
    private $logName = "";

    // init var log file path , log switch log max length.

    public function __construct() {
        
        $this->log_file_path = LOG_FILE_PATH;
        $this->log_switch = LOG_SWITCH;
        $this->log_max_len = LOG_MAX_LEN;
        $this->currentTime = date("Y-m-d H:i:s");
    }

    // return single instance
//    public static function get_instance() {
//        if(!self::$instance instanceof self) {
//            self::$instance = new self;
//        }
//        return self::$instance;
//    }
//    
    // add logs to log file
    // transmit three parameter logname is mean : this code name or you 
    // wants , not is the log filename.
    // type : means log level (INFO , ERROR) two levels.
    // at last write the message($message) to logfile.
    // log_switch control the written log of logfile

    public function addLog($logName, $type, $message, $ipaddr) {
        $this->logName = $logName ;  // add log name to var logName
        $time = $this->currentTime;  // get current date
        
        if ($ipaddr == NULL ) {
            $ipaddr = "workstation";
        }
        if ($this->log_switch) {

            //if (self::$handle == NULL) {

            $filename = $this->log_file_pre . $this->get_max_log_file_suf();
            self::$handle = fopen($this->log_file_path . $filename, 'a');
            //}
            switch($type) {

                case 0:
                    fwrite(self::$handle, "[$ipaddr] " . "[$time]" . " " . "[$this->logName]" . " " . " ERROR:" . $message . " \n");
                    break;
                case 1:
                    fwrite(self::$handle, "[$ipaddr] " . "[$time]" . " " . "[$this->logName]" . " " . " WARN:" . $message . " \n");
                    break;
                case 2:
                    fwrite(self::$handle, "[$ipaddr] " . "[$time]" . " " . "[$this->logName]" . " " . " INFO:" . $message . " \n");
                    break;
                case 3:
                    fwrite(self::$handle, "[$ipaddr] " . "[$time]" . " " . "[$this->logName]" . " " . " DEBUG:" . $message . " \n");
                    break;
                default:
                    fwrite(self::$handle, "[$ipaddr] " . "[$time]" . " " . "[$this->logName]" . " " . " INFO:" . $message . " \n");
                    break;
            }
        }
    }

    // get latest log file's filename suffix
    // the suffix of logfilename is init null.
    // open logfile path and iterate logfile and find the 
    // latest suffix of log filename. and return it.
    private function get_max_log_file_suf() {
        $log_file_suf = null;   

        if ( is_dir($this->log_file_path) ) {

            if ($dh = opendir($this->log_file_path)) {

                while(($file = readdir($dh)) != FALSE) {

                    if ($file != '.' && $file != '..') {
                        if ( filetype($this->log_file_path . $file) == "file") {
                            if (strpos($file, '_') !== false) {
                                $rs = split('_', $file);
                                #print_r($rs);
                                if ($log_file_suf < $rs[1]) {
                                    $log_file_suf = $rs[1];
                                }
                            }
                        }
                    }
                }
                if ($log_file_suf == NULL) {
                    $log_file_suf = 0;
                }
                if (file_exists($this->log_file_path . $this->log_file_pre . $log_file_suf) && filesize($this->log_file_path . $this->log_file_pre . $log_file_suf) >= $this->log_max_len) {
                    $log_file_suf = intval($log_file_suf) + 1;
                }
                return $log_file_suf;
            }
        }
        return 0;
    }
    
    public function close() {
        fclose(self::$handle);
    }
}

