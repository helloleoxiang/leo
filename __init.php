<?php
/**
 * Created by PhpStorm.
 * User: xiemin
 * Date: 2015/4/15
 * Time: 16:11
 */
@ini_set('memory_limit', '1024M');
@set_time_limit(0);
date_default_timezone_set('Asia/Shanghai');

define('BASE_WORK', __DIR__);
//$conf_menu = array(
//    'libpath'   =>  BASE_WORK."/lib",
//    'workerpath' =>  BASE_WORK."/workerdir"
//);

foreach (glob(BASE_WORK."/config/*") as $php_file) {
    require_once $php_file;
}

foreach (glob(BASE_WORK."/lib/abstract/*") as $php_file) {
    require_once($php_file);
}

foreach (glob(BASE_WORK."/lib/db/*") as $php_file) {
    require_once($php_file);
}

foreach (glob(BASE_WORK."/lib/log/*") as $php_file) {
    require_once($php_file);
}

foreach (glob(BASE_WORK."/workerdir/*") as $php_file) {
    require_once($php_file);
}

?>