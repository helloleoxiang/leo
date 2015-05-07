<?php
/**
 *
 * Created by PhpStorm.
 * User: wyf
 * Date: 2015/4/7
 * Time: 20:41
 */

$conf_store = array(
//    "mysql" => array(
//                'host'      => '211.151.61.104',
//                'user'      => 'xiemin',
//                'pass'      => 'NT3LTbgFKkGyeWsR11134',
//                'dbname'    => 'imcms',
//                'port'      => '3306'
//    ),
//    "mysql" => array(
//                'host'      => '211.151.61.109',
//                'user'      => 'root',
//                'pass'      => 'Ifeng888',
//                'dbname'    => 'imcms',
//                'port'      => '3306'
//    ),

    "mysql" => array(
        'host' => '10.50.3.183',
        'user' => 'root',
        'pass' => 'Ifeng888',
        'dbname' => 'imcms',
        'port' => '3306'
    ),


    "mongo" => array(
        'host' => 'localhost',
        //'host' => '10.50.3.183',
        //'host' => '172.30.204.96',
        'port' => '27017'
    ),

    "memcache" => array(
        'host' => 'localhost',
        //'host' => '10.50.3.183',
        //'host' => '172.30.204.96',
        'port' => '11211'
    ),

    "memcacheq" => array(
        //'host' => 'localhost',
        'host' => '10.50.3.183',
        'port' => '22201'
    )
);

?>
