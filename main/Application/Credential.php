<?php

/************************************************************************
 *
 *  Copyright (C) 2010-2015 Magima Co Ltd. All rights reserved.
 *  
 *  @description
 *  @file           Credential.php
 *  @author         0294
 *  @created date   2015-02-28 13:03
 *  @version        1.0
 *
 ************************************************************************
 */

//function __autoload($class_name) {
//    require_once "$class_name" . ".php";
//}
//
class Credential {

    private $description;
    private $cred_id;
    private $type;
    private $secret_file;
    private $user;
    private $password;
    public $user_db;
    public $password_db;

    public function setDescription($desc) {
        $this->description = $desc;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setCred_id($id) {
        $this->cred_id = $id;
    }

    public function getCred_id() {
        return $this->cred_id;
    }

    public function setType($type) {
        $this->type = $type;
    }
    
    public function getType() {
        return $this->type;
    }

    public function setSecret_file($file_path) {
        $this->secret_file = $file_path;
    }

    public function getSecret_file() {
        return $this->secret_file;
    }

    public function setUser($user) {
        $this->user = $user; 
    }

    public function getUser() {
        return $this->user; 
    }
    
    public function setPassword($password) {
        $this->password = $password; 
    }

    public function getPassword() {
        return $this->password; 
    }

}

//$cred = new Credential();
