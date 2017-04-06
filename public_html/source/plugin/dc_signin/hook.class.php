<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class plugin_dc_signin {
	protected $_lang;
	protected $_var;
	protected $_todaytime;
	function __construct() {
		global $_G;
		$this->_var = $_G['cache']['plugin']['dc_signin'];
		if(!$this->_var['open'])return;
		$this->_lang = lang('plugin/dc_signin');
		$this->_todaytime = strtotime(date('Y-m-d',TIMESTAMP));
	}
	function global_usernav_extra3() {
		global $_G;
		if(!$this->_var['open']||!$this->_var['isglobal']||!$_G['uid'])return;
		return'<span><a id="dcsignin_tips" href="plugin.php?id=dc_signin" onclick="showWindow(\'sign\', \'plugin.php?id=dc_signin:sign\')" style="display:block;float:right;margin:3px; width:77px; height:22px; background:url(source/plugin/dc_signin/images/signin_no.png) no-repeat center center;"></a></span>';
	}
	function global_footer(){
		global $_G;
		if(!$this->_var['open'])return;
		return '<script src="'.$_G['siteurl'].'plugin.php?id=dc_signin:check&formhash='.FORMHASH.'" type="text/javascript"></script>';
	}
}
class plugin_dc_signin_forum extends plugin_dc_signin {
		function viewthread_sidetop_output() {
			global $_G,$postlist;
			if(!$this->_var['open']||!$this->_var['sideshow'])return;
			$return = $uids = array();
			foreach($postlist as $p){
				$uids[$p['uid']] = $p['uid'];
			}
			$signs = C::t('#dc_signin#dc_signin')->fetch_all($uids);
			$signgroup = C::t('#dc_signin#dc_signin_group')->getdata();
			foreach($postlist as $p){
				if($signs[$p['uid']]){
					$qdstr='';
					$sitime=dgmdate($signs[$p['uid']]['dateline'], 'u');
					$qdstr='<div class="qdsmile"><li><center>'.$this->_lang['taxq'].'</center><table><tr><th><img src="source/plugin/dc_signin/images/emot/'.$signs[$p['uid']]['emoticon'].'"><th><font size="5px">'.$signs[$p['uid']]['xq'].'</font><br>'.$sitime.'</tr></table></li></div>';
					if($signgroup[$signs[$p['uid']]['sgid']]['icon'])
						$qdstr .= '<p><img src="source/plugin/dc_signin/images/icon/'.$signgroup[$signs[$p['uid']]['sgid']]['icon'].'" width="120" alt="'.$signgroup[$signs[$p['uid']]['sgid']]['grouptitle'].'"/></p>';
					$return[] = $qdstr;
				}else{
					$return[]= '<p>'.$this->_lang['nosignin'].'</p>';
				}
				
			}
			return $return;
		}
	}
?>