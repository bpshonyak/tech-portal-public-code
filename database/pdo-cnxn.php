<?php
$config = array(
   "db" => "*********",
   "username" => "*********",
   "password" => "*********"
);

return new PDO($config["db"], $config["username"], $config["password"]);
?>
