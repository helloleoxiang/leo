<?php
/*
 *
 * date: 20150115
 * author: xiemin
 * descript: 讲日志存入文件
 *
 *
 *
 */

date_default_timezone_set('Asia/Shanghai');

function write_log($level, $msg, $fileName = '')
{
    if (!file_exists("./log")) {
        mkdir ("./log", 777);
    }
	if ($fileName) 
    $pfile = "log/". $fileName;
	else
    $pfile = "log/".date("Ymd").".log";
    $fmt= "[".date("Y-m-d G:i:s")."] [".getmypid()."] [".$level."] %% - ".$msg.".\r\n" ;
    file_put_contents($pfile, $fmt,FILE_APPEND|LOCK_EX);
}
function log_info($msg)
{
    write_log("INFO", $msg);
}
function log_error($msg)
{
    write_log("ERROR", $msg);
}
function log_debug($msg)
{
    write_log("DEBUG", $msg);
}
function log_warn($msg)
{
    write_log("WARN", $msg);
}
?>
