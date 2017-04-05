<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_dc_signin_group extends discuz_table
{

	public function __construct() {

		$this->_table = 'dc_signin_group';
		$this->_pk    = 'id';

		parent::__construct();
	}
	public function getdata(){
		return DB::fetch_all('SELECT * FROM '.DB::table($this->_table).' ORDER BY dayslower ASC', null, $this->_pk);
	}
	
	public function getgid($growth = 0){
		$growth = dintval($growth);
		return DB::fetch_first('SELECT * FROM '.DB::table($this->_table).' WHERE '.DB::field('dayslower',$growth,'<=').' ORDER BY dayslower DESC');
	}
}

?>