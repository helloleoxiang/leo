<?php
/**
 * Created by PhpStorm.
 * User: wyf
 * Date: 2015/4/7
 * Time: 21:01
 */



class MutilProcessMongo {
	public $mongo;
	public $cur_collect;
	public $db = "imcms";
	public $collection = "doc_1361";
	public function __construct($conf)
	{
		do {
			try {
				$host = $conf['mongo']['host'];
				$port = $conf['mongo']['port'];
				$options = array('socketTimeoutMS' => 0, 'connectTimeoutMS' => 0);
				$this->mongo = new MongoClient("mongodb://$host:$port", $options);
				$this->cur_collect = $this->getCollection($this->db, $this->collection);
			} catch (Exception $e) {
				var_dump($e->getMessage());
				$this->mongo = false;
				sleep(1);
			}
		} while (empty($this->mongo));
	}

	public function __destruct()
	{

	}

	public function setCollection($db, $collection)
	{
		$this->db = $db;
		$this->collection = $collection;
	}

	public function getCollection($db, $collection)
	{
		return $this->mongo->selectCollection($db, $collection);
	}

	public function insert($arr)
	{
		try {
			$ret_arr = $this->cur_collect->save($arr);
			if (is_array($ret_arr) || $ret_arr === true) {
				return true;
			}
			return false;
		} catch (Exception $e) {
			var_dump($e->getMessage());
			return false;
		}
	}

	public function update($where, $content, $tag = null)
	{
		try {
			if ($tag === null) {
				return $this->cur_collect->update($where, $content);
			}
			else {
				return $this->cur_collect->update($where, $content, $tag);
			}
		} catch (Exception $e) {
			return false;
		}
	}

	public function findOne($where)
	{
		try {
			return $this->cur_collect->findOne($where);
		} catch (Exception $e) {
			var_dump($e->getMessage());
			return false;
		}
	}



}
