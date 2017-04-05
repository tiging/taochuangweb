<?php
if (! defined ( 'IN_DISCUZ' )) {
		exit ( 'Access Denied' );
	}
include_once DISCUZ_ROOT . './source/plugin/aljrq/function_huitie.php';
if (function_exists('discuz_uc_avatar') == false && $_GET['inajax'] != 1 && $_GET['mod'] != 'medal') {
	include_once libfile ('function/forum');
} 
$config = $_G['cache']['plugin']['aljrq'];
if(file_exists(DISCUZ_ROOT . './data/sysdata/cache_fidcache.php')){
	include_once DISCUZ_ROOT . './data/sysdata/cache_fidcache.php';
}else if(file_exists(DISCUZ_ROOT . './data/cache/cache_fidcache.php')){
	include_once DISCUZ_ROOT . './data/cache/cache_fidcache.php';
}
$duotietime = DB::result_first("select timestamp from ".DB::table('alj_session')." where ming='duotietime'");
if ($_G['timestamp'] < ($duotietime + $config['huitiesj']) || $_GET['mod'] == 'medal' || $_GET['inajax'] == 1){
	exit;
}
if(!$duotietime){
	DB::insert('alj_session',array('ming'=>'duotietime','timestamp'=>$_G['timestamp']));
}else{
	DB::update('alj_session',array('timestamp'=>$_G['timestamp'])," ming='duotietime'");
}
$isnot = $config['isnot'];
$uidlist = $config['uidlist'];
$cishu = $config['cishu'];
$tixing = $config['tixing'];
$huitie = $config['huitie'];
$fid = $config['bankuai'];
$iskaiguan = $config['iskaiguan'];
$globaltime = $config['globaltime'];
$begintime = $config['begintime'];

$time = $wcache['value'];
$issjxz = $config['issjxz'];
$num = $config['num'];
$nb=rand(0,$num);
$ks = $config['ks'];
$js = $config['js'];
$h = intval(date("H"));
$x_timestamp = time();
$fid = unserialize ($fid);
//$fid = implode(',', $fid);
//debug($fid);  
$fid = $fid[array_rand ($fid)];

