<?php

# file name:	AuthenticationManager.php
# author:		dlx0294
# create date:	2015-04-21 16:08:44
# version:		1.0

require_once('../CommonLib/metaData.php');

class Authentication {
	public $ipaddr;
	public $hostname;
    public $username;
	public $password;
    
    public function __construct($ipaddr = Null , $hostname = NUll, 
                                $username = NULL, $password = NULL) {
        $this->ipaddr = $ipaddr;
        $this->hostname = $hostname;
        $this->username = $username;
        $this->password = $password;
    }
}

class AuthenticationManager {

    private static $auths = array(); 

    public function __construct() {
        foreach ($this->readConfig() as $item ) {
            
            $auth = new Authentication($ipaddr = $item['ip'], 
                                       $hostname = $item['hostname'], 
                                       $username = $item['username'],
                                       $password = $item['password']);
            $this->auths[] = $auth;
        }
        print_r($this->auths);
    }   
    
    public function querryAuthByIp($ip = NULL) {
        foreach($this->auths as $auth) {
            if ($auth->ipaddr == $ip) {
                return $auth;
            }
        }
    }
    
    public function readConfig($path = AUTHENTICATION_PATH) {
        if (file_exists($path)) {
            return parse_ini_file($path, true);
        }
    }
}

