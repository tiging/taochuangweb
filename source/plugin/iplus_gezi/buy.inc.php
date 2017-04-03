<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
loadcache('plugin');
$vars=$_G['cache']['plugin']['iplus_gezi'];
$mcredits=$vars['mcredits'];
$dcredits=$vars['dcredits'];
$lcredits=$vars['lcredits'];
$jfname=$_G['setting']['extcredits'][$vars['credits']]['title'];
$wzlen=intval($vars['wzlen']);
$mytips=$vars['mytips'];
$jf = getuserprofile('extcredits'.$_G['cache']['plugin']['iplus_gezi']['credits']);

if(!$_G['uid']){//游客
	showmessage(lang('plugin/iplus_gezi', 'userlogin'), '', array(), array('login' => true));
}
if(submitcheck('applysubmit')){
	if (empty($_POST['links'])||!preg_match("/^(http:|https:)\/\/([0-9a-zA-Z][0-9a-zA-Z-]*\.)+[a-zA-Z]{2,}/", $_POST['links'])){
		showmessage(lang('plugin/iplus_gezi','errorurl'));//链接有误
	}
	if($_POST['adlong']<=0){
		showmessage(lang('plugin/iplus_gezi','editerror'));//时间有误
	}
	if(empty($_POST['title'])){
		showmessage(lang('plugin/iplus_gezi','biaotinoll'));//标题为空
	}	
	$fontarr=array();
	if(file_exists(DISCUZ_ROOT.'./source/plugin/iplus_gezi/lib/font.lib.php')){
		@include DISCUZ_ROOT . './source/plugin/iplus_gezi/lib/font.lib.php';
	}		
	$data=array();
	$data['style']=serialize($fontarr);
	$data['uid']=intval($_G['uid']);
	$data['username']=addslashes($_G['username']);
	$data['title']=addslashes($_POST['title']);
	$data['url']=addslashes($_POST['links']);
	$data['dateline']=$_G['timestamp'];
	if($_POST['adstyle']==1){
		$data['lastdate']=86400*$_POST['adlong']+$data['dateline'];
		$total=$dcredits*$_POST['adlong'];
	}
	if($_POST['adstyle']==2){
		$data['lastdate']=2592000*$_POST['adlong']+$data['dateline'];
		$total=$mcredits*$_POST['adlong'];
	}
	$data['status']=0;
	updatemembercount($_G['uid'], array($vars['credits']=>-$total));
	DB::insert('iplus_gezi',$data);
	updateadlist();
	showmessage(lang('plugin/iplus_gezi','buy1').date('Y-m-d H:i:s',$data['dateline']).'-'.date('Y-m-d H:i:s',$data['lastdate']).lang('plugin/iplus_gezi', 'buy2').$total.lang('plugin/iplus_gezi', 'buy3').$jfname, dreferer(), array(), array('locationtime'=>2, 'showdialog'=>1, 'showmsg' => true, 'closetime' =>20));		
}else{
	if(file_exists(DISCUZ_ROOT.'./source/plugin/iplus_gezi/lib/font.lib.php')) $fonttip=1;
	else $fonttip=0;
	include template("iplus_gezi:buy");
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