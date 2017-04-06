<?php

/**
 *      (C)2007-2012 singcere.net
 *      This is a freeware, But you have no right to modify or distribute
 *      $Id$
 */

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class table_singcere_wechat_redpack extends discuz_table
{

    public function __construct()
    {
        $this->_table = 'singcere_wechat_redpack';
        $this->_pk = 'id';

        parent::__construct();
    }

    public function update_by_billno($mch_billno, $data, $unbuffered = false)
    {
		return DB::update($this->_table, $data, array('mch_billno' => $mch_billno), $unbuffered);
    }

    public function fetch_by_billno($mch_billno) {
        return DB::fetch_first("SELECT * FROM %t WHERE mch_billno = %s", array($this->_table, $mch_billno));
    }

    public function search_condition($conditions, $prefix = '') {
        if($prefix) {
            $prefix = $prefix.'.';
        }

        if($conditions['re_openid']) {
            $wherearr[] = $prefix.DB::field('re_openid', $conditions['re_openid']);
        }

        if($conditions['mch_billno']) {
            $wherearr[] = $prefix.DB::field('mch_billno', $conditions['mch_billno']);
        }

        if($conditions['fromtype']) {
            $wherearr[] = $prefix.DB::field('fromtype', $conditions['fromtype']);
        }

        if($conditions['fromid']) {
            $wherearr[] = $prefix.DB::field('fromid', $conditions['fromid']);
        }

        if($conditions['send_listid']) {
            $wherearr[] = $prefix.DB::field('send_listid', $conditions['send_listid']);
        }

        $wheresql = !empty($wherearr) && is_array($wherearr) ? ' WHERE ' . implode(' AND ', $wherearr) : '';
        return $wheresql;
    }

    public function count_by_search($conditions) {
        return DB::result_first("SELECT COUNT(*) FROM %t %i", array($this->_table, $this->search_condition($conditions)));
    }

    public function fetch_all_by_search($conditions, $orderby, $start, $limit) {
        return DB::fetch_all("SELECT * FROM %t %i ".($orderby ? " ORDER BY ".$orderby : '').DB::limit($start, $limit), array($this->_table, $this->search_condition($conditions)), $this->_pk);
    }
}
