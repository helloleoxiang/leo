<?php
/**
 * Created by PhpStorm.
 * User: xiemin
 * Date: 2015/4/15
 * Time: 13:17
 */


class TestReadInetdb extends  WorkerProcess {
	protected $start_time;
	protected $spend_time;

	public function __construct()
	{
		/* 这里设置这个类是否被执行 */
		$this->setIsRun(true);
		parent::__construct();
	}
	public function __destruct()
	{

	}

	public function Run()
	{
		log_info("process is starting");
		$this->startProcess();
	}

	public function startProcess()
	{
		global $conf_store;
		$memc = new MutilProcessMemc($conf_store);
		$min = $this->task_data['min'];
		$max = $this->task_data['max'];
		log_info("min[$min], max[$max]");
		$gap = ($max - $min) / 2;
		$compareNum = $min + $gap;
		$startTime = $this->microtime_float();
		$success = 0;
		$fail = 0;
		for ($i = $min; $i <= $max ; $i++) {
			if ($i >= $compareNum && empty($this->ioInfo)){
				$this->getIOInfo('java');
			}
			$ret = $memc->Get($i);
			if (!$ret) {
				$fail++;
			} else {
				$success++;
			}
		}
		$endTime = $this->microtime_float();
		$cost = $endTime - $startTime;
		$num = $max - $min + 1;
		$this->record(__CLASS__, __FUNCTION__, $num , $cost, $success, $fail);
	}

	protected function getStoreRes() {
		global $conf_store;
		$memc = new MutilProcessMemc($conf_store);
		$ret = $memc->getStatus();
		$ret = array_shift($ret);
		$res = ceil($ret['bytes'] / 1024 / 1024);
		return $res;
	}
}
