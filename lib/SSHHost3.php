<?php

require_once('./phpseclib/phpseclib.php');

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

	function __construct ($host, $user, $pass){
		$this->host_ip = $host;
		$this->cur_dir="~";
		$this->ssh_connection = ssh2_connect($this->host_ip);

		if($this->ssh_connection == false) {
			__distruct();
        }
		
		$auth_ret = ssh2_auth_password($this->ssh_connection, $user, $pass);
		if($auth_ret == false) {
			__distruct();
        }
	}
	
	function exec($cmd){

        //$shell = ssh2_shell($this->ssh_connection, 'xterm');
        //fwrite($shell, 'cd /tmp && pwd;'.PHP_EOL);
        if (strstr($cmd, "&&")) {
		    $stream = ssh2_exec($this->ssh_connection, $cmd);
        }
		stream_set_blocking($stream, TRUE);
		$cmd_ret = fread($stream, 4096);
		fclose($stream);
		return $cmd_ret;
	}
}

$host = new SSHHost('10.0.5.107', 'dlx0294', "magima@1");
echo $host->exec();

