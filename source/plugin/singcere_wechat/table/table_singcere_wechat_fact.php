<?php

/**
 *      (C)2007-2012 singcere.net
 *      This is a freeware, But you have no right to modify or distribute
 *
 *      $Id$
 */

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class table_singcere_wechat_fact extends discuz_table {

    public function __construct() {
        $this->_table = 'singcere_wechat_fact';
        $this->_pk = 'factid';
        parent::__construct();
    }
    
    public function delete_unused_by_openid($openid) {
        return DB::delete($this->_table, array('openid' => $openid, 'status' => 0));
    }
    
    public function fetch_unused_by_openid($openid) {
        return DB::fetch_first("SELECT * FROM %t WHERE openid = %s AND status = %d", array($this->_table, $openid, 0));
    }
    
    public function update_unused_by_openid($openid, $data) {
        return DB::update($this->_table, $data, array('openid' => $openid, 'status' => 0));
    }
}