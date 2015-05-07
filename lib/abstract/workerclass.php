<?php
/**
 * Created by PhpStorm.
 * User: xiemin
 * Date: 2015/4/15
 * Time: 11:55
 */
abstract class WorkerProcess
{
	protected  $isrun = false;
	protected $task_data;
	protected static $taskAttrs = array();
	protected $ioInfo;
	public function __construct()
	{
		global $conf_store;
		$memc = new MutilProcessMemcq($conf_store);
		$this->task_data = (array) json_decode($memc->Get($memc->getQueueName()));

	}

	public function __destruct()
	{

	}

	public function beginRun()
	{
	}

	public function getTaskData() {
		return $this->task_data;	
	}

	public function realRun($task_data)
	{
		//多进程多任务的时候 第一个任务取完了 保存给后续任务使用
		$this->task_data = $task_data;
		if ($this->isrun === true) {
			$this->beginRun();
			$this->Run();
			$this->afterRun();
		}
	}

	public function setAttrs($k, $v) {
		self::$taskAttrs[$k] = $v;
	}

	public function Run()
	{

	}

	public function afterRun()
	{
		unset($this->task_data);
	}

	public function setIsRun($isrun)
	{
		if (isset($this->isrun)) {
			$this->isrun = $isrun;
		}
		else {
			$this->isrun = false;
		}
	}

	protected function getIOInfo($field) {
		$i = 0;
		do {
			$ret = exec("/usr/bin/top -n 1 -b -a | grep ".$field." | head -n 1");
			$i++;
		} while ($i <=2 && empty($ret));
		var_dump($ret);
		$this->ioInfo = trim($ret);
	}

	public function  microtime_float () {
		list( $usec ,  $sec ) =  explode ( " " ,  microtime ());
		return ((float) $usec  + (float) $sec );
	}

	protected function record($className, $funcName, $dataNum, $cost, $success, $fail) {
		$ioAr = preg_split("/\s+/", $this->ioInfo);
		$cpu = $ioAr[8];
		$cpu or $cpu = 0;
		$mem = $ioAr[9];
		$mem or $mem = 0;
		$res = $this->getStoreRes();
		$str =  '['.self::$taskAttrs['c'] . '_' .self::$taskAttrs['n'].']' .' ' . $className.'_'.$funcName . ' ' . $res .' '. $cpu .' ' . $mem.' '. $dataNum .' '. $cost .' '. $success .' '. $fail;	
		var_dump($str);
		write_log('INFO', $str, 'record_'.date("Ymd").'.log');
	}