if ($isnot) {
	$isduotie = $config['isduotie'];

	if($isduotie){//---------------------------------------------多帖		 
		for($i=0;$i<$num;$i++){
			$fid = $config['bankuai'];
			$fid = unserialize ($fid); 
			$fid = $fid[array_rand ($fid)];
			
			if(!$fid){
				continue;
			}
			if($config['xh_day']){
				$xh_day=DB :: result_first("select count(*) from " . DB :: table('alj_count') . " where curdate()=FROM_UNIXTIME(dateline,'%Y-%m-%d')");
				if($xh_day>$cishu){
					break;
				}
			}else{
				$con=" and replies<=$cishu";
			}
			$count=DB::result_first(" select count(*) from ".DB::table('forum_thread')." where closed<>'1' AND displayorder<>'-1' AND displayorder<>'-2' AND displayorder<>'-3' AND displayorder<>'-4' $con and fid =$fid and (dateline+$begintime*60*60)<=$x_timestamp and (dateline+$globaltime*60*60)>$x_timestamp");
			if($count<2){
				$randnum=0;
			}else{
				$randnum=rand(0, $count);
			}
			
			$arr=DB::fetch_first(" select * from ".DB::table('forum_thread')." where closed<>'1' AND displayorder<>'-1' AND displayorder<>'-2' AND displayorder<>'-3' AND displayorder<>'-4' $con and fid =$fid and (dateline+$begintime*60*60)<=$x_timestamp and (dateline+$globaltime*60*60)>$x_timestamp limit $randnum,1");
			
			if(!$arr){
				continue;
			}
			if($arr['lastpost']==$x_timestamp){
				
				continue;
			}
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
			$tid = $arr['tid'];
			if ($tixing) {
				$authorid = $arr['authorid'];
			}
			$x_fid = $arr['fid'];
			$subject = $arr['subject'];
			
			if(trim($fidcache['fids'][$fid])){
		
				$rhuitie = str_replace ("\r", "", $fidcache['fids'][$fid]);
				$rhuitie = explode("\n", $rhuitie);
				$rhuitietid = array_rand($rhuitie);
				$huitie = $rhuitie[$rhuitietid];
					
			}else{
				$rhuitie = str_replace ("\r", "", $config['autocontent']);
				$rhuitie = explode("\n", $rhuitie);
				$rhuitietid = array_rand($rhuitie);
				$huitie = $rhuitie[$rhuitietid];
			}	
			$huitie = str_replace ("{name}", $x_user, $huitie);
			$huitie = str_replace ("{author}", $arr['author'], $huitie);
			$huitie = str_replace ("{date}", date('Y-m-d',$_G['timestamp']), $huitie);
			$huitie = str_replace ("{time}", date('H:i:s',$_G['timestamp']), $huitie);
			$huitie = str_replace ("{subject}", $arr['subject'], $huitie);
			$huitie = str_replace ("{forum}", $forums[$fid], $huitie);
			$huitie = str_replace ("{biaoqing}", '{:3_'.rand(41,64).':}', $huitie);
			if (!$huitie) {//内容是否为空
				continue;
			}
			if (!$tid) {//tid为空
				continue;
			}
			if(in_array($tid,explode(',',str_replace ("，", ",", $config['gltid'])))){
				//debug(in_array($tid,explode(',',str_replace ("，", ",", $config['gltid']))));
				continue;
			}
			if ($iskaiguan) {//重明
				$checkuid = DB :: result_first("SELECT count(*) FROM " . DB :: table('forum_post') . " where authorid=$x_uid and tid=$tid");
							
				if ($checkuid) {//重明
					continue;
				}
			}
					
			if (!($h >= $ks && $h <= $js)) {//10  1 3  10<1 
				break;
			}
			//debug($aaa);	
			$groupid=DB::result_first("SELECT groupid FROM ".DB::table('common_member')." where uid=$x_uid");	
			$x_user = DB :: result_first("SELECT username FROM " . DB :: table('common_member') . " where uid=$x_uid");
			if(!$x_user){
				continue;
			}
			$x_useip = "202." . rand(96, 184) . "." . rand(124, 127) . "." . rand(9, 200); 
			// $x_fid=DB::result_first("SELECT fid FROM ".DB::table('forum_thread')." where tid=$tid");
			$pid = insertpost(array('fid' => $x_fid, 'tid' => $tid, 'first' => '0', 'author' => $x_user, 'authorid' => $x_uid, 'subject' => '', 'dateline' => $x_timestamp, 'message' => $huitie, 'useip' => $x_useip, 'invisible' => '0', 'anonymous' => '0', 'usesig' => 1, 'htmlon' => '0', 'bbcodeoff' => '0', 'smileyoff' => '0', 'parseurloff' => '0', 'attachment' => '0',)); 
			// debug($pid);
			$x_lastpost = "$tid\t" . addslashes($subject) . "\t$x_timestamp\t$x_user";
			//updatemembercount($x_uid, array('extcredits2 ' => 1));
			DB :: query("UPDATE " . DB :: table('common_member_count') . " SET posts=posts+1 WHERE uid='$x_uid'", 'UNBUFFERED');
			if(!DB :: result_first("SELECT oltime FROM " . DB :: table('common_member_count') . " where uid=$x_uid")){
				DB :: query("UPDATE " . DB :: table('common_member_count') . " SET oltime=".rand(1,10)." WHERE uid='$x_uid'", 'UNBUFFERED');
			}
			DB :: query("UPDATE " . DB :: table('common_member_status') . " SET lastip='$x_useip',lastvisit='$x_timestamp',lastactivity='$x_timestamp',lastpost='$x_timestamp' WHERE uid='$x_uid'", 'UNBUFFERED');
			DB :: query("UPDATE " . DB :: table('forum_forum') . " SET posts=posts+1,todayposts=todayposts+1,lastpost='$x_lastpost' WHERE fid='$x_fid'", 'UNBUFFERED');
			if($_G['setting']['version']!='X2'){
				if ($checkuid) {//重明
					DB :: query("UPDATE " . DB :: table('forum_thread') . " SET replies=replies+1,views=views+1,lastposter='$x_user', lastpost='$x_timestamp',maxposition=maxposition+1 WHERE tid='$tid'", 'UNBUFFERED');
				}else{
					DB :: query("UPDATE " . DB :: table('forum_thread') . " SET heats=heats +1,replies=replies+1,views=views+1,lastposter='$x_user', lastpost='$x_timestamp',maxposition=maxposition+1 WHERE tid='$tid'", 'UNBUFFERED');
				}	
			}else{ 
				if ($checkuid) {//重明
					DB :: query("UPDATE " . DB :: table('forum_thread') . " SET replies=replies+1,views=views+1,lastposter='$x_user', lastpost='$x_timestamp' WHERE tid='$tid'", 'UNBUFFERED');
				}else{
					DB :: query("UPDATE " . DB :: table('forum_thread') . " SET heats=heats +1,replies=replies+1,views=views+1,lastposter='$x_user', lastpost='$x_timestamp' WHERE tid='$tid'", 'UNBUFFERED');
				}
			}
			if (!empty($x_uid) && $x_uid != $_G['uid']) {
				notification_add_huitie1($authorid, 'post', 'reppost_noticeauthor', array('tid' => $tid,
					'subject' => $subject,
					'fid' => $x_fid,
					'pid' => $pid,
					'from_id' => $tid,
					'from_idtype' => 'post',
					), 0, $x_uid, $x_user);
			} 
			DB::insert('alj_count',array(
					'tid'=>$tid,	
					'subject'=>$subject,	
					'fid'=>$x_fid,	
					'uid'=>$x_uid,	
					'username'=>$x_user,	
					'dateline'=>$_G['timestamp'],	
					'huitie'=>$huitie,	
					'replies'=>$arr['replies']+1,	
				));
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
		} 
	} 
} 
	
?>