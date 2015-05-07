<?php
/**
 * Created by PhpStorm.
 * User: wyf
 * Date: 2015/4/7
 * Time: 20:31
 */


class MutilProcessMysql {

    public $dbh;
    public function __construct($conf)
    {
        $host = $conf['mysql']['host'];
        $db_name = $conf['mysql']['dbname'];
        $user = $conf['mysql']['user'];
        $pass = $conf['mysql']['pass'];
        try {
            $dbh = new \PDO("mysql:host=$host;dbname=$db_name", $user, $pass);
            $this->dbh = $dbh;
        } catch (PDOException $e) {
            log_info("Error!: " . $e->getMessage());
            die();
        }
    }
    public  function __destruct()
    {

    }

    public function exec($sql)
    {
        try {
            $sth = $this->dbh->prepare($sql);
            $ret = $sth->execute();
            if ($ret === true) {
                /* select return array, other sql return NULL */
                return $sth->fetchall();
            }
            else {
                return -1;
            }
        }
        catch (PDOException $e) {
            log_error("Error!: " . $e->getMessage());
            return -1;
        }
    }



}