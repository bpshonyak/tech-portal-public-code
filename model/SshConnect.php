<?php

class SSHConnect{

	//fields
	private $ssh_host = '*********';
	private $ssh_port = 22;
	private $ssh_auth_user = '*********';
	private $ssh_auth_pass = '*********';
	private $ssh_server_fp = '*********';
	private $connection;

	public function __construct(){

	}

	/**
	 *Creates an SSH connection
	 */
	public function connect() {
		if (!($this->connection = ssh2_connect($this->ssh_host, $this->ssh_port))){
			throw new Exception('Cannot connect to server');
		}
		$fingerprint = ssh2_fingerprint($this->connection, SSH2_FINGERPRINT_MD5 | SSH2_FINGERPRINT_HEX);
		if (strcmp($this->ssh_server_fp, $fingerprint) !== 0) {
			throw new Exception('Unable to verify server identity!');
		}
		if (!ssh2_auth_password($this->connection, $this->ssh_auth_user, $this->ssh_auth_pass)){
			throw new Exception('Authentication rejected by server');
		}
	}

	/**
	 *Executes commands over an SSH connection
	 *@param string $cmd Command to execute
	 */
	public function exec($cmd) {
  	if (!($stream = ssh2_exec($this->connection, $cmd))) {
      throw new Exception('SSH command failed');
  	}
  	stream_set_blocking($stream, true);
  	$data = "";
  	while ($buf = fread($stream, 4096)) {
   	 	$data .= $buf;
 	 	}
  	fclose($stream);
  	return $data;
	}

	/**
	 *Closes SSH connection
	 */
	public function disconnect() {
  	$this->exec('echo "EXITING" && exit;');
  	$this->connection = null;
	}

	/**
	 *Close the SSH connection when this code finishes executing
	 */
	public function __destruct() {
		$this->disconnect();
	}
}

?>
