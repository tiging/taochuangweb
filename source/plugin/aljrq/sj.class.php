<?php
	if(!defined('IN_DISCUZ')) {
	exit('Access Deined');
}
class mobileplugin_aljrq {
	function global_header_mobile() {
		global $_G,$todayposts,$postdata,$posts,$forumlist;
		if($_GET['view'] == 'me'){
			return;
		}
		$config = $_G ['cache'] ['plugin'] ['aljrq'];
		if(!$config['isrq']){
			return;
		}
		$forumsttp = explode ("\n", str_replace ("\r", "", $config ['forumsttp']));
		foreach($forumsttp as $key=>$value){
			$arr=explode('|',$value);
			$types[$arr[0]]=$arr;
		}
		if($forumsttp&&$types){
			foreach($types as $k=>$v){
				$forumlist[$k][todayposts]=$v[1]+$forumlist[$k][todayposts];//今天发帖数
				$forumlist[$k][threads]=$v[2]+$forumlist[$k][threads];//主题数
				$forumlist[$k][posts]=$v[3]+$forumlist[$k][posts];//总数
			}
		}
		if(!$config['iskaiqi']){
			return;
		}
		$all=explode('|',$config['suoyou']);
		//debug($all);
		
		if($all[0]>$todayposts){
			$todyas=1;
		}
		//debug(empty($todyas));
		//今日
		$todayposts = empty($all[0])?$todayposts:$all[0]+$todayposts;
		//昨日
		$postdata[0] = empty($all[1])?$postdata[0]:$all[1]+$postdata[0];
		//帖子
		$posts = empty($all[2])?$posts:$all[2]+$posts;
		//会员
		$_G['cache']['userstats']['totalmembers'] = empty($all[3])?$_G['cache']['userstats']['totalmembers']:$all[3]+$_G['cache']['userstats']['totalmembers'];
		
		
	}
	function global_footer_mobile() {
		global $_G;
		$config = $_G ['cache'] ['plugin'] ['aljrq'];
		$qht.= '<script type="text/javascript" src="plugin.php?id=aljrq:xintie"></script>';
		$qht.= '<script type="text/javascript" src="plugin.php?id=aljrq:ht"></script>';
		$qht.= '<script type="text/javascript" src="plugin.php?id=aljrq:view"></script>';
		$qht.= '<script type="text/javascript" src="plugin.php?id=aljrq:dantie"></script>';
		$qht.= '<script type="text/javascript" src="plugin.php?id=aljrq:dtviews"></script>';
		$qht.= '<script type="text/javascript" src="plugin.php?id=aljrq:pro"></script>';
		return $qht;
	}
}
class mobileplugin_aljrq_forum extends mobileplugin_aljrq {
}
?>