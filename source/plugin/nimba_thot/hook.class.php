<?php
/*
 * 主页：http://addon.discuz.com/?@ailab
 * 人工智能实验室：Discuz!应用中心十大优秀开发者！
 * 插件定制 联系QQ594941227
 * From www.ailab.cn
 */
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
} 
class plugin_nimba_thot {
}

class plugin_nimba_thot_forum extends plugin_nimba_thot{
    function viewthread_postbottom_output(){
		global $_G,$postlist,$notin;
		loadcache('plugin');
		$return=array();
		if(!$_G['forum_firstpid']) return $return;
		$uid=$_G['forum_thread']['authorid']? $_G['forum_thread']['authorid']:$_G['uid'];
		$var=$_G['cache']['plugin']['nimba_thot'];
		$title=trim($var['title']);//标题
		$color=$var['color'];
		$openForums=unserialize($var['open']);//是否开启版块
		if(!in_array($_G['fid'],$openForums)) return $return;
		$num=intval($var['num']);
		$num=$num%2==0? $num:($num+1);//偶数		
		$viewForum=$var['forums'];//是否显示版块名称
		$rank=intval($var['rank']);//排序
		$orderMaps=array(
			1=>'order by views DESC',
			2=>'order by lastpost DESC',
			3=>'order by replies DESC',
			4=>'order by dateline DESC',
			5=>'order by rand()',
		);
		$r=$orderMaps[$rank];
		$ban=unserialize($var['ban']);//屏蔽板块
		if($ban&&$ban[0]!=0){
			$notin='and fid not in('.implode(',',$ban).')';
		}else{
			$notin='';
		}
		$hotlist=DB::fetch_all("SELECT tid,subject,fid FROM ".DB::table('forum_thread')." where authorid=$uid $notin and displayorder>=0 $r LIMIT 0,$num");
		if($viewForum){
			loadcache('forums');
			$forumlist=$_G['cache']['forums'];	
		}
		include template('nimba_thot:post');
		$return[0]=$louzhu;//仅限楼主贴显示
		return $return;
    }
}

?>