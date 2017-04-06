<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_dc_signin_llreward extends discuz_table
{
	public function __construct() {
		$this->_table = 'dc_signin_llreward';
		$this->_pk    = 'id';

		parent::__construct();
	}

}

?>