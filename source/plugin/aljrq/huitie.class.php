<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
} 

class plugin_aljrq {
	function __construct() {
		
	} 
	function avatar($param) {
		global $_G,$avatarCache;
		$config = $_G['cache']['plugin']['aljrq'];
		if(!$config['is_avatar']){
			return;
		}
		$filesrc=DISCUZ_ROOT.'./data/sysdata/cache_aljrq.php';
		if (file_exists($filesrc)) {
			include_once $filesrc;
		} 
		if (file_exists(DISCUZ_ROOT . './data/cache/cache_aljrq.php')) {
			$filesrc = DISCUZ_ROOT . './data/cache/cache_aljrq.php';
			include_once DISCUZ_ROOT . './data/cache/cache_aljrq.php';
		}
		if((TIMESTAMP-filemtime($filesrc)>=3600)||!file_exists($filesrc)){
			$this->scanfile();
		}
		list($uid, $size, $returnsrc) = $param['param'];
		$uid = abs(intval($uid));
		if(isset($avatarCache[$uid])){
			$avatarstatus=$avatarCache[$uid];
		}else{
			$avatarstatus=DB::result_first("SELECT avatarstatus FROM ".DB::table('common_member')." where uid=$uid");
			$avatarCache[$uid]=$avatarstatus;
		}	
		if($avatarstatus==0&&$_GET['ac']!='avatar'&&$uid&&file_exists($filesrc)){
			@require $filesrc;
			$num=count($avatar);
			$src=$avatar[$uid%$num];
			if(!file_exists($src)){
				$this->scanfile();
				@require $filesrc;
				$num=count($avatar);
				$src=$avatar[$uid%$num];
			}	
			$_G['hookavatar'] = $returnsrc ? $src : '<img src="'.$src.'" />';
		}	
	}
	function scanfile(){
		global $_G;
		$dir = DISCUZ_ROOT.'./source/plugin/aljrq/images/';
		$handle=opendir($dir); 
		$avatar=array();
		while(false!==($file=readdir($handle))){ 
			if(substr_count($file,'.jpg')){
				$avatar[]='source/plugin/aljrq/images/'.$file;
			}
		}
		@require_once libfile('function/cache');	
		writetocache('aljrq', getcachevars(array('avatar' => $avatar))); //将管理中心配置项写入缓存
	}
	function global_header() {
		global $_G;
		$config = $_G['cache']['plugin']['aljrq'];
		$qht.= '<script type="text/javascript" src="plugin.php?id=aljrq:xintie"></script>';
		$qht.= '<script type="text/javascript" src="plugin.php?id=aljrq:ht"></script>';
		$qht.= '<script type="text/javascript" src="plugin.php?id=aljrq:view"></script>';
		$qht.= '<script type="text/javascript" src="plugin.php?id=aljrq:dantie"></script>';
		$qht.= '<script type="text/javascript" src="plugin.php?id=aljrq:dtviews"></script>';
		$qht.= '<script type="text/javascript" src="plugin.php?id=aljrq:pro"></script>';
		return $qht;
	}
} 
class plugin_aljrq_forum extends plugin_aljrq {

	
	function index_aljrq_output() {
		global $onlinenum,$onlineinfo,$membercount,$guestcount,$_G,$todayposts,$postdata,$posts,$forumlist;
		$config=$_G['cache']['plugin']['aljrq'];
		if(!$config['isrq']){
			return;
		}
		if($config['isrq']){
			$onlineinfo[0]=$config['high'];
			$onlineinfo[1]=$config['time'];
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
		$ks = $config['ks'];
		$js = $config['js'];
		$h = intval(date("H"));
		$all=explode('|',$config['suoyou']);
		if($all[0]>$todayposts&&$h >= $config['xunitime']){
			$todyas=1;
		}
		
		//今日
		$todayposts = empty($todyas)?$todayposts:$all[0]+$todayposts;
		//昨日
		$postdata[0] = empty($all[1])?$postdata[0]:$all[1]+$postdata[0];
		//帖子
		$posts = empty($all[2])?$posts:$all[2]+$posts;
		//会员
		$_G['cache']['userstats']['totalmembers'] = empty($all[3])?$_G['cache']['userstats']['totalmembers']:$all[3]+$_G['cache']['userstats']['totalmembers'];
	}
	
	function forumdisplay_top_output(){
		loadcache('plugin');
		global $_G;
		
		$config=$_G['cache']['plugin']['aljrq'];
		$h = intval(date("H"));
		if($h < $config['xunitime']){
			return;
		}
		$forumsttp = explode ("\n", str_replace ("\r", "", $config ['forumsttp']));
		foreach($forumsttp as $key=>$value){
			$arr=explode('|',$value);
			$types[$arr[0]]=$arr;
		}
		
		$_G['forum'][todayposts]=$types[$_G['fid']][1]+$_G['forum'][todayposts];
		$_G['forum'][threads]=$types[$_G['fid']][2]+$_G['forum'][threads];
		//debug($_G['forum']);
	}

	function index_aljrq() {
		global $_G;
		$config=$_G['cache']['plugin']['aljrq'];
		$ks = $config['ks'];
		$js = $config['js'];
		$h = intval(date("H"));
		if (!($h >= $ks && $h <= $js)) {//10  1 3  10<1 
			DB::query("delete FROM ".DB::table('common_session')." where ip1='0'");
			return;
		}
		if($config['isrq']){
			$timeout = $_G['timestamp'] - 1800;	
			DB::delete('common_session',"ip1='0' AND lastactivity <='$timeout'");
			$onlinenum = DB::result_first("SELECT count(*) FROM ".DB::table('common_session'));
			$randnum=rand($config[m],$config[mm]);
			$yrandnum=rand($config[m],$config[mm]);
			$query = DB::query("SELECT * FROM ".DB::table('plugin_ljmajia')." ORDER BY rand() LIMIT 0, $randnum");
			while($row=DB::fetch($query)){
				$members[]=$row;
			}
			if($onlinenum<$config['lt']){
				for($i=1;$i<$yrandnum;$i++){
					$randtime = mt_rand(100, 1800);
					$onlinetime = $_G['timestamp'] - $randtime;
					$insertarray = array(
					'sid' => random(6),
					'ip1' => '0',
					'groupid' => 7,
					'lastactivity' => $onlinetime,
					'action' => 2,
					'fid' => 0,
				);
						
				DB::insert('common_session', $insertarray);
				}
				foreach($members as $member){
					if(DB::result_first("SELECT uid FROM ".DB::table('common_session')." WHERE uid = ".$member['uid'])){
						continue;
					}else{
						$randtime = mt_rand(100, 1800);
						$onlinetime = $_G['timestamp'] - $randtime;
						$insertarray = array(
						'sid' => random(6),
						'ip1' => '0',
						'groupid' => $member[groupid],
						'lastactivity' => $onlinetime,
						'action' => 0,
						'fid' => 0,
						'uid' => $member['uid'],
						'username' => $member['username'],
						);
						
						DB::insert('common_session', $insertarray);
					}
				}
			}
		}else{
			DB::query("delete FROM ".DB::table('common_session')." where ip1='0'");
		}
	}
	function post_checkreply_message($param) {
		global $_G;
		$config = $_G['cache']['plugin']['aljrq'];
		$settingfile = DISCUZ_ROOT . './data/sysdata/cache_aljrq_setting.php';
		if (file_exists($settingfile)) {
			include_once $settingfile;
		} 
		if (file_exists(DISCUZ_ROOT . './data/cache/cache_aljrq_setting.php')) {
			$settingfile = DISCUZ_ROOT . './data/cache/cache_aljrq_setting.php';
			include_once DISCUZ_ROOT . './data/cache/cache_aljrq_setting.php';
		}
		if(!$config['isxintie']){
			return;
		}
		$bkfid = unserialize($config['bankuai']);
		if ($param ['param'] [0] == "post_newthread_succeed") {
			if(in_array ($_GET['fid'], $bkfid)){
				$tid = $param ['param'] [2][tid];
				//debug($tid);
				$wcache['xintietid'][] = $tid;
				require_once libfile('function/cache');
				writetocache('aljrq_setting', getcachevars(array('wcache' => $wcache))); //将管理中心配置项写入缓存
			}
		}
	}
}
?>