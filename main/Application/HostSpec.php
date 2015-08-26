<?php

/************************************************************************
 *
 *  Copyright (C) 2010-2015 Magima Co Ltd. All rights reserved.
 *  
 *  @description
 *  @file           HostSpec.php
 *  @author         0294
 *  @created date   2015-02-05 16:07
 *  @version        1.1
 *
 ************************************************************************
 */

class HostSpec {

    private $host_name;
    private $host_type;
    private $os_type;

    private $version;
    private $ip;
    private $hdd;
    private $memory;
    private $cpu;

    private $backup_target_type;
    private $backup_target_subtype;
    private $backup_src_dir;
    private $backup_dst_dir;
    private $backup_target_name;
    private $backup_target_config;
    private $backup_dest_ip;
    private $backup_tool;
    private $backup_tool_version;
    private $transfer_protocol;
    private $transfer_client_tool;
    private $transfer_client_tool_version;
    private $transfer_server_tool;
    private $transfer_server_tool_version;

    public function setHost_name($host_name) {
        $this->host_name = $host_name;
    }

    public function getHost_name() {
        return $this->host_name;
    }

    public function setBackup_target_name($backup_target_name) {
        $this->backup_target_name = $backup_target_name;
    }
    public function getBackup_target_name() {
        return $this->backup_target_name;
    }
    public function setBackup_target_config($backup_target_config) {
        $this->backup_target_config = $backup_target_config;
    }
    public function getBackup_target_config() {
        return $this->backup_target_config;
    }
    public function setBackup_dest_ip($backup_dest_ip) {
        $this->backup_dest_ip = $backup_dest_ip;
    }
    public function getBackup_dest_ip() {
        return $this->backup_dest_ip;
    }
    public function setBackup_tool($backup_tool) {
        $this->backup_tool = $backup_tool;
    }
    public function getBackup_tool() {
        return $this->backup_tool;
    }
    public function setBackup_tool_version($backup_tool_version) {
        $this->backup_tool_version = $backup_tool_version;
    }
    public function getBackup_tool_version() {
        return $this->backup_tool_version;
    }
    public function setTransferProtocol($transfer_protocol) {
        $this->transfer_protocol = $transfer_protocol;
    }
    public function getTransferProtocol() {
        return $this->transfer_protocol;
    }
    public function setTransfer_client_tool($transfer_client_tool) {
        $this->transfer_client_tool = $transfer_client_tool;
    }
    public function getTransfer_client_tool() {
        return $this->transfer_client_tool;
    }
    public function setTransfer_server_tool($transfer_server_tool) {
        $this->transfer_server_tool = $transfer_server_tool;
    }
    public function getTransfer_server_tool() {
        return $this->transfer_server_tool;
    }
    public function setTransfer_server_tool_version($server_tool_version) {
        $this->transfer_server_tool_version = $server_tool_version;
    }
    public function getTransfer_server_tool_version() {
        return $this->transfer_server_tool_version;
    }
    public function setBackup_dst_dir($backup_dst_dir) {
        $this->backup_dst_dir = $backup_dst_dir;
    }
    public function getBackup_dst_dir() {
        return $this->backup_dst_dir;
    }
    public function setBackup_src_dir($backup_src_dir) {
        $this->backup_src_dir = $backup_src_dir;       
    }
    public function getBackup_src_dir() {
        return $this->backup_src_dir;
    }
    public function setBackup_target_subtype($subtype) {
        $this->backup_target_subtype = $subtype;
    }
    public function getBackup_target_subtype() {
        return $this->backup_target_subtype;
    }
    public function setBackup_target_type($backup_target_type) {
        $this->backup_target_type = $backup_target_type;
    }
    public function getBackup_target_type() {
        return $this->backup_target_type;
    }
    public function setHost_type($host_type) {
        $this->host_type = $host_type;
    }
    public function getHost_type() {
        return $this->host_type;    
    }
    public function setDbname($dbname) {
        $this->dbname = $dbname;
    }
    public function getDbname() {
        return $this->dbname;
    }
    public function setDbuser($dbuser) {
        $this->dbuser = $dbuser;
    }
    public function getDbuser() {
        return $this->dbuser;
    }
    public function setDbconf($dbconf) {
        $this->dbconf = $dbconf;
    }
    public function getDbconf() {
        return $this->dbconf;
    }
    /**
     * @return the $host
     */
    public function getHost() {
        return $this->host;
    }

    /**
     * @return the $os_type
     */
    public function getOs_type() {
        return $this->os_type;
    }

    /**
     * @return the $version
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * @return the $ip
     */
    public function getIp() {
        return $this->ip;
    }

    /**
     * @return the $hdd
     */
    public function getHdd() {
        return $this->hdd;
    }

    /**
     * @return the $memory
     */
    public function getMemory() {
        return $this->memory;
    }

    /**
     * @return the $cpu
     */
    public function getCpu() {
        return $this->cpu;
    }

    /**
     * @return the $src_backup_dir
     */
    public function getSrc_backup_dir() {
        return $this->src_backup_dir;
    }

    /**
     * @return the $dest_backup_dir
     */
    public function getDest_backup_dir() {
        return $this->dest_backup_dir;
    }

    /**
     * @return the $backup_type
     */
    public function getBackup_type() {
        return $this->backup_type;
    }

    /**
     * @param field_type $host
     */
    public function setHost($host) {
        $this->host = $host;
    }

    /**
     * @param field_type $os_type
     */
    public function setOs_type($os_type) {
        $this->os_type = $os_type;
    }

    /**
     * @param field_type $version
     */
    public function setVersion($version) {
        $this->version = $version;
    }

    /**
     * @param field_type $ip
     */
    public function setIp($ip) {
        $this->ip = $ip;
    }

    /**
     * @param field_type $hdd
     */
    public function setHdd($hdd) {
        $this->hdd = $hdd;
    }

    /**
     * @param field_type $memory
     */
    public function setMemory($memory) {
        $this->memory = $memory;
    }

    /**
     * @param field_type $cpu
     */
    public function setCpu($cpu) {
        $this->cpu = $cpu;
    }

    /**
     * @param field_type $src_backup_dir
     */
    public function setSrc_backup_dir($src_backup_dir) {
        $this->src_backup_dir = $src_backup_dir;
    }

    /**
     * @param field_type $dest_backup_dir
     */
    public function setDest_backup_dir($dest_backup_dir) {
        $this->dest_backup_dir = $dest_backup_dir;
    }

    /**
     * @param field_type $backup_type
     */
    public function setBackup_type($backup_type) {
        $this->backup_type = $backup_type;
    }
}

