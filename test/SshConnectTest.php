<?php

	error_reporting( E_ALL );
	ini_set('display_errors', 1);

	echo "hello world";
  require_once ("../model/SshConnect.php");
	$connection = new SSHConnect();
	$connection->connect();

	$connection->exec('powershell.exe "Set-ADAccountPassword -Identity testuser -Reset -NewPassword (ConvertTo-SecureString -AsPlainText Password01 -Force)"');



?>
