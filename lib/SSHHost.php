<?php

require_once('../../lib/phpseclib/phpseclib.php');
require_once('../CommonLib/Utils.php');
require_once('../CommonLib/GlobalLogger.php');

class SSHChannel {
	private $ssh_connection;
	private $ssh_session;
}

class SSHHost {
	private $host_ip;
	private $host_ssh_port;
	private $ssh_connection;
	private $ssh_session;
	private $cur_dir ;
	private $ssh_channel;
    private $date;
    private $logger;

	function __construct ($host, $user, $pass){
        $this->date = Utils::getDateInstance();
        $this->logger = GlobalLogger::getLoggerInstance();
		$this->host_ip = $host;
		//$this->cur_dir="~";
		$this->ssh_connection = ssh2_connect($this->host_ip);

		if($this->ssh_connection == false) {
            $errorInfo = "Your ipaddress is error!\n";
            $this->logger->addLog("ssh connect", 0, $errorInfo, $host);
            throw new Exception(Utils::red("lines: 25, $errorInfo"));
			//__distruct();
        }

		$auth_ret = ssh2_auth_password($this->ssh_connection, $user, $pass);
		if($auth_ret == false) {
            $errorInfo = "your username or password have not matched!";
            $this->logger->addLog("authentication", 0, $errorInfo, $host);
            throw new Exception(Utils::red("SSHHost.php:lines: 31, $errorInfo"));
			//__distruct();
        }
	}
	
	function exec($cmd){
        //echo $this->date->getCurrentTime() . " executing: " . $cmd . "\n";
        $executingInfo = "executing: $cmd";
        $this->logger->addLog("cmds", 100, $executingInfo, $this->host_ip);
		$stream = ssh2_exec($this->ssh_connection, $cmd );

        if ($stream == false) {
            $errorExecInfo = "execute cmds: $cmd error!";
            $this->logger->addLog("cmds", 0, $errorExecInfo, $this->host_ip);
            throw new Exception(Utils::red($errorExecInfo));
        }
        
        $errStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
		stream_set_blocking($stream, TRUE);
		stream_set_blocking($errStream, TRUE);

        $stdout =  stream_get_contents($stream);
        $stderr = stream_get_contents($errStream);
        if (strstr($cmd, "ftp") or strstr($cmd, "lftp")) {
            echo "\n" . $stdout . "\n";
            if(strstr($stdout, "530")) {
                echo Utils::red("\nftp login failed, please try again!\n");
            }
        }
        $this->logger->addLog("cmds", 100, $stdout, $this->host_ip);
       
        //echo $this->date->getCurrentTime() . " STDOUT: " . $stdout . "\n";
        
        if ($stderr != null || $stderr != "" )  { 
            if (strstr($stderr, "Permission denied")) {
                echo Utils::red("\nyou need higher authority!\n");
                exit();
            }
            echo $this->date->getCurrentTime() .  " WARNING: " . $stderr . "\n";
            $this->logger->addLog("cmds", 0, $stderr, $this->host_ip);
        }
        if (strstr($stderr, "Error:") || strstr($stderr, "Warning:") || (strstr($stderr, "Cannot"))) {
            throw new Exception(Utils::red("have error info please check your cmd is true!"));   
            
        }
        //if ($errStream != null ) {
        //    throw new Exception(Utils::red("execute cmds: $cmd error!"));
        //}
		fclose($stream);
		fclose($errStream);
		return $stdout;
	}
    
    function send($srcFilePath, $destFilePath) {
        if (ssh2_scp_send($this->ssh_connection, $srcFilePath, $destFilePath , 0644)) {
            echo "uploading $srcFilePath ok!\n";
        } else {
            echo "uploading $srcFilePath error!\n please check!\n";
        }
    }
}


