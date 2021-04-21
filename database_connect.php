<?php

$DATABASE = array(
	"host"          => "localhost",
	"port"          => 0,
	"username"      => "root",
	"password"      => "",
	"name"		=> "hyperloop",
	"debug"		=> TRUE,
);

$dbh = new PDO(
	'mysql:host='.$DATABASE["host"].';port='.$DATABASE["port"].';dbname='.$DATABASE["name"].'; charset=utf8', $DATABASE["username"], $DATABASE["password"]
			);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
$dbh->setAttribute(PDO::ATTR_TIMEOUT, 1);
$dbh->setAttribute(PDO::ATTR_PERSISTENT, 1);