<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_dc_signin_emot extends discuz_table
{

	public function __construct() {

		$this->_table = 'dc_signin_emot';
		$this->_pk    = 'id';

		parent::__construct();
	}
	public function getdata(){
		return DB::fetch_all('SELECT * FROM '.DB::table($this->_table).' ORDER BY displayorder ASC', null, $this->_pk);
	}
	public function getdatabyxq(){
		return DB::fetch_all('SELECT * FROM '.DB::table($this->_table).' ORDER BY displayorder ASC', null, 'qdxq');
	}
	public function getrand(){
		return DB::fetch_first('SELECT * FROM '.DB::table($this->_table).' ORDER BY RAND() LIMIT 1');
	}
}

?>