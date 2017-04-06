<?php

/**
 *      (C)2007-2012 singcere.net
 *      This is a freeware, But you have no right to modify or distribute
 *      无忌源码  https://shop149355769.taobao.com
 *      $Id$
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_singcere_wechat_authcode extends discuz_table {

	public function __construct() {
		$this->_table = 'singcere_wechat_authcode';
		$this->_pk = 'sid';

		parent::__construct();
	}

	public function fetch_by_code($code) {
		return DB::fetch_first('SELECT * FROM %t WHERE code=%d', array($this->_table, $code));
	}

	public function delete_history() {
		$time = TIMESTAMP - 3600;
		return DB::delete($this->_table, "createtime<$time");
	}

}
