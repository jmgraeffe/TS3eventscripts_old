<?php
$config = array();

/* TS3 settings */

// nickname of query user (maybe the script needs to write text messages)
$config["name"]				= "Serverbot";

// ts3 server ip
$config["ts3"]["ip"] 		= "127.0.0.1";
// ts3 query port (default is 10011)
$config["ts3"]["port"] 		= 10011;
// ts3 virtual server id
$config["ts3"]["id"]		= 1;
// timeout, the maximum time which the script tooks to connect to the server
$config["ts3"]["timeout"] 	= 0.5;

// server query user (default is serveradmin)
$config["ts3"]["user"] 		= "serveradmin";
// server query user password
$config["ts3"]["pass"]		= "password";


/* MySQL/SQLite settings (for plugins with requirement of databases) */

// can be MySQL (=> "mysqlli") or SQLite 3 (=> "sqlite3")
$config["sql"]["system"] 	= "mysqli"; 
// SQL server host (default is localhost)
$config["sql"]["host"] 		= "localhost";
// SQL server port (default is 3306)
$config["sql"]["port"]		= 3306;
// SQL user name
$config["sql"]["user"] 		= "root";
// SQL password
$config["sql"]["pass"]		= "password";
// SQL database name
$config["sql"]["db"]		= "db01";

?>