<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class extend_signin{
	protected $_lang = array();
	public function __construct(){
		$this->_lang = @include DISCUZ_ROOT.'./source/plugin/dc_signin/language/'.$this->getextend().'.'.currentlang().'.php';
		if(empty($this->_lang))$this->_lang = @include DISCUZ_ROOT.'./source/plugin/dc_signin/language/'.$this->getextend().'.php';
	}
	private function getextend(){
		if(preg_match('/^(\w+)\_signin$/',get_class($this),$a))
			return $a[1];
	}
}
?>