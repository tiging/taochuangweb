<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
if($_GET['formhash']!=FORMHASH)die();
$_lang = lang('plugin/dc_signin');
if(!$_G['cache']['plugin']['dc_signin']['open'])die();
$timezone = date_default_timezone_get();
C::app()->timezone_set(getglobal('setting/timeoffset')); //ʱ����Ϊϵͳʱ��
$todaystime = strtotime(dgmdate(TIMESTAMP,'Y-m-d',getglobal('setting/timeoffset')));
$mysignin = C::t('#dc_signin#dc_signin')->fetch($_G['uid']);
if($mysignin['dateline']>$todaystime&&$_G['cache']['plugin']['dc_signin']['isglobal']){
	echo '$(\'dcsignin_tips\').style.backgroundImage="url(source/plugin/dc_signin/images/signin_yes.png)";';
	echo '$(\'dcsignin_tips\').onclick="";';
}
loadcache(array('dc_signinextend','dc_signinstats'));
$signinstats = &$_G['cache']['dc_signinstats'];
if(empty($signinstats)){
	$signinstats = array('todaycount'=>0,'yestercount'=>0,'historymaxcount'=>0,'todaystime'=>$todaystime,'tid'=>0);
	savecache('dc_signinstats', $signinstats);
}
if($todaystime!=$signinstats['todaystime']){  //�շ�������ִ��һ�εļƻ����� �ж�����
	if(dgmdate(TIMESTAMP,'Ym',getglobal('setting/timeoffset'))!=dgmdate($signinstats['todaystime'],'Ym',getglobal('setting/timeoffset'))){ //�·ݷ������ģ�������ص�ǩ�������� 0
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

if($_G['cache']['dc_signinextend']['autosign']){ //�ж��Ƿ��Ѱ�װ�Զ�ǩ��
	C::import('extend/signin','plugin/dc_signin');
	C::import('autosign/signin','plugin/dc_signin/extend');
	$mobj = new autosign_signin();
	$mobj ->mrun();
}
if(function_exists('date_default_timezone_set')) {//ʱ���ָ�
	@date_default_timezone_set($timezone);
}
?>