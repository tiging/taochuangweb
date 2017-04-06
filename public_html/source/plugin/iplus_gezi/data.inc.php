<?php
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$_GET['adadmin'] = dhtmlspecialchars($_GET['adadmin']);
if(!$_GET['adadmin']){
	if(!submitcheck('slidesubmit')){
		$count = DB::result_first("SELECT COUNT(*) FROM ".DB::table('iplus_gezi'));
		$pagenum=20;
		$page=max(1,intval($_GET['page']));
		$start=($page-1)*$pagenum;
		showtableheader(lang('plugin/iplus_gezi', 'adlog'), '');
		showformheader('plugins&operation=config&do='.$pluginid.'&identifier=iplus_gezi&pmod=data');
		showtableheader();
		showsubtitle(iplus_gezi(array( 'adname','aduser', 'starttime', 'endtime' , 'adtools')));
		if($count) {
			$query = DB::query("SELECT * FROM ".DB::table('iplus_gezi')." ORDER BY dateline DESC LIMIT $start,$pagenum");
			while($result = DB::fetch($query)) {
				$result=$result;
				showtablerow(NULL,NULL, array('<a href="'.$result['url'].'" title="'.$result['url'].'" target="_blank">'.$result['title'].'</a>','<a href="home.php?mod=space&uid='.$result['uid'].'" target="_blank">'.$result['username'].'</a>(UID:'.$result['uid'].')',date('Y-m-d H:i', $result['dateline']),'<font color="#0066FF">'.date('Y-m-d H:i', $result['lastdate']).'</font>','<a href="admin.php?action=plugins&operation=config&do='.$pluginid.'&identifier=iplus_gezi&pmod=data&adadmin=edit&aid='.intval($result['id']).'">'.lang ('plugin/iplus_gezi', 'adedit').'</a> <a href="admin.php?action=plugins&operation=config&do='.$pluginid.'&identifier=iplus_gezi&pmod=data&adadmin=del&del_id='.$result['id'].'">'.lang('plugin/iplus_gezi', 'addels').'</a>'));
			}
		}
		showtablerow();
		showtablefooter();
		showformfooter();
		echo multi($count, $pagenum, $page, ADMINSCRIPT.'?action=plugins&operation=config&identifier=iplus_gezi&pmod=data');		
	}else{
		cpmsg(iplus_gezi('addelok'), '', '', '', '<input class="btn" type="button" value="'.iplus_gezi('addeloks').'" onclick="location.href=\'admin.php?action=plugins&operation=config&do='.$pluginid.'&identifier=iplus_gezi&pmod=data&adadmin=del&del_id='.$del_id.'\'"/>&nbsp;&nbsp;<input class="btn" type="button" value="'.iplus_gezi('addelno').'" onclick="location.href=\'admin.php?action=plugins&operation=config&do='.$pluginid.'&identifier=iplus_gezi&pmod=data\'"/>');
	}
}elseif($_GET['adadmin']=='edit'){
	if(!submitcheck('slidesubmit')){
		$aid = intval($_GET['aid']);
		showtableheader(lang('plugin/iplus_gezi', 'admrpmh'), '');
		$id_info = DB::fetch_first("SELECT * FROM ".DB::table('iplus_gezi')." WHERE id='$aid'");
		$id_info =dhtmlspecialchars($id_info);
		showformheader('plugins&operation=config&do='.$pluginid.'&identifier=iplus_gezi&pmod=data&adadmin=edit&id='.intval($id_info['id']));
		showsetting(iplus_gezi('adyodd'), 'title', addslashes($id_info['title']), 'text','0');	
		showsetting(iplus_gezi('adlinks'), 'url', addslashes($id_info['url']), 'text','0');	
		showsubmit('slidesubmit', 'submit');
		showtablefooter();
		showformfooter();
	}else{
		$aid = intval($_GET['id']);
		$title = daddslashes($_GET['title']);
		$url = daddslashes($_GET['url']);
		DB::update('iplus_gezi', array('title'=>$title,'url'=>$url),"id='$aid'");
		updateadlist();
		cpmsg(iplus_gezi('adokok'),'action=plugins&operation=config&do='.$pluginid.'&identifier=iplus_gezi&pmod=data');
	}
}elseif($_GET['adadmin']=='del'){
	$aid = intval($_GET['del_id']);
	$deichek = DB::result_first("SELECT COUNT(*) FROM ".DB::table('iplus_gezi')." WHERE id='$aid'");
	if(!$deichek) {
		cpmsg_error(iplus_gezi('adweis'));
	} else {
		DB::query("DELETE FROM ".DB::table('iplus_gezi')." WHERE id='$aid' LIMIT 1 ");
	}
	updateadlist();
	cpmsg(iplus_gezi('addesss'),'action=plugins&operation=config&do='.$pluginid.'&identifier=iplus_gezi&pmod=data');
}

function iplus_gezi($str) {
	if(is_array($str)) {
		$return = array();
		foreach($str as $value) {
			$return[] = iplus_gezi($value);
		}
		return $return;
	} else {
		return lang('plugin/iplus_gezi', $str);
	}
}
function updateadlist(){
	global $_G;
	loadcache('plugin');
	$vars=$_G['cache']['plugin']['iplus_gezi'];
	$maxnum=5*intval($vars['rows']);
	$wzcolor=$vars['wzcolor'];
	$adcolor=$vars['adcolor'];
	$wzlen=intval($vars['wzlen']);
	$query = DB::query( "SELECT title,url,style FROM ".DB::table('iplus_gezi')." where lastdate>".$_G['timestamp']." ORDER BY id ASC LIMIT $maxnum");
	$links=array();
	$i=0;
	while($value=DB::fetch($query)){
		if($value){
			$style='style="';
			$fontarr=unserialize($value['style']);
			if($fontarr['fontcolor']) $style.='color:'.$fontarr['fontcolor'].';';
			else $style.='color:'.$adcolor.';';
			if($fontarr['fontweight']==1) $style.='font-weight: bold;';
			if($fontarr['fontstyle']==1) $style.='font-style: italic;';
			if($fontarr['textdecoration']==1) $style.='text-decoration: underline;';
			$style.='"';
			$value['style'] = $style;		
			$value['title'] = dhtmlspecialchars(cutstr($value['title'],$wzlen,''));
			$links[]=$value;
			$i+=1;
		}
	}
	$default=$maxnum-$i;
	if($default){//Ä¬ÈÏ²¹Æë
		for($i=1;$i<=$default;$i++){
			$links[]=array('title'=>$vars['wztitle'],'url'=>'');
		}
	}
	@require_once libfile('function/cache');
	$cacheArray .= "\$links=".arrayeval($links).";\n\$lasttime=".$_G['timestamp'].";\n";
	writetocache('iplus_gezi', $cacheArray);			
}
?>
