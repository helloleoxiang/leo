<?php

/**
 * Created by PhpStorm.
 * User: wyf
 * Date: 2015/4/7
 * Time: 21:24
 */
class MutilProcessMemc
{
	public $memc;
	public $conf;
	const try_times = 3;
	const sleep_time = 5;

	public function __construct($conf)
	{
		$this->_conf = $conf;
		$this->init();
	}

	public function init() {
		$this->memc = new \Memcached();
		$this->memc->addserver($this->_conf['memcache']['host'], $this->_conf['memcache']['port']);
	}

	public function __destruct()
	{

	}

	public function Set($key, $val)
	{
		try {
			$ret = $this->memc->set($key, $val);
			if ($ret === false) {
				log_info("memcache set failed key: $key");
				$this->init();
				$ret = $this->memc->set($key, $val);
			}
		} catch (Exception $e) {
				log_info("memcache set failed key: " . $e->getMessage());
				$this->init();
			return false;
		}
		return $ret;
	}

	public function Get($key)
	{
		try {
			$ret = $this->memc->get($key);
			if ($ret === false) {
				log_info("memcache get failed key: $key");
				log_info("memcache get failed key: " . print_r($this->memc->getStats(), true));
				$this->init();
				$ret = $this->memc->get($key);
			}
		} catch (Exception $e) {
				log_info("memcache get failed key: " . $e->getMessage());
		$this->init();
			return false;
		}
		return $ret;
	}
	//    public function close()
	//    {
	//        $this->memc->close();
	//    }

	public function getStatus()
	{
		$ret = $this->memc->getStats();
		if ($ret === false) {
			log_info("memcache get stats failed key: $key");
			$this->init();
			$ret = $this->memc->get($key);
		}
		return $ret;
	}

	public function reConnect($k)
	{
		for ($i = 1; $i <= self::try_times; $i++) {
			unset($this->memc);
			\mynamespace\log_info("key: $k, sleep...");
			sleep(self::sleep_time * $i);
			$this->memc = new \mynamespace\MyMemc($this->conf);
			$ret = $this->memc->get($k);
			if ($ret !== false) {
				return $ret;
			}
		}
		return false;
	}
}
