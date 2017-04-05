<?php


if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
class extend_install{
	public $title;
	public $des;
	public $com;
	public $author;
	public $version;
	public $mobile = 0;
	protected $_lang = array();
	public function __construct(){
		$this->_lang = @include DISCUZ_ROOT.'./source/plugin/dc_signin/language/'.$this->getextend().'.'.currentlang().'.php';
		if(empty($this->_lang))$this->_lang = @include DISCUZ_ROOT.'./source/plugin/dc_signin/language/'.$this->getextend().'.php';
	}
	private function getextend(){
		if(preg_match('/^(\w+)\_install$/',get_class($this),$a))
			return $a[1];
	}
	public function install(){
		return true;
	}
	
	public function uninstall(){
		return true;
	}
	
	public function upgrade($version){
		
	}
}
?>