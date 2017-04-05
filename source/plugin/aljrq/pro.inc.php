<?php
if (! defined ( 'IN_DISCUZ' )) {
	exit ( 'Access Denied' );
}
include_once DISCUZ_ROOT . './source/plugin/aljrq/function_huitie.php';
if (function_exists('discuz_uc_avatar') == false && $_GET['inajax'] != 1 && $_GET['mod'] != 'medal') {
	include_once libfile ('function/forum');
}
$config = $_G['cache']['plugin']['aljrq'];

$protime = DB::result_first("select timestamp from ".DB::table('alj_session')." where ming='protime'");

if ($_G['timestamp'] < ($protime + $config['protime'])||!$config['ispro']){
	exit;
}
if(!$protime){
	DB::insert('alj_session',array('ming'=>'protime','timestamp'=>$_G['timestamp']));
}else{
	DB::update('alj_session',array('timestamp'=>$_G['timestamp'])," ming='protime'");
}
for($i=0;$i<$config['pronum'];$i++){
	$xh_day=DB :: result_first("select count(*) from " . DB :: table('alj_count_pro') . " where curdate()=FROM_UNIXTIME(dateline,'%Y-%m-%d')");
	if($xh_day>$config['pro_day']){
		break;
	}
	$count=DB::result_first(" select count(*) from ".DB::table('portal_article_title')." where status=0 and id=0 and (dateline+'".$config['prostart']."'*60*60)<='".$_G['timestamp']."' and (dateline+'".$config['proend']."'*60*60)>'".$_G['timestamp']."'");
	
	if($count<2){
		$randnum=0;
	}else{
		$randnum=rand(0, $count);
	}

	$arr=DB::fetch_first(" select * from ".DB::table('portal_article_title')." where status=0 and id=0 and (dateline+'".$config['prostart']."'*60*60)<='".$_G['timestamp']."' and (dateline+'".$config['proend']."'*60*60)>'".$_G['timestamp']."' limit $randnum,1");
	if(in_array($arr['aid'],explode(',',str_replace ("£¬", ",", $config['glaid'])))){
		//debug(in_array($tid,explode(',',str_replace ("£¬", ",", $config['gltid']))));
		continue;
	}
	$count_uid=DB::result_first(' select count(*) from '.DB::table('plugin_ljmajia')." where status=0 ");
			
	if($count_uid<2){
		$randuid=0;
	}else{
		$randuid=rand(0, $count_uid);
	}
	
	$x_uid=DB::result_first(' select uid from '.DB::table('plugin_ljmajia')." where status=0 limit $randuid,1");
	$x_user = DB :: result_first("SELECT username FROM " . DB :: table('common_member') . " where uid=$x_uid");
	if(!$x_user){
		continue;
	}
	$rhuitie = str_replace ("\r", "", $config['promessage']);
	$rhuitie = explode("\n", $rhuitie);
	$rhuitietid = array_rand($rhuitie);
	$huitie = $rhuitie[$rhuitietid];
	$huitie = str_replace ("{name}", $x_user, $huitie);
	$huitie = str_replace ("{author}", $arr['username'], $huitie);
	$huitie = str_replace ("{date}", date('Y-m-d',$_G['timestamp']), $huitie);
	$huitie = str_replace ("{time}", date('H:i:s',$_G['timestamp']), $huitie);
	$huitie = str_replace ("{subject}", $arr['title'], $huitie);
	$x_useip = "202." . rand(96, 184) . "." . rand(124, 127) . "." . rand(9, 200);
	DB :: query("UPDATE ".DB::table('portal_article_count')." SET viewnum=viewnum+1,commentnum=commentnum+1 WHERE aid=".$arr['aid']);
	DB::insert('portal_comment',array(
		'uid'=>$x_uid,	
		'username'=>$x_user,	
		'id'=>$arr['aid'],	
		'idtype'=>'aid',	
		'postip'=>$x_useip,	
		'dateline'=>$_G['timestamp'],	
		'message'=>$huitie,	
	));
	DB::insert('alj_count_pro',array(
		'aid'=>$arr['aid'],	
		'subject'=>$arr['title'],	
		'uid'=>$x_uid,	
		'username'=>$x_user,	
		'dateline'=>$_G['timestamp'],	
		'huitie'=>$huitie,		
	));	
}
?>