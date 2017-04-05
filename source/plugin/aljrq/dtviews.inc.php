<?php
if (! defined ( 'IN_DISCUZ' )) {
		exit ( 'Access Denied' );
}
$config = $_G['cache']['plugin']['aljrq'];
$ks = $config['ks'];
$js = $config['js'];
$h = intval(date("H"));
$x_timestamp = time();
$dtvtime = DB::result_first("select timestamp from ".DB::table('alj_session')." where ming='dtvtime'");
if ($_G['timestamp'] < ($dtvtime + $config['dtvtime'])){
	exit;
}
if(!$dtvtime){
	DB::insert('alj_session',array('ming'=>'dtvtime','timestamp'=>$_G['timestamp']));
}else{
	DB::update('alj_session',array('timestamp'=>$_G['timestamp'])," ming='dtvtime'");
}
//浏览量自增长
if($config['isdtviews']){
	$tid = str_replace('，',',',$config['dtviewstid']);
	$tid = trim ($tid,',');
	$tid = explode ( ',', $tid );
	$tid = array_unique ( $tid );
	$tid = array_filter ( $tid );
	if(count($tid)>$config['dtview_num']){
		
		$rand_tid=array_rand ($tid,$config['dtview_num']);
		if($config['dtview_num']<2){
			$tid=$tid[$rand_tid];
		}else{
			foreach($rand_tid as $k=>$v){
				$tid_array[]=$tid[$v];
			}
			$tid = dimplode ( $tid_array );
		}
		
	}else{
		$tid = dimplode ( $tid );
	}
	
	//单帖浏览量
	$query=DB :: query('select * from '.DB::table('forum_thread')." where tid in ($tid)"); 
	while($array=DB::fetch($query)){
		$dtarr[]=$array;
	}				
	foreach($dtarr as $dtk=>$dtv){
		if (!($h >= $ks && $h <= $js)) {//10  1 3  10<1 
			break;
		}
		if(!$dtv['tid']){
			continue;
		}
		$bigv=rand($config['dtviewssmall'], $config['dtviewsbig']);
		$vviews=$dtv['views'];
		if($vviews>$config['dtbigv']){
			continue;
		}
		$sv=$vviews+$bigv;
		DB :: query("UPDATE " . DB :: table('forum_thread') . " SET views='".intval($sv)."' WHERE tid='".intval($dtv['tid'])."'", 'UNBUFFERED');
	}
}
//浏览量自增长
?>