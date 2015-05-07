<?php

/**
 * Created by PhpStorm.
 * User: wyf
 * Date: 2015/4/7
 * Time: 21:24
 */
class MutilProcessMemcq
{
    public $memc;
    public $conf;
    public $queue_name = "process_task_mongotest";

    public function __construct($conf)
    {
        $this->memc = new Memcached();
        $this->conf = $conf;
        $this->memc->addserver($conf['memcacheq']['host'], $conf['memcacheq']['port']);
    }

    public function __destruct()
    {

    }

    public function Set($data)
    {
        $ret = $this->memc->set($this->getQueueName(), $data);
        if ($ret === false) {
            log_info("memcache set failed key");
        }
        return 0;
    }

    public function Get($queue_name)
    {
        $ret = $this->memc->get($queue_name);
        if ($ret === false) {
            log_info("memcache get failed key: $queue_name");
        }
        return $ret;
    }
//    public function close()
//    {
//        $this->memc->close();
//    }

    public function reConnect($k)
    {
//        for ($i = 1; $i <= self::try_times; $i++) {
//            unset($this->memc);
//            \mynamespace\log_info("key: $k, sleep...");
//            sleep(self::sleep_time * $i);
//            $this->memc = new \mynamespace\MyMemc($this->conf);
//            $ret = $this->memc->get($k);
//            if ($ret !== false) {
//                return $ret;
//            }
//        }
        return false;
    }

    public function getQueueName()
    {
        return $this->queue_name;
    }
}
