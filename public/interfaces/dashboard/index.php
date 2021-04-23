<?php
$d = __FILE__;
$d = dirname($d);
$d = dirname($d);
$d = dirname($d);
$d = dirname($d);
require_once("$d/back/includes/interface_ini.php");

echo $blade->run(
  "layouts.dashboard.dashboard",
  array()
);
