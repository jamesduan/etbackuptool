<?php

/************************************************************************
 *
 *  Copyright (C) 2010-2015 Magima Co Ltd. All rights reserved.
 *  
 *  @description
 *  @file           HostManager.php
 *  @author         0294
 *  @created date   2015-02-13 14:24
 *  @version        1.0.1
 *
 *****************************************************************
 */

//function __autoload($class_name) {
//    require_once "$class_name" . ".php";
//}
//
require_once("../CommonLib/metaData.php");
require_once("SrcHost.php");
require_once("DestHost.php");
require_once("WorkStationHost.php");

class HostManager {
    
    public $hosts = array();
    private $host_type  = array();
    
    public function __construct() {
        
        $this->host_type = unserialize(HOST_TYPE);

        $srcHost = new SrcHost();
        $destHost = new DestHost();
        $workstation = new WorkStationHost();
         
        $this->hosts[$this->host_type[0]] = $srcHost;
        $this->hosts[$this->host_type[1]] = $destHost;
        $this->hosts[$this->host_type[2]] = $workstation;
        //print_r($this->hosts);
    }

    public function get_host($hostType) {

        if ($hostType == $this->host_type[0])

            return $this->hosts[$this->host_type[0]];

        else if ($hostType == $this->host_type[1]) 

            return $this->hosts[$this->host_type[1]];

    }
}

