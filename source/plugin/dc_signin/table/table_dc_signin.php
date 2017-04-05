<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_dc_signin extends discuz_table
{
	public function __construct() {
		$this->_table = 'dc_signin';
		$this->_pk    = 'uid';

		parent::__construct();
	}
	public function getrange($start = 0,$limit = 20,$orderby='',$sort='DESC'){
		return DB::fetch_all('SELECT * FROM '.DB::table($this->_table).($orderby?' ORDER BY'.DB::order($orderby, $sort):'').DB::limit($start, $limit));
	}
	public function clearmonthdays(){
		DB::query('UPDATE '.DB::table($this->_table).' SET `monthdays` = 0,`monthcondays` = 0');
	}
	public function clearcondaydays(){
		$condateline = strtotime(dgmdate(TIMESTAMP,'Y-m-d'))-86400;
		DB::query('UPDATE '.DB::table($this->_table).' SET `monthcondays` = 0,`condays` = 0 WHERE `dateline`<'.$condateline);
	}
}

?>