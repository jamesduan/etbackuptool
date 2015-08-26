<?php

# Copyright (C) 2010-2015 Magima Co Ltd. All rights reserved.

# @description

# @file           OsEnv.php
# @author         0294
# @created date   2015-01-30 10:27
# @version        1.0.1

require_once('HostSpec.php');

class OsEnv {

	private $osType;
	private $osVersion;
	private $diskFree;
	private $diskTotalSpace;
	private $memFree;
	private $memTotal;
	private $isSupportFtp;
	private $isSupportHttp;
	private $isSupportNfs;
	private $netApp;
    
	/**
	 * @return the $osType
	 */

	public function getOsType() {
		return $this->osType;
	}

	/**
	 * @return the $osVersion
	 */
	public function getOsVersion() {
		return $this->osVersion;
	}

	/**
	 * @return the $diskFree
	 */
	public function getDiskFree() {
		return $this->diskFree;
	}

	/**
	 * @return the $diskTotalSpace
	 */
	public function getDiskTotalSpace() {
		return $this->diskTotalSpace;
	}

	/**
	 * @return the $memFree
	 */
	public function getMemFree() {
		return $this->memFree;
	}

	/**
	 * @return the $memTotal
	 */
	public function getMemTotal() {
		return $this->memTotal;
	}

	/**
	 * @return the $isSupportFtp
	 */
	public function getIsSupportFtp() {
		return $this->isSupportFtp;
	}

	/**
	 * @return the $isSupportHttp
	 */
	public function getIsSupportHttp() {
		return $this->isSupportHttp;
	}

	/**
	 * @return the $isSupportNfs
	 */
	public function getIsSupportNfs() {
		return $this->isSupportNfs;
	}

	/**
	 * @return the $netApp
	 */
	public function getNetApp() {
		return $this->netApp;
	}

	/**
	 * @param field_type $osType
	 */
	public function setOsType($osType) {
		$this->osType = $osType;
	}

	/**
	 * @param field_type $osVersion
	 */
	public function setOsVersion($osVersion) {
		$this->osVersion = $osVersion;
	}

	/**
	 * @param field_type $diskFree
	 */
	public function setDiskFree($diskFree) {
		$this->diskFree = $diskFree;
	}

	/**
	 * @param field_type $diskTotalSpace
	 */
	public function setDiskTotalSpace($diskTotalSpace) {
		$this->diskTotalSpace = $diskTotalSpace;
	}

	/**
	 * @param field_type $memFree
	 */
	public function setMemFree($memFree) {
		$this->memFree = $memFree;
	}

	/**
	 * @param field_type $memTotal
	 */
	public function setMemTotal($memTotal) {
		$this->memTotal = $memTotal;
	}

	/**
	 * @param field_type $isSupportFtp
	 */
	public function setIsSupportFtp($isSupportFtp) {
		$this->isSupportFtp = $isSupportFtp;
	}

	/**
	 * @param field_type $isSupportHttp
	 */
	public function setIsSupportHttp($isSupportHttp) {
		$this->isSupportHttp = $isSupportHttp;
	}

	/**
	 * @param field_type $isSupportNfs
	 */
	public function setIsSupportNfs($isSupportNfs) {
		$this->isSupportNfs = $isSupportNfs;
	}

	/**
	 * @param field_type $netApp
	 */
	public function setNetApp($netApp) {
		$this->netApp = $netApp;
	}

    public function __construct() {
        $this->netApp = array(
               'ssh' => '22',
               'ftp' => '21',
               'http' => '80',
        );

        $this->isSupportFtp = false;
       	$this->isSupportHttp = false;
       	$this->isSupportNfs = false;
                 
    }
    
    public function check() {
        // diskFree , diskUsed, memFree, memUsed
        echo "checking...\n";
    }
}

