<?php
/**
 *      [Discuz!] (C)2015-2099 DARK Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: signin.lib.class.php 4260 2015-10-19 10:18:15Z wang11291895@163.com $
 */
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class LibSigin{
	protected $_version = 'v1.0.1';
	protected $_user;
	protected $_reward = array();
	protected $_lang = array();
	protected $_emot = array();
	protected $_msg;
	protected $_msgflag = false;
	public function __construct($user) {
		$this->LibSigin($user);
	}
	public function LibSigin($user) {
		$this->_user = $user;
		SigninInfo::Set('user',$user);
		$this->_lang = lang('plugin/dc_signin');
	}
	public function version(){
		return $this->_version;
	}
	public function Sigin(){
		global $_G;
		loadcache(array('dc_signinstats','dc_signinextend','plugin'));
		if(!$this->_user||!$this->_user['uid']||!$_G['cache']['plugin']['dc_signin']['open'])return;
		$signinstats = &$_G['cache']['dc_signinstats'];
		$timezone = date_default_timezone_get();
		C::app()->timezone_set(getglobal('setting/timeoffset')); //时区置为系统时区
		$todaystime = strtotime(dgmdate(TIMESTAMP,'Y-m-d',getglobal('setting/timeoffset')));
		if($todaystime!=$signinstats['todaystime']){
			if(dgmdate(TIMESTAMP,'Ym',getglobal('setting/timeoffset'))!=dgmdate($signinstats['todaystime'],'Ym',getglobal('setting/timeoffset'))){
				C::t('#dc_signin#dc_signin')->clearmonthdays();
			}
			C::t('#dc_signin#dc_signin')->clearcondaydays();
			if($signinstats['todaystime']<$todaystime-86400)
				$signinstats['yestercount'] = 0;
			else
				$signinstats['yestercount'] = $signinstats['todaycount'];
			$signinstats['todaycount'] = 0;
			$signinstats['todaystime'] = $todaystime;
			$signinstats['tid'] = 0;
			savecache('dc_signinstats', $signinstats);
		}
		$mysignin = C::t('#dc_signin#dc_signin')->fetch($this->_user['uid']);
		if($mysignin['dateline']>$todaystime){
			return array('ret'=>1,'msg'=>$this->_lang['issign']);
		}
		$groups = unserialize($_G['cache']['plugin']['dc_signin']['groups']);
		if(!in_array($this->_user['groupid'],$groups)){
			return array('ret'=>2,'msg'=>$this->_lang['isnoallowgroup']);
		}
		if($_G['cache']['plugin']['dc_signin']['timeopen']){
			$nowhour = dgmdate(TIMESTAMP,'H',getglobal('setting/timeoffset'));
			if($_G['cache']['plugin']['dc_signin']['stime']>$nowhour||$nowhour>$_G['cache']['plugin']['dc_signin']['ftime']){
				return array('ret'=>3,'msg'=>$this->_lang['isnoinrighttime']);
			}
		}
		$extcredit = array();
		$jcjf = rand($_G['cache']['plugin']['dc_signin']['mincredit'],$_G['cache']['plugin']['dc_signin']['maxcredit']);
		$extcredit[$_G['cache']['plugin']['dc_signin']['extcredit']] = $jcjf;
		updatemembercount($this->_user['uid'], $extcredit, true, '', 0, '',$this->_lang['pluginname'],$this->_lang['qdjfjl']);
		$chkpath = DISCUZ_ROOT.'./source/plugin/dc_sig'.'nin/tem'.'plate/cen'.'ter.htm';
		$emot = $this->Emot();
		$historydata = array(
			'uid'=>$this->_user['uid'],
			'username'=>$this->_user['username'],
			'dateline'=>TIMESTAMP,
			'icon'=>$emot['icon'],
			'xq'=>$emot['name'],
			'say'=>$this->Msg(),
			'credit'=>$jcjf,
		);
		SigninInfo::Set('history',$historydata);
		C::t('#dc_signin#dc_signin_history')->insert($historydata);
		if(empty($mysignin)){
			$qdgroup = C::t('#dc_signin#dc_signin_group')->getgid(1);
			$mysignin = array(
				'uid'=>$this->_user['uid'],
				'username'=>$this->_user['username'],
				'dateline'=>TIMESTAMP,
				'days'=>1,
				'condays'=>1,
				'monthdays'=>1,
				'monthcondays'=>1,
				'bcredit'=>$jcjf,
				'credit'=>$jcjf,
				'sgid'=>$qdgroup['id'],
				'xq'=>$emot['name'],
				'emoticon'=>$emot['icon'],
			);
			SigninInfo::Set('mysignin',$mysignin);
			C::t('#dc_signin#dc_signin')->insert($mysignin);
		}else{
			$qddays = $mysignin['days']+1;
			$qdgroup = C::t('#dc_signin#dc_signin_group')->getgid($qddays);
			$mysigninu = array(
				'uid'=>$this->_user['uid'],
				'username'=>$this->_user['username'],
				'dateline'=>TIMESTAMP,
				'days'=>$qddays,
				'condays'=>$mysignin['condays']+1,
				'monthdays'=>$mysignin['monthdays']+1,
				'monthcondays'=>$mysignin['monthcondays']+1,
				'bcredit'=>$jcjf,
				'credit'=>$mysignin['credit']+$jcjf,
				'sgid'=>$qdgroup['id'],
				'xq'=>$emot['name'],
				'emoticon'=>$emot['icon'],
			);
			SigninInfo::Set('mysignin',$mysigninu);
			C::t('#dc_signin#dc_signin')->update($this->_user['uid'],$mysigninu);
		}
		$signinstats['todaycount']+=1;
		if($signinstats['todaycount']>$signinstats['historymaxcount'])
			$signinstats['historymaxcount']=$signinstats['todaycount'];
		savecache('dc_signinstats', $signinstats);
		SigninInfo::Set('signinstats',$signinstats);
		if(md5_file($chkpath)!='040558a78e39f2418e415d48cff3eab8')return true;
		$reward = array();
		$reward['rand'] =$this->_lang['randreward'].$_G['setting']['extcredits'][$_G['cache']['plugin']['dc_signin']['extcredit']]['title'].$jcjf;
		C::import('extend/signin','plugin/dc_signin');
		foreach($_G['cache']['dc_signinextend'] as $k=>$ext){
			if($ext['com']!='reward')continue;
			C::import($k.'/signin','plugin/dc_signin/extend',false);
			$modstr = $k.'_signin';
			if (class_exists($modstr,false)){
				$_obj = new $modstr();
				if(in_array('mrun',get_class_methods($_obj))){
					$_tmp=$_obj->mrun();
					if($_tmp)$reward[$k]=$_tmp;
				}
			}
		}
		SigninInfo::Set('reward',$reward);
		if($_G['cache']['dc_signinextend']['post']){
			C::import('post/signin','plugin/dc_signin/extend');
			$mobj = new post_signin();
			$mobj ->mrun();
		}
		if(function_exists('date_default_timezone_set')) {  //时区恢复
			@date_default_timezone_set($timezone);
		}
		return true;
	}
	protected function Emot(){
		if(!empty($this->_emot))return $this->_emot;
		$this->_emot = C::t('#dc_signin#dc_signin_emot')->getrand();
		return $this->_emot;
	}
	protected function Msg(){
		if(!empty($this->_emot)&&!$this->_msgflag)return $this->_emot['text'];
		if(!empty($this->_msg))return $this->_msg;
		return $this->_lang['nosaymsg'];
	}
	public function SetEmot($emot){
		$this->_emot = $emot;
	}
	public function SetMsg($msg){
		$this->_msg = $msg;
		$this->_msgflag = true;
	}
	public function GetReward(){
		return SigninInfo::Get('reward');
	}
}
class SigninInfo{
	private static $_info = array();
	
	public static function Set($val,$data){
		if(!$val)return;
		self::$_info[$val] = $data;
	}
	public static function Get($val){
		if(!$val)return self::$_info;
		return self::$_info[$val];
	}
	
}
?>