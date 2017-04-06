<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$chkpath = DISCUZ_ROOT.'./source/plugin/dc_sig'.'nin/tem'.'plate/cen'.'ter.htm';
$action = trim($_GET['action']);
$action = $action ? $action : 'index';
$_lang = lang('plugin/dc_signin');
$version = 'Ver 1.0.4';
$todaystime = strtotime(dgmdate(TIMESTAMP,'Y-m-d'));
if(!$_G['cache']['plugin']['dc_signin']['open'])showmessage('undefined_action');
if (!$_G['uid']) showmessage('not_loggedin','member.php?mod=logging&action=login');
if(!preg_match("/^[a-z0-9_\-]+$/i", $action))showmessage('undefined_action');
$file = DISCUZ_ROOT.'./source/plugin/dc_signin/module/'.$action.'.inc.php';
if(!file_exists($file)) showmessage('undefined_action');
loadcache(array('dc_signinstats','dc_signinextend'));
$signinstats = &$_G['cache']['dc_signinstats'];
if(empty($signinstats)){
	$signinstats = array('todaycount'=>0,'yestercount'=>0,'historymaxcount'=>0,'todaystime'=>$todaystime,'tid'=>0);
	savecache('dc_signinstats', $signinstats);
}
if($todaystime!=$signinstats['todaystime']){  //日发生更改执行一次的计划任务 判断条件
	if(dgmdate(TIMESTAMP,'Ym')!=dgmdate($signinstats['todaystime'],'Ym')){ //月份发生更改，与月相关的签到数据清 0
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
if($_G['cache']['dc_signinextend']['rmcopy']){
	$rmcopy = @include DISCUZ_ROOT.'./source/plugin/dc_signin/data/rmcopy.config.php';
}
$signindh = $_lang['signindh'];
$signindhtp = @include DISCUZ_ROOT.'./source/plugin/dc_signin/data/signindh.config.php';
if(!empty($signindhtp)&&is_array($signindhtp)){
	$signindhdj = $signindh['qddj'];
	unset($signindh['qddj']);
	$signindh =array_merge($signindh,$signindhtp,array('qddj'=>$signindhdj));
}
$mysignin = C::t('#dc_signin#dc_signin')->fetch($_G['uid']);
$navtitle = $_lang['pluginname'];
@include $file;
include template('dc_signin:center');
?>