<?php

/**
 *      (C)2007-2012 singcere.net
 *      This is a freeware, But you have no right to modify or distribute
 *      无忌源码  https://shop149355769.taobao.com
 *      $Id$
 */

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class table_singcere_wechat_pay extends discuz_table
{

    public function __construct()
    {
        $this->_table = 'singcere_wechat_pay';
        $this->_pk = 'id';

        parent::__construct();
    }

    public function update_by_tradeno($out_trade_no, $data, $unbuffered = false)
    {
		return DB::update($this->_table, $data, array('out_trade_no' => $out_trade_no), $unbuffered);
    }

    public function update_for_notify($out_trade_no, $data, $unbuffered = false)
    {
        return DB::update($this->_table, $data, array('out_trade_no' => $out_trade_no, 'transaction_id' => ''), $unbuffered);
    }

    public function fetch_by_transactionid($transactionid) {
        return DB::fetch_first("SELECT * FROM %t WHERE transaction_id = %s", array($this->_table, $transactionid));
    }

    public function search_condition($conditions, $prefix = '') {
        if($prefix) {
            $prefix = $prefix.'.';
        }

        if($conditions['unionid']) {
            $wherearr[] = $prefix.DB::field('unionid', $conditions['unionid']);
        }

        if($conditions['openid']) {
            $wherearr[] = $prefix.DB::field('openid', $conditions['openid']);
        }

        if($conditions['uid']) {
            $wherearr[] = $prefix.DB::field('uid', $conditions['uid']);
        }

        if($conditions['fromtype']) {
            $wherearr[] = $prefix.DB::field('fromtype', $conditions['fromtype']);
        }

        if($conditions['fromid']) {
            $wherearr[] = $prefix.DB::field('fromid', $conditions['fromid']);
        }

        if($conditions['senceid']) {
            $wherearr[] = $prefix.DB::field('senceid', $conditions['senceid']);
        }

        if($conditions['out_trade_no']) {
            $wherearr[] = $prefix.DB::field('out_trade_no', $conditions['out_trade_no']);
        }

        if($conditions['transaction_id']) {
            $wherearr[] = $prefix.DB::field('transaction_id', $conditions['transaction_id']);
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
