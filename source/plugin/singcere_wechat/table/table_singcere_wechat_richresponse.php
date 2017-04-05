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

class table_singcere_wechat_richresponse extends discuz_table {

    public function __construct() {
        $this->_table = 'singcere_wechat_richresponse';
        $this->_pk = 'id';
        parent::__construct();
    }

    public function fetch_all_by_cmdid($cmdid) {
        if (!is_array($cmdid)) {
            $cmdid = array($cmdid);
        }
        return DB::fetch_all("SELECT * FROM %t WHERE cmdid IN(%n)", array($this->_table, $cmdid));
    }

}