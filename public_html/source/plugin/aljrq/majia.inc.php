<?php
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$config = array();
foreach($pluginvars as $key => $val) {

	$config[$key] = $val['value'];
	
}
$_G['cache']['plugin']['aljrq'] = $config;
$user_majia = $config['user_majia'];
	$user_majia = unserialize ($user_majia);
	$query=DB::query(' select * from '.DB::table('common_usergroup'));
	while($array=DB::fetch($query)){
		$users[]=$array;
	}
	foreach($users as $kk=>$vv){
		$groupids[$vv['groupid']]=$vv['grouptitle'];
	}
$do=daddslashes($_GET['do']);
$op=daddslashes($_GET['op']);
if(!$op){
	$op='newmajia';
}
include template('aljrq:nav');
if($op=='newmajia'){
	if(submitcheck('importsubmit')) {
		loaducenter();
		$init_arr = explode(',', $_G['setting']['initcredits']);
		$usernames=	daddslashes($_GET['usernames']);
		if(!$usernames){
			cpmsg(lang('plugin/aljrq','majia_1'),'', 'error');
		}
		$newpassword=	daddslashes($_GET['newpassword']);
		$newgroupid=	daddslashes($_GET['newgroupid']);
		$password = md5($newpassword);		
		$usernames = explode("\r\n", $usernames);
		$usernames = array_unique($usernames);
		$i=0;
		foreach($usernames as $k => $v){
			$email = trim(rand('1000000','9999999').'@qq.com');
			$v = daddslashes(trim($v));
			$user = dstrlen($v);
			if($user < 2) {
				$username_sb[] = $v;
				continue;
			} elseif($user > 20) {
				$username_sb[] = $v;
				continue;
			}
			if ($v == '') {
				continue;
			}
			if(DB::result_first(' select username from '.DB::table('common_member')." where username='$v'")){
				$username_sb[] = $v;
				continue;
			}
			$uid = uc_user_register($v, $newpassword, $email, $questionid, $answer, $_G['clientip']);
			if($uid <= 0) {
				if($uid == -1) {
					$username_sb[] = $v;
					continue;
				} elseif($uid == -2) {
					$username_sb[] = $v;
					continue;
				} elseif($uid == -3) {
					$username_sb[] = $v;
					continue;
				} elseif($uid == -4) {
					$username_sb[] = $v;
					continue;
				} elseif($uid == -5) {
					$username_sb[] = $v;
					continue;
				} elseif($uid == -6) {
					$username_sb[] = $v;
					continue;
				} else {
					$username_sb[] = $v;
					continue;
				}
			}
			$ctrlip = "202." . rand(96, 184) . "." . rand(124, 127) . "." . rand(9, 200);
						
			$userdata = array(
				'uid' => $uid,
				'username' => $v,
				'password' => $password,
				'email' => $email,
				'adminid' => 0,
				'groupid' => $newgroupid,
				'regdate' => $_G['timestamp'],
				'credits' => $init_arr[0],
				'timeoffset' => 9999
			);
			$status_data = array(
				'uid' => $uid,
				'regip' => $ctrlip,
				'lastip' => $ctrlip,
				'lastvisit' => $_G['timestamp'],
				'lastactivity' => $_G['timestamp'],
				'lastpost' => 0,
				'lastsendmail' => 0,
			);
			DB::insert('common_member', $userdata);
			DB::insert('common_member_status', $status_data);
			DB::insert('common_member_profile', array('uid'=>$uid));
			DB::insert('common_member_field_forum', array('uid'=>$uid));
			DB::insert('common_member_field_home', array('uid'=>$uid));
			$count_data = array(
				'uid' => $uid,
				'extcredits1' => $init_arr[1],
				'extcredits2' => $init_arr[2],
				'extcredits3' => $init_arr[3],
				'extcredits4' => $init_arr[4],
				'extcredits5' => $init_arr[5],
				'extcredits6' => $init_arr[6],
				'extcredits7' => $init_arr[7],
				'extcredits8' => $init_arr[8]
			);
			DB::insert('common_member_count', $count_data);
			DB::insert('common_setting', array('skey' => 'lastmember', 'svalue' => $v), false, true);
			if(DB::result_first(' select username from '.DB::table('plugin_ljmajia')." where username='$v'")){
				$username_sb[] = $v;
				continue;
			}
			DB::insert('plugin_ljmajia',array(
				'uid'=>$uid,
				'username'=>$v,
				'status'=>$status,
				'insert_time'=>$_G['timestamp'],
			));
			$i++;
		}
			updatecache();
		if (count($username_sb)) {
			$username_sb = implode(', ', $username_sb);	
			$username_sb=lang('plugin/aljrq','majia_2').$username_sb;
		} else {
			$username_sb = '';
		}
		cpmsg(lang('plugin/aljrq','majia_3').$i.lang('plugin/aljrq','majia_4'), 'action=plugins&operation=config&do='.$do.'&identifier=aljrq&pmod=majia', 'succeed', array(), $username_sb);
	}
	include template('aljrq:majia');
}else if($op=='uidmajia'){
	if(submitcheck('importsubmit')) {
		$uids=	daddslashes($_GET['uids']);
		$uids = explode("\r\n", $uids);
		$uids = array_unique($uids);
		$i=0;
		foreach($uids as $key=>$val){
			$val=intval($val);
			$user=DB::result_first(' select username from '.DB::table('common_member')." where uid='$val'");
			if(!$user){
				$username_sb[] = $val;
				continue;
			}
			if(DB::result_first(' select username from '.DB::table('plugin_ljmajia')." where username='$user'")){
				$username_sb[] = $val;
				continue;
			}
			DB::insert('plugin_ljmajia',array(
				'uid'=>$val,
				'username'=>$user,
				'status'=>$status,
				'insert_time'=>$_G['timestamp'],
			));
			$i++;
		}
		if (count($username_sb)) {
			$username_sb = implode(', ', $username_sb);	
			$username_sb=lang('plugin/aljrq','majia_5').$username_sb;
		} else {
			$username_sb = '';
		}
		cpmsg(lang('plugin/aljrq','majia_3').$i.lang('plugin/aljrq','majia_4'), 'action=plugins&operation=config&do='.$do.'&identifier=aljrq&pmod=majia&op=uidmajia', 'succeed', array(), $username_sb);
	}
	include template('aljrq:uidmajia');
}else if($op=='usermajia'){
	if(submitcheck('importsubmit')) {
		$importgroupid=	daddslashes($_GET['importgroupid']);
		$query=DB::query(' select * from '.DB::table('common_member')." where groupid=$importgroupid");
		while($array=DB::fetch($query)){
			$uids[]=$array;
		}
		$i=0;
		foreach($uids as $key=>$val){
			if(!$val['username']){
				$username_sb[] = $val['username'];
				continue;
			}
			if(DB::result_first(' select username from '.DB::table('plugin_ljmajia')." where username='".$val['username']."'")){
				$username_sb[] = $val['username'];
				continue;
			}
			DB::insert('plugin_ljmajia',array(
				'uid'=>$val['uid'],
				'username'=>$val['username'],
				'status'=>$status,
				'insert_time'=>$_G['timestamp'],
			));
			$i++;
		}
		if (count($username_sb)) {
			$username_sb = implode(', ', $username_sb);	
			$username_sb=lang('plugin/aljrq','majia_5').$username_sb;
		} else {
			$username_sb = '';
		}
		cpmsg(lang('plugin/aljrq','majia_3').$i.lang('plugin/aljrq','majia_4'), 'action=plugins&operation=config&do='.$do.'&identifier=aljrq&pmod=majia&op=usermajia', 'succeed', array(), $username_sb);
	}
	include template('aljrq:usermajia');
}else if($op=='glmajia'){
	$curpage = daddslashes($_GET['page']);
	$perpage = 15;
	if (!$curpage) {
		$curpage = 1;
	} 
	$curnum = ($curpage-1) * $perpage;
	$sign = 1;
	if(submitcheck('ss_submit')) {
		$uid=intval($_GET['uid']);
		$kaishi=strtotime($_GET['kaishi']);
		$jiesu=strtotime($_GET['jiesu']);
		$con=" where 1";
		if($uid){
			$con.=" and a.uid =$uid";
		}
		if($kaishi&&$jiesu){
			$con.=" and a.insert_time >= $kaishi and a.insert_time <= $jiesu";
		}
	}
	
	$num = DB :: result_first(' select count(*) from '.DB::table('plugin_ljmajia')." a left join ".DB::table('common_member_count')." b on a.uid=b.uid $con");
	$query=DB::query(' select * from '.DB::table('plugin_ljmajia')." a left join ".DB::table('common_member_count')." b on a.uid=b.uid $con order by a.insert_time desc limit $curnum,$perpage");
	while($array=DB::fetch($query)){
		$lj_users[]=$array;
	}
	
	$uidarray=	daddslashes($_GET['uidarray']);
	$btnsubmit = $_GET['btnsubmit'];
	if ($btnsubmit == 'delete'&&!submitcheck('ss_submit')) {
		if(submitcheck('deletesubmit')){
			foreach($uidarray as $k=>$v){
				$v=intval($v);
				DB::query("DELETE FROM ".DB::table('plugin_ljmajia')." WHERE uid='$v'");
				if(DB::result_first(' select uid from '.DB::table('common_member')." where uid='".$v."'")){
					DB::query("DELETE FROM ".DB::table('common_member')." WHERE uid='$v'");
				}
				loaducenter();
				uc_user_delete($uids);
				updatecache();
			}
		}
		header("location:admin.php?action=plugins&operation=config&do=$do&identifier=aljrq&pmod=majia&op=glmajia");
	} else if ($btnsubmit == 'remove') {
		if($_GET['formhash']==formhash()){
			foreach($uidarray as $k=>$v){
				$v=intval($v);
				DB::query("DELETE FROM ".DB::table('plugin_ljmajia')." WHERE uid='$v'");
			}
		}
		header("location:admin.php?action=plugins&operation=config&do=$do&identifier=aljrq&pmod=majia&op=glmajia");
	}
	$app=	$_GET['app'];
	$uid=	daddslashes($_GET['uid']);
	loaducenter();
	if($app=='jin'){
		DB :: query("UPDATE " . DB :: table('plugin_ljmajia') . " SET status=2 WHERE uid='$uid'", 'UNBUFFERED');
		header("location:admin.php?action=plugins&operation=config&do=$do&identifier=aljrq&pmod=majia&op=glmajia&page=$curpage");
	}else if($app=='qi'){
		DB :: query("UPDATE " . DB :: table('plugin_ljmajia') . " SET status=0 WHERE uid='$uid'", 'UNBUFFERED');
		header("location:admin.php?action=plugins&operation=config&do=$do&identifier=aljrq&pmod=majia&op=glmajia&page=$curpage");
	}else if($app=='delqk'){
		if($_GET['formhash']==formhash()){
			DB::query("delete from ".DB::table('plugin_ljmajia'));
			cpmsg(lang('plugin/ljhuitie','xx2'),'action=plugins&operation=config&do=$do&identifier=aljrq&pmod=majia&op=glmajia');
		}
	}
	include template('aljrq:glmajia');
}
?>