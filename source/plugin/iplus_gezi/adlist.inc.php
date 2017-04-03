<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
if(!$_G['uid']){//游客
	showmessage(lang('plugin/iplus_gezi', 'userlogin'), '', array(), array('login' => true));
}
$_GET['adadmin'] = dhtmlspecialchars($_GET['adadmin']);
if(!$_GET['adadmin']){
	if(!submitcheck('slidesubmit')){
		loadcache('plugin');
		$adcolor=$_G['cache']['plugin']['iplus_gezi']['adcolor'];
		$count = DB::result_first("SELECT COUNT(*) FROM ".DB::table('iplus_gezi'));
		$pagenum=20;
		$page=max(1,intval($_GET['page']));
		$start=($page-1)*$pagenum;
		$adlist=array();
		if($count) {
			$query = DB::query("SELECT * FROM ".DB::table('iplus_gezi')." where uid=".$_G['uid']." and lastdate>".time()." ORDER BY dateline DESC LIMIT $start,$pagenum");
			while($result = DB::fetch($query)) {
				$style='style="';
				$fontarr=unserialize($result['style']);
				if($fontarr['fontcolor']) $style.='color:'.$fontarr['fontcolor'].';';
				else $style.='color:'.$adcolor.';';
				if($fontarr['fontweight']==1) $style.='font-weight: bold;';
				if($fontarr['fontstyle']==1) $style.='font-style: italic;';
				if($fontarr['textdecoration']==1) $style.='text-decoration: underline;';
				$style.='"';			
				$result['style']=$style;
				$result['lastdate']=date('Y-m-d H:i:s',$result['lastdate']);
				$adlist[]=$result;
			}
		}
		$multi=multi($count, $pagenum, $page, ADMINSCRIPT.'?action=plugins&operation=config&identifier=iplus_gezi&pmod=data');		
	}else{
		cpmsg(iplus_gezi('addelok'), '', '', '', '<input class="btn" type="button" value="'.iplus_gezi('addeloks').'" onclick="location.href=\'admin.php?action=plugins&operation=config&do='.$pluginid.'&identifier=iplus_gezi&pmod=data&adadmin=del&del_id='.$del_id.'\'"/>&nbsp;&nbsp;<input class="btn" type="button" value="'.iplus_gezi('addelno').'" onclick="location.href=\'admin.php?action=plugins&operation=config&do='.$pluginid.'&identifier=iplus_gezi&pmod=data\'"/>');
	}
}elseif($_GET['adadmin']=='edit'){
	if(!submitcheck('linkschecksubmit')){
		$aid = intval($_GET['adid']);
		$query = DB::query("SELECT * FROM ".DB::table('iplus_gezi')." WHERE uid=".$_G['uid']." and id='$aid'");
		$adinfo = DB::fetch($query);
		$adinfo=dhtmlspecialchars($adinfo);
	}else{
		if (empty($_GET['url'])||!preg_match("/^(http:|https:)\/\/([0-9a-zA-Z][0-9a-zA-Z-]*\.)+[a-zA-Z]{2,}/", $_GET['url'])){
			showmessage(lang('plugin/iplus_gezi','errorurl'));//链接有误
		}
		if(empty($_GET['title'])){
			showmessage(lang('plugin/iplus_gezi','biaotinoll'));//标题为空
		}	
		$aid = intval($_GET['adid']);
		$title = daddslashes($_GET['title']);
		$url = daddslashes($_GET['url']);
		if($title&&$url){
			DB::update('iplus_gezi', array('title'=>$title,'url'=>$url),"id='$aid'");
			updateadlist();
			showmessage(lang('plugin/iplus_gezi','adokok'),'home.php?mod=spacecp&ac=plugin&id=iplus_gezi:adlist',array(),array('refreshtime'=>3));	
		}else{
			showmessage(lang('plugin/iplus_gezi','editerror'),'javascript:history.back()',array(),array('refreshtime'=>3));	
		}
	}
}elseif($_GET['adadmin']=='del'){
	if(count($_GET['delete'])!=0){
		foreach($_GET['delete'] as $k=>$id){
			$id=intval($id);
			DB::query("DELETE FROM ".DB::table('iplus_gezi')." WHERE uid=".$_G['uid']." and id='$id' LIMIT 1 ");
		}
		showmessage(lang('plugin/iplus_gezi','addesss'),'home.php?mod=spacecp&ac=plugin&id=iplus_gezi:adlist',array(),array('refreshtime'=>3));	
	}else{
		showmessage(lang('plugin/iplus_gezi','delerror'),'javascript:history.back()',array(),array('refreshtime'=>3));	
	}
	updateadlist();
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
	if($default){//默认补齐
		for($i=1;$i<=$default;$i++){
			$links[]=array('title'=>$vars['wztitle'],'url'=>'');
		}
	}
	@require_once libfile('function/cache');
	$cacheArray .= "\$links=".arrayeval($links).";\n\$lasttime=".$_G['timestamp'].";\n";
	writetocache('iplus_gezi', $cacheArray);			
}

?>