<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$_lang = lang('plugin/dc_signin');
if(!$_G['cache']['plugin']['dc_signin']['open'])showmessage('undefined_action');
$timezone = date_default_timezone_get();
C::app()->timezone_set(getglobal('setting/timeoffset')); //时区置为系统时区
$todaystime = strtotime(dgmdate(TIMESTAMP,'Y-m-d'));
if(function_exists('date_default_timezone_set')) {//时区恢复
	@date_default_timezone_set($timezone);
}
if(!$_G['uid']) showmessage('not_loggedin','member.php?mod=logging&action=login');
if(submitcheck('signsubmit')){
	$emotid = dintval($_GET['emotid']);
	$content = trim($_GET['content']);
	$content = dhtmlspecialchars($content);
	$emot = C::t('#dc_signin#dc_signin_emot')->fetch($emotid);
	if(empty($emot))showmessage($_lang['emoterror']);
	include_once DISCUZ_ROOT.'./source/plugin/dc_signin/signin.lib.class.php';
	$sign = new LibSigin($_G['member']);
	$sign->SetEmot($emot);
	$sign->SetMsg($content);
	$return = $sign->Sigin();
	if($return['ret'])showmessage($return['msg']);
	$referer = dreferer();
	$referer = $referer==$_G['siteurl']?'plugin.php?id=dc_signin':$referer;
	$reward = $sign->GetReward();
	$rewardstr = implode(',',$reward);
	showmessage($_lang['qdok'].$rewardstr,$referer,array(),array('alert'=>'right'));
}
$mysignin = C::t('#dc_signin#dc_signin')->fetch($_G['uid']);
if($mysignin['dateline']>$todaystime&&!defined('IN_MOBILE'))showmessage($_lang['issign']);
if(defined('IN_MOBILE')){
	if(empty($mysignin))$mysignin=array('days'=>0,'condays'=>0,'credit'=>0);
	$qdgroup = C::t('#dc_signin#dc_signin_group')->getdata();
	foreach($qdgroup as $gd){
		if($gd['dayslower']>$mysignin['days']){
			$upgrade = $gd;
			break;
		}
	}
}
$emots = C::t('#dc_signin#dc_signin_emot')->getdata();
include template('dc_signin:sign');
?>