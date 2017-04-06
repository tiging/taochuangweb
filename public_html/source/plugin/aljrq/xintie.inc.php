<?php
if (! defined ( 'IN_DISCUZ' )) {
		exit ( 'Access Denied' );
}
include_once DISCUZ_ROOT . './source/plugin/aljrq/function_huitie.php';
if (function_exists('discuz_uc_avatar') == false && $_GET['inajax'] != 1 && $_GET['mod'] != 'medal') {
	include_once libfile ('function/forum');
}
$settingfile = DISCUZ_ROOT . './data/sysdata/cache_aljrq_setting.php';
if (file_exists($settingfile)) {
	include_once $settingfile;
} 
if (file_exists(DISCUZ_ROOT . './data/cache/cache_aljrq_setting.php')) {
	$settingfile = DISCUZ_ROOT . './data/cache/cache_aljrq_setting.php';
	include_once DISCUZ_ROOT . './data/cache/cache_aljrq_setting.php';
}
$config = $_G['cache']['plugin']['aljrq'];
if(!$config['isxintie']){
	exit;
}
$xintietime = DB::result_first("select timestamp from ".DB::table('alj_session')." where ming='xintietime'");
if ($_G['timestamp'] < ($xintietime + $config['xintietime']) || $_GET['mod'] == 'medal' || $_GET['inajax'] == 1){
	exit;
}
if(!$xintietime){
	DB::insert('alj_session',array('ming'=>'xintietime','timestamp'=>$_G['timestamp']));
}else{
	DB::update('alj_session',array('timestamp'=>$_G['timestamp'])," ming='xintietime'");
}
if($wcache['xintietid']){
	foreach($wcache['xintietid'] as $kkk => $vvv){
		$xintie=DB::fetch_first('select authorid,tid,fid,subject,replies from '.DB::table('forum_thread')." where tid ='".$vvv."'");
		if(!$xintie){
			unset($wcache['xintietid'][$kkk]);
		}
		// order by insert_time desc limit 0,1000
		$count_uid=DB::result_first(' select count(*) from '.DB::table('plugin_ljmajia')." where status=0 ");
			
		if($count_uid<2){
			$randuid=0;
		}else{
			$randuid=rand(0, $count_uid);
		}
		
		$x_uid=DB::result_first(' select uid from '.DB::table('plugin_ljmajia')." where status=0 limit $randuid,1");
		if(!$x_uid){
			continue;
		}
		$rhuitie = str_replace ("\r", "", $config['nierong']);
		$rhuitie = explode("\n", $rhuitie);
		$rhuitietid = array_rand($rhuitie);
		$huitie = $rhuitie[$rhuitietid];
		$huitie = str_replace ("{name}", $x_user, $huitie);
		$huitie = str_replace ("{author}", $arr['author'], $huitie);
		$huitie = str_replace ("{date}", date('Y-m-d',$_G['timestamp']), $huitie);
		$huitie = str_replace ("{time}", date('H:i:s',$_G['timestamp']), $huitie);
		$huitie = str_replace ("{subject}", $arr['subject'], $huitie);
		$huitie = str_replace ("{forum}", $forums[$fid], $huitie);
		$huitie = str_replace ("{biaoqing}", '{:3_'.rand(41,64).':}', $huitie);
		if (!$huitie) {//内容是否为空
			break;
		}
		
		$x_user = DB :: result_first("SELECT username FROM " . DB :: table('common_member') . " where uid=$x_uid");
		if(!$x_user){
			continue;
		}
		$x_useip = "202." . rand(96, 184) . "." . rand(124, 127) . "." . rand(9, 200); 
		
		$pid = insertpost(array('fid' => $xintie['fid'], 'tid' => $xintie['tid'], 'first' => '0', 'author' => $x_user, 'authorid' => $x_uid, 'subject' => '', 'dateline' => $_G['timestamp'], 'message' => $huitie, 'useip' => $x_useip, 'invisible' => '0', 'anonymous' => '0', 'usesig' => 1, 'htmlon' => '0', 'bbcodeoff' => '0', 'smileyoff' => '0', 'parseurloff' => '0', 'attachment' => '0',)); 
		
		$tid=$xintie['tid'];
		$subject=$xintie['subject'];
		$x_lastpost = "$tid\t" . addslashes($subject) . "\t".$_G['timestamp']."\t$x_user";
		//updatemembercount($x_uid, array('extcredits2 ' => 1));
		DB :: query("UPDATE " . DB :: table('common_member_count') . " SET posts=posts+1 WHERE uid='$x_uid'", 'UNBUFFERED');
		if(!DB :: result_first("SELECT oltime FROM " . DB :: table('common_member_count') . " where uid=$x_uid")){
			DB :: query("UPDATE " . DB :: table('common_member_count') . " SET oltime=".rand(1,10)." WHERE uid='$x_uid'", 'UNBUFFERED');
		}
		DB :: query("UPDATE " . DB :: table('common_member_status') . " SET lastip='$x_useip',lastvisit='$_G[timestamp]',lastactivity='$x_timestamp',lastpost='$x_timestamp' WHERE uid='$x_uid'", 'UNBUFFERED');
		DB :: query("UPDATE " . DB :: table('forum_forum') . " SET posts=posts+1,todayposts=todayposts+1,lastpost='$x_lastpost' WHERE fid='".$xintie['fid']."'", 'UNBUFFERED');
		$checkuid = DB :: result_first("SELECT count(*) FROM " . DB :: table('forum_post') . " where authorid=$x_uid and tid=$tid");
		if($_G['setting']['version']!='X2'){
			//$max=DB::result_first(' select position from '.DB::table('forum_post')." where pid=$pid");
			if ($checkuid) {//重明
				DB :: query("UPDATE " . DB :: table('forum_thread') . " SET replies=replies+1,views=views+1,lastposter='$x_user', lastpost='$_G[timestamp]',maxposition=maxposition+1 WHERE tid='".$xintie['tid']."'", 'UNBUFFERED');
			}else{
				DB :: query("UPDATE " . DB :: table('forum_thread') . " SET heats=heats +1,replies=replies+1,views=views+1,lastposter='$x_user', lastpost='$_G[timestamp]',maxposition=maxposition+1 WHERE tid='".$xintie['tid']."'", 'UNBUFFERED');
			}
		}else{ 
			if ($checkuid) {//重明
				DB :: query("UPDATE " . DB :: table('forum_thread') . " SET replies=replies+1,views=views+1,lastposter='$x_user', lastpost='$_G[timestamp]' WHERE tid='".$xintie['tid']."'", 'UNBUFFERED');
			}else{
				DB :: query("UPDATE " . DB :: table('forum_thread') . " SET heats=heats +1,replies=replies+1,views=views+1,lastposter='$x_user', lastpost='$_G[timestamp]' WHERE tid='".$xintie['tid']."'", 'UNBUFFERED');
			}
			if(DB::result_first('select * from '.DB::table('forum_threadrush')." where tid='$tid'")){
				DB::insert('forum_postposition',array('tid'=>$tid,'pid'=>$pid));
			}
		}
		if ($config[tixing]) {
			$authorid = $xintie['authorid'];
		}
		if (!empty($x_uid) && $x_uid != $_G['uid']) {
			notification_add_huitie1($authorid, 'post', 'reppost_noticeauthor', array('tid' => $xintie['tid'],
				'subject' => $xintie['subject'],
				'fid' => $xintie['fid'],
				'pid' => $pid,
				'from_id' => $xintie['tid'],
				'from_idtype' => 'post',
				), 0, $x_uid, $x_user);
		} 
		$isjiangli = $config['isjiangli'];
		if($isjiangli){
			$jfs = $config['jfs'];
			$extcredits='extcredits'.$config['jflx'];
			updatemembercount($x_uid , array($extcredits => $jfs));
		}
		//虚拟在线部分处理代码
							if(DB::result_first("SELECT uid FROM ".DB::table('common_session')." WHERE uid = ".$x_uid)){
								continue;
							}else{
								$randtime = mt_rand(100, 1800);
								$onlinetime = $_G['timestamp'] - $randtime;
								$insertarray = array(
								'sid' => random(6),
								'ip1' => '0',
								'groupid' => $groupid,
								'lastactivity' => $onlinetime,
								'action' => 0,
								'fid' => 0,
								'uid' => $x_uid,
								'username' => $x_user,
								);
								
								DB::insert('common_session', $insertarray);
							}
					//虚拟在线部分处理代码
		unset($wcache['xintietid'][$kkk]);
		
		require_once libfile('function/cache');
		writetocache('aljrq_setting', getcachevars(array('wcache' => $wcache))); //将管理中心配置项写入缓存
	}	
}
?>