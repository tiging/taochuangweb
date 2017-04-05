<?php

/**
 *      (C)2007-2012 singcere.net
 *      This is a freeware, But you have no right to modify or distribute
 *
 *      $Id$
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_singcere_wechat_cmd extends discuz_table {
	public function __construct() {
		$this->_table 	= 'singcere_wechat_cmd';
		$this->_pk		= 'id';
		parent::__construct();
	}
	
	public function fetch_all_by_type($type, $status = -1, $glue = '>=', $key = 'cmdname') {
		return DB::fetch_all("SELECT * FROM %t WHERE ".DB::field('status', $status, $glue)." AND type = %s ORDER BY displayorder DESC", array($this->_table, $type), $key);
	}
	
	public function fetch_all_by_status($status, $glue = '>=') {
		return DB::fetch_all("SELECT * FROM %t WHERE ".DB::field('status', $status, $glue)." ORDER BY displayorder DESC", array($this->_table));
	}
        
        public function exist_by_cmdname($cmdname) {
            return DB::result_first("SELECT COUNT(*) FROM %t WHERE cmdname = %s", array($this->_table, $cmdname));
        }
}