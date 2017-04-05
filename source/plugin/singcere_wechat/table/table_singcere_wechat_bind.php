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


class table_singcere_wechat_bind extends discuz_table {
	public function __construct() {
		$this->_table	= 'singcere_wechat_bind';
		$this->_pk		= 'id'; 
		parent::__construct();
	}
	
	public function fetch_by_uid($uid) {	
		return DB::fetch_first("SELECT * FROM %t WHERE uid = %d", array($this->_table, $uid));
	}
	
	public function fetch_by_openid($openid) {
		return DB::fetch_first("SELECT * FROM %t WHERE openid = %s", array($this->_table, $openid));
	}

	public function fetch_by_unionid($unionid) {
		return DB::fetch_first("SELECT * FROM %t WHERE unionid = %s", array($this->_table, $unionid));
	}
	
	public function delete_by_uid($uids) {
		return DB::delete($this->_table, DB::field('uid', $uids));
	}
	
	public function update_status($uid, $status) {
		return DB::update($this->_table, array('status' => $status), array('uid' => $uid), true);
	}
	
	public function fetch_all($start, $limit) {
		return DB::fetch_all("SELECT * FROM %t ORDER BY dateline DESC ".DB::limit($start, $limit), array($this->_table),'uid');
	}
	
	public function update_by_openid($openid, $data) {
	    return DB::update($this->_table, $data, array('openid' => $openid));
	}
	
	public function search_condition($conditions, $prefix = '') {
		if($prefix) {
			$prefix = $prefix.'.';
		}
		
		if($conditions['openid']) {
			$wherearr[] = $prefix.DB::field('openid', $conditions['openid']);
		}
		
		if($conditions['uid']) {
			$wherearr[] = $prefix.DB::field('uid', $conditions['uid']);
		}
		
		if($conditions['sex']) {
			$wherearr[] = $prefix.DB::field('sex', $conditions['sex']);
		}
		
		if($conditions['counts']) {
			$wherearr[] = $prefix.DB::field('counts', $conditions['counts'], '>=');
		}
		
		if($conditions['subscribe']) {
			$wherearr[] = $prefix.DB::field('subscribe', $conditions['subscribe']);
		} 
		
		if($conditions['lastauth']) {
			if(is_array($conditions['lastauth'])) {
				$wherearr[] = $prefix.DB::field('lastauth', $conditions['lastauth'][0], '>=');
				$wherearr[] = $prefix.DB::field('lastauth', $conditions['lastauth'][1], '<=');
			} else {
				$wherearr[] = $prefix.DB::field('lastauth', $conditions['lastauth'], '>=');
			}
		}
		
		$wheresql = !empty($wherearr) && is_array($wherearr) ? ' WHERE ' . implode(' AND ', $wherearr) : '';
		return $wheresql;
	}
	
	public function count_by_search($conditions) {
		return DB::result_first("SELECT COUNT(*) FROM %t %i", array($this->_table, $this->search_condition($conditions)));
	}
	
	public function fetch_all_by_search($conditions, $orderby, $start, $limit) {
		return DB::fetch_all("SELECT * FROM %t %i ".($orderby ? " ORDER BY ".$orderby : '').DB::limit($start, $limit), array($this->_table, $this->search_condition($conditions)));
	}
	
}