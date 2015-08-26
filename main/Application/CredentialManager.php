<?php

/************************************************************************
 *
 *  Copyright (C) 2010-2015 Magima Co Ltd. All rights reserved.
 *  
 *  @description
 *  @file           CredentialManager.php
 *  @author         0294
 *  @created date   2015-02-28 13:43
 *  @version        1.0
 *
 ************************************************************************
 */

//function __autoload($class_name) {
//    require_once "$class_name" . ".php";
//}
require_once("../CommonLib/metaData.php");
require_once("Credential.php");

class CredentialManager {

    private $credentials = array();   

    public function __construct() {
        $config_datas = $this->parseCredentialConfig(CREDENTIAL_PATH);
        foreach ($config_datas as $key => $value ) {
            $credential = new Credential();

            $credential->setDescription($value["description"]);
            $credential->setCred_id($value["cred_id"]);
            $credential->setType($value["type"]);
            $credential->setSecret_file($value["secret_file"]);
            $this->credentails[$key] = $credential;
        }
        //print_r($this->credentails);
    }

    public function parseCredentialConfig($config_path) {
        if ( file_exists($config_path)) {
            $creds_conf = parse_ini_file($config_path, true);
            return $creds_conf;
        } else {
            throw new Exception(__CLASS__ . " : $config_path not exists!");
            return false;
        }
    }

    public function querryCredentialByKey($key) {
        foreach( $this->credentails as $credentialName => $value) {
            if ($key == $credentialName) {
                return $value;
            }
        }
    }

}

//$cred = new CredentialManager();
//print_r($cred);
//print_r($cred->parseCredentialConfig(CREDENTIAL_PATH));
