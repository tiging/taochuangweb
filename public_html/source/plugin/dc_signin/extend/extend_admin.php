<?php


if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
class extend_admin{
	protected $_lang = array();
	public function __construct(){
		$this->_lang = @include DISCUZ_ROOT.'./source/plugin/dc_signin/language/'.$this->getextend().'.'.currentlang().'.php';
		if(empty($this->_lang))$this->_lang = @include DISCUZ_ROOT.'./source/plugin/dc_signin/language/'.$this->getextend().'.php';
	}
	private function getextend(){
		if(preg_match('/^(\w+)\_admin$/',get_class($this),$a))
			return $a[1];
	}
}
?>