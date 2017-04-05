<?php
 
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
$op=$_GET['op'];
include template('aljrq:nav_tj');
if($op=='pro'){
	if(!submitcheck('submit')) {
		$day  = date('d'); 
		$mon  = date('m'); 
		$year = date('Y'); 
		$today  = date('N'); 
		$start = date('Y-m-d', mktime(0,0,0, $mon, $day-$today+1, $year)); 
		$end   = date('Y-m-d H:i:s', mktime(23,59,59, $mon, $day-$today+7, $year)); 
		$tomon_s = date('Y-m-d H:i:s', mktime(0,0,0, $mon, 1, $year)); 
		$tomon_e = date('Y-m-d H:i:s', mktime(23,59,59, $mon+1, 0, $year));
		
		$brnum=DB::result_first("select count(*) from ".DB::table('alj_count_pro')." where dateline >='".strtotime(date('Y-m-d 00:00:00'))."'");
		if(!$brnum){
			$brnum=0;
		}
		$bznum=DB::result_first("select count(*) from ".DB::table('alj_count_pro')." where dateline >='".strtotime($start)."' and dateline <='".strtotime($end)."'");
		if(!$bznum){
			$bznum=0;
		}
		$bynum=DB::result_first("select count(*) from ".DB::table('alj_count_pro')." where dateline >='".strtotime($tomon_s)."' and dateline <='".strtotime($tomon_e)."'");
		if(!$bynum){
			$bynum=0;
		}
		//debug($brnum);
		showformheader('plugins&operation=config&identifier=aljrq&pmod=reply_tj&op=pro');
		showtableheader(lang('plugin/aljrq','count_4').$brnum.'&nbsp;&nbsp;'.lang('plugin/aljrq','count_5').$bznum.'&nbsp;&nbsp;'.lang('plugin/aljrq','count_6').$bynum);
		include template('aljrq:admin_keyword');
		showsubtitle(array('aid',lang('plugin/aljrq','count_1'), lang('plugin/aljrq','count_8'),lang('plugin/aljrq','count_9'),lang('plugin/aljrq','count_10'),lang('plugin/aljrq','count_11')));
		$currpage=$_GET['page']?$_GET['page']:1;
		$perpage=25;
		$start=($currpage-1)*$perpage;
		$con=" where 1";
		if(submitcheck('seoeye_submit')) {
			$keyword=addcslashes($_GET['keyword'], '%_');
			$aid=intval($_GET['aid']);
			$kaishi=strtotime($_GET['kaishi']);
			$jiesu=strtotime($_GET['jiesu']);
			
			if($keyword){
				$con.=" and username like '%$keyword%'";
			}
			if($aid){
				$con.=" and aid =$aid";
			}
			if($kaishi&&$jiesu){
				$con.=" and dateline >= $kaishi and dateline <= $jiesu";
			}
		}
		
		$num=DB::result_first('select count(*) from '.DB::table('alj_count_pro')." $con ");
		//debug($con);
		$c_query=DB::query('select * from '.DB::table('alj_count_pro')." $con order by dateline desc limit $start,$perpage");

		while($c_arr=DB::fetch($c_query)){
			$counts[]=$c_arr;
		}
		//$counts=DB::fetch_all('select * from '.DB::table('alj_count')." $con order by dateline desc limit $start,$perpage");
		$paging = helper_page :: multi($num, $perpage, $currpage, 'admin.php?action=plugins&operation=config&identifier=aljrq&pmod=reply_tj&op=pro&aid='.$aid.'&kaishi='.$kaishi.'&jiesu='.$jiesu, 0, 10, false, false);
		foreach($counts as $tongji){
			$dateline=date('Y-m-d H:i:s',$tongji['dateline']);
			showtablerow('', array('', 'style="width:200px;"', 'style="width:480px;"','style="width:100px;"','class="td_l"','style="width:50px;"'), array(							
							$tongji['aid'],	
							$tongji['subject'],	
							
							$tongji['huitie'],
							$tongji['username'],
							$dateline,		
							'<a target="_blank" href="portal.php?mod=view&aid='.$tongji['aid'].'">'.lang('plugin/aljrq','count_12').'</a>',		
							));
			
		}
		showsubmit('', '', '','',$paging);
		showtablefooter();
		showformfooter();
	}
}else{
	if(!submitcheck('submit')) {
		$day  = date('d'); 
		$mon  = date('m'); 
		$year = date('Y'); 
		$today  = date('N'); 
		$start = date('Y-m-d', mktime(0,0,0, $mon, $day-$today+1, $year)); 
		$end   = date('Y-m-d H:i:s', mktime(23,59,59, $mon, $day-$today+7, $year)); 
		$tomon_s = date('Y-m-d H:i:s', mktime(0,0,0, $mon, 1, $year)); 
		$tomon_e = date('Y-m-d H:i:s', mktime(23,59,59, $mon+1, 0, $year));
		
		$brnum=DB::result_first("select count(*) from ".DB::table('alj_count')." where dateline >='".strtotime(date('Y-m-d 00:00:00'))."'");
		if(!$brnum){
			$brnum=0;
		}
		$bznum=DB::result_first("select count(*) from ".DB::table('alj_count')." where dateline >='".strtotime($start)."' and dateline <='".strtotime($end)."'");
		if(!$bznum){
			$bznum=0;
		}
		$bynum=DB::result_first("select count(*) from ".DB::table('alj_count')." where dateline >='".strtotime($tomon_s)."' and dateline <='".strtotime($tomon_e)."'");
		if(!$bynum){
			$bynum=0;
		}
		//debug($brnum);
		showformheader('plugins&operation=config&identifier=aljrq&pmod=reply_tj');
		showtableheader(lang('plugin/aljrq','count_4').$brnum.'&nbsp;&nbsp;'.lang('plugin/aljrq','count_5').$bznum.'&nbsp;&nbsp;'.lang('plugin/aljrq','count_6').$bynum);
		include template('aljrq:admin_keyword');
		showsubtitle(array('tid',lang('plugin/aljrq','count_1'),lang('plugin/aljrq','count_7'), lang('plugin/aljrq','count_8'),lang('plugin/aljrq','count_9'),lang('plugin/aljrq','count_10'),lang('plugin/aljrq','count_11')));
		$currpage=$_GET['page']?$_GET['page']:1;
		$perpage=25;
		$start=($currpage-1)*$perpage;
		$con=" where 1";
		if(submitcheck('seoeye_submit')) {
			$keyword=addcslashes($_GET['keyword'], '%_');
			$tid=intval($_GET['tid']);
			$kaishi=strtotime($_GET['kaishi']);
			$jiesu=strtotime($_GET['jiesu']);
			
			if($keyword){
				$con.=" and username like '%$keyword%'";
			}
			if($tid){
				$con.=" and tid =$tid";
			}
			if($kaishi&&$jiesu){
				$con.=" and dateline >= $kaishi and dateline <= $jiesu";
			}
		}
		$forums=C::t('forum_forum')->range();
		$num=DB::result_first('select count(*) from '.DB::table('alj_count')." $con ");
		//debug($con);
		$c_query=DB::query('select * from '.DB::table('alj_count')." $con order by dateline desc limit $start,$perpage");

		while($c_arr=DB::fetch($c_query)){
			$counts[]=$c_arr;
		}
		//$counts=DB::fetch_all('select * from '.DB::table('alj_count')." $con order by dateline desc limit $start,$perpage");
		$paging = helper_page :: multi($num, $perpage, $currpage, 'admin.php?action=plugins&operation=config&identifier=aljrq&pmod=reply_tj&tid='.$tid.'&kaishi='.$kaishi.'&jiesu='.$jiesu, 0, 10, false, false);
		foreach($counts as $tongji){
			$dateline=date('Y-m-d H:i:s',$tongji['dateline']);
			showtablerow('', array('', 'style="width:200px;"', 'class="td_k"', 'style="width:480px;"','class="td_l"','class="td_l"','style="width:50px;"'), array(							
							$tongji['tid'],	
							$tongji['subject'],	
							$forums[$tongji['fid']]['name'],	
							$tongji['huitie'],
							$tongji['username'],
							$dateline,		
							'<a target="_blank" href="forum.php?mod=viewthread&tid='.$tongji['tid'].'">'.lang('plugin/aljrq','count_12').'</a>',		
							));
			
		}
		showsubmit('', '', '','',$paging);
		showtablefooter();
		showformfooter();
	}
}
?>
