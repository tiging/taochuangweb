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

class table_singcere_wechat_tmplmsg extends discuz_table {

    public function __construct() {
        $this->_table = 'singcere_wechat_tmplmsg';
        $this->_pk = 'msgid';
        parent::__construct();
        
    }

    public function fetch_all($start, $limit) {
        return DB::fetch_all("SELECT * FROM %t ORDER BY dateline DESC ".DB::limit($start, $limit), array($this->_table), $this->_pk);
    }
    
    public function fetch_first_by_openid($openid) {
        return DB::fetch_first("SELECT * FROM %t WHERE openid = %s ORDER BY dateline DESC", array($this->_table, $openid));
    }
}