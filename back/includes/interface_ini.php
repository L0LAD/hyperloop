<?php

$d = __FILE__;
$d = dirname($d);


require_once("$d/libraries/BladeOne.php");
require_once("$d/libraries/BladeOneHtml.php");
$d = dirname($d);

require_once("$d/includes/interface_ini.php");



use eftec\bladeone\BladeOne;
use eftec\bladeonehtml\BladeOneHtml;

class bladeImport extends BladeOne
{
}

$d = dirname($d);
$views = $d . '/resources';
$cache = $d . '/resources/cache';

$blade = new bladeImport($views, $cache, BladeOne::MODE_DEBUG); // MODE_DEBUG allows to pinpoint troubles.
