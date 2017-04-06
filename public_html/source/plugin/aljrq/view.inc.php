<?php
if (! defined ( 'IN_DISCUZ' )) {
		exit ( 'Access Denied' );
}
$config = $_G['cache']['plugin']['aljrq'];
$viewtime = DB::result_first("select timestamp from ".DB::table('alj_session')." where ming='viewtime'");
if ($_G['timestamp'] < ($viewtime + $config['viewtime'])){
	exit;
}
if(!$viewtime){
	DB::insert('alj_session',array('ming'=>'viewtime','timestamp'=>$_G['timestamp']));
}else{
	DB::update('alj_session',array('timestamp'=>$_G['timestamp'])," ming='viewtime'");
}
$globaltime = $config['viewendtime'];
$begintime = $config['viewstarttime'];
$viewnum = $config['viewnum'];
$nb=rand(0,$num);
$ks = $config['ks'];
$js = $config['js'];
$h = intval(date("H"));
$x_timestamp = time();
//浏览量自增长
$isview = $config['isview'];
if($isview){
	for($i=0;$i<$viewnum;$i++){
		$viewsnum = $config['viewsnum'];
		$browse_bk = $config['browse_bk'];
		$browse_bk = unserialize ($browse_bk);
		$browse_fid = $browse_bk[array_rand ($browse_bk)];
		$bigv = $config['bigv'];
		$smallv = $config['smallv'];
		$bigv=rand($smallv, $bigv);
		$count=DB::result_first(" select count(*) from ".DB::table('forum_thread')." where closed<>'1' AND displayorder<>'-1' AND displayorder<>'-2' AND displayorder<>'-3' AND displayorder<>'-4' and views<=$viewsnum and fid =$browse_fid and (dateline+$begintime*60*60)<=$x_timestamp and (dateline+$globaltime*60*60)>$x_timestamp");
		if($count<2){
			$randnum=0;
		}else{
			$randnum=rand(0, $count);
		}
		$varr=DB::fetch_first(" select tid,views,lastpost from ".DB::table('forum_thread')." where closed<>'1' AND displayorder<>'-1' AND displayorder<>'-2' AND displayorder<>'-3' AND displayorder<>'-4' and views<=$viewsnum and fid =$browse_fid and (dateline+$begintime*60*60)<=$x_timestamp and (dateline+$globaltime*60*60)>$x_timestamp limit $randnum,1");
		
		if(!$varr){
			continue;
		}
		if (!($h >= $ks && $h <= $js)) {//10  1 3  10<1 
			break;
		}
		//debug($varr);
		$vtid=$varr[tid];
		if(in_array($vtid,$_G['cookie']['aljrq_arr'])){
			continue;
		}
		$arr[]=$vtid;
		$vviews=$varr[views];
		$sv=$vviews+$bigv;
		DB :: query("UPDATE " . DB :: table('forum_thread') . " SET views='$sv' WHERE tid='$vtid'", 'UNBUFFERED');
		//dsetcookie('aljrq_arr', $arr), $config['viewtime']);
	}
	//dsetcookie('aljrq_arr', ''), 1);
}
//浏览量自增长
?>