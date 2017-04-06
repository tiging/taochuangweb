<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_dc_signin_history extends discuz_table
{
	public function __construct() {
		$this->_table = 'dc_signin_history';
		$this->_pk    = 'id';

		parent::__construct();
	}
	public function count_by_where($where) {
		return ($where = (string)$where) ? DB::result_first('SELECT COUNT(*) FROM '.DB::table($this->_table).' WHERE '.$where) : 0;
	}

	public function fetch_all_by_where($where, $start = 0, $limit = 0) {
		$where = $where ? ' WHERE '.(string)$where : '';
		return DB::fetch_all('SELECT * FROM '.DB::table($this->_table).$where.' ORDER BY dateline DESC'.DB::limit($start, $limit));
	}
}

?>