	public function testData() {
		$data = '{"title":"\u6c7d\u8f66\u65b0\u5e02\u573a \u4e2d\u56fd\"\u4ef7\u683c\u6218\"\u906d\u53d7\u4fe1\u8a89\u5371\u673a","stitle":"","classid":"11000001","source":"\u7ecf\u6d4e\u53c2\u8003\u62a5","author":"","editor":"robot","message":"\t\u3000\u3000\u4ece\u8f7f\u8f66\u5e74\u9500\u4e94\u4e07\u591a\u8f86\uff0c\u5230\u4e0d\u5f97\u4e0d\u9eef\u7136\u9000\u573a\uff0c\u4e2d\u56fd\u6c7d\u8f66\u5728\u4fc4\u7f57\u65af\u5e02\u573a\u7684\u60b2\u6b22\u8d77\u843d\u53d1\u4eba\u6df1\u7701\uff1a\u201c\u4ef7\u683c\u6218\u201d\u4e0d\u4ec5\u6ca1\u80fd\u7ed9\u4e2d\u56fd\u6c7d\u8f66\u4f01\u4e1a\u5e26\u6765\u9884\u60f3\u7684\u6536\u76ca\uff0c\u53cd\u800c\u56e0\u8fdd\u80cc\u4e86\u201c\u89c4\u5219\u201d\u9010\u6e10\u5728\u6d77\u5916\u5e02\u573a\u906d\u53d7\u51b7\u9047\u3002<br\/>\n\n\u3000\u3000\u4e0d\u53ef\u5426\u8ba4\uff0c\u201c\u4ef7\u683c\u6218\u201d\u4e00\u76f4\u662f\u6211\u56fd\u4f01\u4e1a\u5728\u6d77\u5916\u5e02\u573a\u201c\u5f00\u7586\u8f9f\u571f\u201d\u7684\u6709\u6548\u65b9\u5f0f\uff0c\u52b3\u52a8\u529b\u3001\u539f\u6750\u6599\u7b49\u65b9\u9762\u7684\u6bd4\u8f83\u4f18\u52bf\uff0c\u4e00\u5ea6\u4f7f\u4e2d\u56fd\u6c7d\u8f66\u76f8\u4fe1\uff0c\u4ec5\u9760\u4f4e\u4ef7\u5c31\u80fd\u53d6\u5f97\u4e0d\u9519\u7684\u9500\u552e\u6210\u7ee9\u3002 \u7136\u800c\uff0c\u201c\u5175\u8d25\u4fc4\u7f57\u65af\u201d\u7684\u906d\u9047\uff0c\u65e0\u7591\u7ed9\u8e0c\u8e87\u6ee1\u5fd7\u7684\u4e2d\u56fd\u6c7d\u8f66\u4f01\u4e1a\u4e00\u8bb0\u5f53\u5934\u68d2\u3002<br\/>\n\n\u3000\u3000\u5728\u4fc4\u7f57\u65af\u9047\u51b7\u53ea\u662f\u4e2d\u56fd\u6c7d\u8f66\u51fa\u53e3\u6d77\u5916\u5883\u51b5\u7684\u4e00\u4e2a\u7f29\u5f71\uff0c\u76ee\u524d\uff0c\u5305\u62ec\u9a6c\u6765\u897f\u4e9a\u5728\u5185\u7684\u51e0\u4e2a\u4e3b\u8981\u6c7d\u8f66\u51fa\u53e3\u76ee\u7684\u56fd\u5df2\u53d8\u76f8\u6253\u51fa\u4e86\u4e2d\u56fd\u6c7d\u8f66\u201c\u7981\u5165\u4ee4\u201d\uff0c\u4e2d\u56fd\u6c7d\u8f66\u5728\u56fd\u9645\u5e02\u573a\u4e0a\u7684\u4fe1\u8a89\u4e0e\u5f62\u8c61\u6b63\u5728\u906d\u9047\u7a7a\u524d\u5371\u673a\u3002\u8be5\u4ee5\u600e\u6837\u7684\u65b9\u5f0f\u8fdb\u5165\u56fd\u9645\u5e02\u573a\uff0c\u5df2\u6210\u4e3a\u6574\u4e2a\u4e2d\u56fd\u6c7d\u8f66\u4ea7\u4e1a\u8feb\u5207\u9700\u8981\u53cd\u7701\u7684\u95ee\u9898\u3002<br\/>\n\n\u3000\u3000\u5f53\u524d\uff0c\u6211\u56fd\u51fa\u53e3\u7684\u6c7d\u8f66\u4e3b\u8981\u662f\u4f4e\u7aef\u3001\u7ecf\u6d4e\u578b\u8f7f\u8f66\uff0c\u5730\u57df\u76f8\u540c\u3001\u4ef7\u683c\u63a5\u8fd1\u3001\u4ea7\u54c1\u6863\u6b21\u76f8\u8fd1\uff0c\u8fd9\u5c31\u4f7f\u5f97\u4fc4\u7f57\u65af\u548c\u4e9a\u3001\u975e\u3001\u62c9\u7b49\u56fd\u5bb6\u548c\u5730\u533a\u6210\u4e3a\u6211\u56fd\u6c7d\u8f66\u4f01\u4e1a\u4ea4\u950b\u7684\u6218\u573a\u3002\u56e0\u6b64\uff0c\u624b\u63e1\u201c\u4ef7\u683c\u6b66\u5668\u201d\u7684\u4e2d\u56fd\u4f01\u4e1a\uff0c\u5728\u6d77\u5916\u7684\u5bf9\u624b\u51e0\u4e4e\u5168\u662f\u56fd\u5185\u540c\u884c\u3002\u7531\u4e8e\u4e2d\u56fd\u6c7d\u8f66\u4f01\u4e1a\u666e\u904d\u89c4\u6a21\u5c0f\u3001\u6570\u91cf\u591a\u3001\u7f3a\u4e4f\u521b\u65b0\u80fd\u529b\u4e0e\u6838\u5fc3\u7ade\u4e89\u529b\uff0c\u4ea7\u54c1\u9644\u52a0\u503c\u4f4e\uff0c\u5728\u6d77\u5916\u5e02\u573a\u4e0a\u4e00\u5473\u62fc\u4ef7\u683c\uff0c\u53d7\u5230\u635f\u4f24\u7684\u53ea\u80fd\u662f\u6574\u4e2a\u4e2d\u56fd\u7684\u6c7d\u8f66\u5de5\u4e1a\uff1b\u800c\u540c\u5ba4\u64cd\u6208\u66f4\u6709\u53ef\u80fd\u7ed9\u6574\u4e2a\u201c\u4e2d\u56fd\u5236\u9020\u201d\u7684\u5f62\u8c61\u8499\u4e0a\u4e00\u5c42\u4ef7\u5ec9\u8d28\u52a3\u7684\u9634\u5f71\u3002<br\/>\n\n\u3000\u3000\u4e8b\u5b9e\u4e0a\uff0c\u5728\u5168\u7403\u91d1\u878d\u5371\u673a\u53d1\u751f\u4e4b\u524d\uff0c\u7531\u4e8e\u4e00\u5473\u8ffd\u6c42\u5e02\u573a\u4efd\u989d\u7684\u589e\u957f\uff0c\u6211\u56fd\u6c7d\u8f66\u51fa\u53e3\u5c31\u5df2\u5448\u73b0\u51fa\u91cf\u589e\u4ef7\u8dcc\u7684\u6001\u52bf\u3002\u7531\u4e8e\u51fa\u53e3\u6c7d\u8f66\u201c\u540c\u8d28\u5316\u201d\u4e25\u91cd\uff0c\u5728\u6d77\u5916\u7684\u4ef7\u683c\u6218\u4e5f\u6108\u6f14\u6108\u70c8\uff0c\u867d\u7136\u56fd\u9645\u5e02\u573a\u7a7a\u95f4\u8d8a\u6765\u8d8a\u5927\uff0c\u6211\u56fd\u6c7d\u8f66\u51fa\u53e3\u7684\u4ef7\u683c\u5374\u8d8a\u6765\u8d8a\u4f4e\uff0c\u4f01\u4e1a\u5229\u6da6\u7a7a\u95f4\u8d8a\u6765\u8d8a\u5c0f\u3002<br\/>\n\n\u3000\u3000\u800c\u65e5\u672c\u6c7d\u8f66\u5728\u56fd\u9645\u5e02\u573a\u7684\u4efd\u989d\u53ea\u670920%\u5de6\u53f3\uff0c\u5c3d\u7ba1\u9762\u4e34\u5e2d\u5377\u5168\u7403\u7684\u91d1\u878d\u5371\u673a\uff0c\u4f46\u65e5\u672c\u6c7d\u8f66\u4f01\u4e1a\u5374\u62e5\u6709\u8f83\u9ad8\u7684\u5229\u6da6\u3002\u4e00\u4e2a\u91cd\u8981\u7684\u539f\u56e0\u662f\u65e5\u672c\u6c7d\u8f66\u4f01\u4e1a\u4e3a\u4fdd\u969c\u884c\u4e1a\u5229\u6da6\u7387\uff0c\u5b81\u613f\u8ba9\u4ea7\u54c1\u5728\u5e02\u573a\u4e0a\u4fdd\u6301\u76f8\u5bf9\u7a00\u7f3a\u7684\u72b6\u6001\uff0c\u8fd9\u503c\u5f97\u4e2d\u56fd\u6c7d\u8f66\u4f01\u4e1a\u8b66\u9192\u3002<br\/>\n\n\u3000\u3000\u76ee\u524d\uff0c\u53d7\u5168\u7403\u7ecf\u6d4e\u5f62\u52bf\u62d6\u7d2f\uff0c\u56fd\u9645\u6c7d\u8f66\u5e02\u573a\u840e\u7f29\u7684\u5c40\u9762\u4ecd\u7136\u6ca1\u6709\u5f97\u5230\u660e\u663e\u6539\u5584\uff0c\u800c\u4e0e\u6b64\u540c\u65f6\uff0c\u6211\u56fd\u52b3\u52a8\u529b\u6210\u672c\u5374\u4e0d\u65ad\u589e\u52a0\uff0c\u539f\u6750\u6599\u6da8\u4ef7\u5e26\u6765\u6210\u672c\u538b\u529b\u3001\u73af\u4fdd\u8981\u6c42\u65e5\u76ca\u63d0\u9ad8\u4ee5\u53ca\u4eba\u6c11\u5e01\u5347\u503c\u7b49\u4e0d\u5229\u56e0\u7d20\uff0c\u5df2\u8ba9\u201c\u4e2d\u56fd\u5236\u9020\u201d\u7684\u4ef7\u683c\u4f18\u52bf\u9010\u6e10\u4e27\u5931\uff0c\u4e2d\u56fd\u6c7d\u8f66\u4eca\u540e\u6050\u6015\u5f88\u96be\u518d\u9760\u5355\u7eaf\u7684\u4ef7\u683c\u6218\u5728\u56fd\u9645\u5e02\u573a\u751f\u5b58\u4e0b\u53bb\u3002<br\/>\n\n\u3000\u3000\u800c\u66f4\u4ee4\u4eba\u62c5\u5fe7\u7684\u662f\uff0c\u7531\u4e8e\u56fd\u9645\u7ade\u4e89\u52a0\u5267\uff0c\u4e0d\u5c11\u56fd\u5bb6\u8d38\u6613\u4fdd\u62a4\u4e3b\u4e49\u62ac\u5934\uff0c\u8feb\u4e8e\u672c\u56fd\u5236\u9020\u4e1a\u7684\u538b\u529b\uff0c\u503e\u5411\u4e8e\u628a\u53cd\u503e\u9500\u5408\u6cd5\u5316\u3001\u5236\u5ea6\u5316\uff0c\u540c\u65f6\u7b51\u8d77\u4e86\u5404\u79cd\u8d38\u6613\u58c1\u5792\u3002\u7279\u522b\u662f\u9488\u5bf9\u4e2d\u56fd\u4f01\u4e1a\u201c\u4ef7\u683c\u6218\u201d\u800c\u51fa\u53f0\u7684\u5404\u79cd\u6280\u672f\u58c1\u5792\uff0c\u5df2\u5f00\u59cb\u8ba9\u4e2d\u56fd\u6c7d\u8f66\u51fa\u53e3\u4e3e\u6b65\u7ef4\u8270\u3002\u56e0\u6b64\uff0c\u201c\u4e2d\u56fd\u5236\u9020\u201d\u5728\u56fd\u9645\u5e02\u573a\u9891\u9891\u201c\u89e6\u96f7\u201d\u51b3\u975e\u5076\u7136\u73b0\u8c61\u3002<br\/>\n\n\u3000\u3000\u6d77\u5916\u76c8\u5229\u7684\u9053\u8def\u773c\u770b\u8d8a\u8d70\u8d8a\u7a84\uff0c\u4e2d\u56fd\u6c7d\u8f66\u4f01\u4e1a\u8981\u201c\u8d70\u51fa\u53bb\u201d\uff0c\u8def\u5728\u4f55\u65b9\uff1f<br\/>\n\n\u3000\u3000\u76ee\u524d\uff0c\u5728\u56fd\u9645\u5e02\u573a\u7684\u7ade\u4e89\u4e2d\uff0c\u4ef7\u683c\u5e76\u975e\u4e3b\u8981\u7684\u624b\u6bb5\uff0c\u5404\u56fd\u6c7d\u8f66\u4f01\u4e1a\u7eb7\u7eb7\u5728\u4ea7\u54c1\u7684\u6280\u672f\u542b\u91cf\u3001\u8d28\u91cf\u3001\u670d\u52a1\u4e0a\u4e0b\u529f\u592b\u3002\u6b63\u56e0\u5982\u6b64\uff0c\u6211\u56fd\u6c7d\u8f66\u51fa\u53e3\u76ee\u524d\u8fd8\u65e0\u529b\u8fdb\u519b\u6b27\u6d32\u3001\u5317\u7f8e\u7b49\u9ad8\u7aef\u5e02\u573a\uff0c\u5f71\u54cd\u529b\u4e5f\u53ea\u9650\u4e8e\u4e9a\u3001\u975e\u3001\u62c9\u7f8e\u7b49\u53d1\u5c55\u4e2d\u56fd\u5bb6\u3002<br\/>\n\n\u3000\u3000\u5168\u7403\u7ecf\u6d4e\u5371\u673a\u4e0d\u4ec5\u7ed9\u6211\u56fd\u6c7d\u8f66\u4f01\u4e1a\u5e26\u6765\u51b2\u51fb\uff0c\u4e5f\u4f7f\u4e16\u754c\u6c7d\u8f66\u4ea7\u4e1a\u7684\u683c\u5c40\u53d1\u751f\u4e86\u53d8\u5316\u3002\u5bf9\u4e8e\u4e2d\u56fd\u6c7d\u8f66\u800c\u8a00\uff0c\u4f7f\u7528\u4ef7\u683c\u4f18\u52bf\u6253\u5f00\u6d77\u5916\u5e02\u573a\uff0c\u5e94\u8be5\u53ea\u662f\u7b2c\u4e00\u6b65\uff0c\u4e4b\u540e\u5e94\u8be5\u66f4\u52a0\u52aa\u529b\u5730\u63d0\u9ad8\u4ea7\u54c1\u8d28\u91cf\u3001\u552e\u540e\u670d\u52a1\u6c34\u5e73\u548c\u54c1\u724c\u77e5\u540d\u5ea6\uff0c\u5728\u56fd\u9645\u5e02\u573a\u4e0a\u5de9\u56fa\u548c\u6269\u5927\u5df2\u6709\u7684\u5730\u4f4d\u3002\u6211\u56fd\u6c7d\u8f66\u51fa\u53e3\u4f01\u4e1a\u5728\u575a\u6301\u201c\u8d70\u51fa\u53bb\u201d\u6218\u7565\u7684\u540c\u65f6\uff0c\u66f4\u8981\u4ece\u8c03\u7ed3\u6784\u3001\u8f6c\u53d8\u589e\u957f\u65b9\u5f0f\u3001\u589e\u5f3a\u81ea\u4e3b\u521b\u65b0\u80fd\u529b\u3001\u8d28\u91cf\u4e0e\u670d\u52a1\u7b49\u65b9\u9762\u7740\u624b\u5e94\u5bf9\u5e02\u573a\u7684\u53d8\u5316\u3002<br\/>\n\n\u3000\u3000\u4ece\u4e2d\u56fd\u6c7d\u8f66\u201c\u5175\u8d25\u4fc4\u7f57\u65af\u201d\u7684\u8fc7\u7a0b\u4e2d\u4e0d\u96be\u770b\u51fa\uff0c\u5728\u98ce\u4e91\u53d8\u5e7b\u7684\u56fd\u9645\u5e02\u573a\u4e0a\uff0c\u6211\u56fd\u4f01\u4e1a\u6614\u65e5\u60ef\u7528\u7684\u4ef7\u683c\u6b66\u5668\u5df2\u975e\u5236\u80dc\u5229\u5668\uff0c\u5f00\u62d3\u65b0\u5e02\u573a\uff0c\u201c\u4e0e\u72fc\u5171\u821e\u201d\u66f4\u9700\u8981\u6709\u8fc7\u786c\u7684\u8d28\u91cf\u548c\u4f18\u8d28\u7684\u552e\u540e\u670d\u52a1\u3002\u5982\u679c\u5ffd\u89c6\u4e86\u8fd9\u4e00\u70b9\uff0c\u5373\u4fbf\u5982\u4e30\u7530\u3001\u901a\u7528\u8fd9\u6837\u7684\u767e\u5e74\u8001\u5e97\u4e5f\u96be\u9003\u4e22\u6389\u5df2\u6709\u5e02\u573a\u7684\u547d\u8fd0\u3002<br\/>\n\n\u3000\u3000\u65e0\u8bba\u5982\u4f55\uff0c\u9762\u5bf9\u7c7b\u4f3c\u201c\u5175\u8d25\u4fc4\u7f57\u65af\u201d\u7684\u4e8b\u4ef6\u9891\u9891\u53d1\u751f\uff0c\u4e2d\u56fd\u4f01\u4e1a\u7edd\u4e0d\u5e94\u89c6\u800c\u4e0d\u89c1\uff0c\u5176\u4e2d\u7684\u6559\u8bad\uff0c\u540e\u6765\u8005\u5728\u5f00\u62d3\u201c\u65b0\u5e02\u573a\u201d\u7684\u8fc7\u7a0b\u4e2d\u4e0d\u53ef\u4e0d\u9274\uff1a\u53ea\u6709\u4f7f\u201c\u4ef7\u683c\u6218\u201d\u8ba9\u4f4d\u4e8e\u6280\u672f\u6218\u3001\u670d\u52a1\u6218\u3001\u8d28\u91cf\u6218\u548c\u54c1\u724c\u6218\u624d\u80fd\u6709\u6548\u5851\u9020\u4e2d\u56fd\u6c7d\u8f66\u7684\u5f62\u8c61\uff0c\u624d\u80fd\u4f7f\u201c\u4e2d\u56fd\u5236\u9020\u201d\u6210\u4e3a\u56fd\u9645\u5e02\u573a\u4e0a\u7684\u91d1\u5b57\u62db\u724c\u3002<br\/>\n\n\u3000\u3000\u4e2d\u56fd\u901a\u5411\u6c7d\u8f66\u5f3a\u56fd\u7684\u8def\u8fd8\u5f88\u9065\u8fdc\uff0c\u5f53\u524d\uff0c\u6574\u4e2a\u4e2d\u56fd\u6c7d\u8f66\u884c\u4e1a\u638c\u63e1\u7684\u6838\u5fc3\u6280\u672f\u4e5f\u975e\u5e38\u6709\u9650\uff0c\u66f4\u6ca1\u6709\u8db3\u4ee5\u4e0e\u5927\u4f17\u3001\u798f\u7279\u7b49\u4e16\u754c\u5de8\u5934\u6297\u8861\u7684\u5927\u578b\u4f01\u4e1a\u548c\u54c1\u724c\u3002\u9762\u5bf9\u6d77\u5916\u5e02\u573a\uff0c\u56fd\u5185\u6c7d\u8f66\u4f01\u4e1a\u51b3\u4e0d\u53ef\u6025\u529f\u8fd1\u5229\uff0c\u9700\u8981\u4e00\u6b65\u4e00\u4e2a\u811a\u5370\u5730\u5f80\u524d\u8d70\uff0c\u5e02\u573a\u5fc5\u5b9a\u4f1a\u7ed9\u201c\u4e2d\u56fd\u5236\u9020\u201d\u4e00\u4e2a\u516c\u9053\u3002<br\/>\n\n\u3000\u3000(\u7ecf\u6d4e\u53c2\u8003\u62a5)<br\/>\n","keywords":"","status":"1","online":-1,"wwwurl":"http:\/\/auto.ifeng.com\/roll\/20100514\/308041.shtml","ext":[],"articleid":"1010308041","createtime":"2010-05-14 09:46:00","sort":"100","sorttime":"1273801560","img":[],"newstype":0,"id":"4001318","preId":"4001319","preTitle":"\u8d28\u68c0\u603b\u5c40\u89e3\u9664\u8fdb\u53e3\u79d1\u5e15\u5947\u4e25\u91cd\u5b89\u5168\u98ce\u9669\u8b66\u544a","preStitle":"","nextId":4001315,"nextTitle":"4\u6708\u9500\u91cf\u73af\u6bd4\u4e0b\u964d \u65e5\u7cfb\u8f66\u6210\u6700\u5927\u53d7\u5bb3\u8005","nextStitle":"","updatetime":"2014-02-27 17:05:08"}';
		return $data;
	}
}
