<?php


if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
$_lang = lang('plugin/dc_signin');
loadcache(array('dc_signinextend','plugin'));
if(!$_G['cache']['dc_signinextend']['llreward'])cpmsg($_lang['llrewarderror'], 'action=plugins&operation=config&do='.$pluginid.'&identifier=dc_signin&pmod=extend', 'error');
$llrewardlang = @include DISCUZ_ROOT.'./source/plugin/dc_signin/language/llreward.'.currentlang().'.php'; //¶ÁÈ¡ ÀÛÁ¬½±ÀøÓïÑÔ°ü
if(empty($llrewardlang))$llrewardlang = @include DISCUZ_ROOT.'./source/plugin/dc_signin/language/llreward.php';
if($_GET['act']=='delete'){
	$id=dintval($_GET['id']);
	if(submitcheck('confirmed')){
		C::t('#dc_signin#dc_signin_llreward')->delete($id);
		cpmsg($_lang['deletesucceed'], 'action=plugins&operation=config&do='.$pluginid.'&identifier=dc_signin&pmod=llreward', 'succeed');
	}
	cpmsg($_lang['deletecheck'],'action=plugins&operation=config&do='.$pluginid.'&identifier=dc_signin&pmod=llreward&act=delete&id='.$id,'form');

}elseif($_GET['act']=='add'){
	$ext = get_ext();
	if(empty($ext))cpmsg($_lang['llreward_exterror'],'','error');
	if(submitcheck('submit')){
		$data = array(
			'ltype'=>trim($_GET['ltype']),
			'count'=>dintval($_GET['count']),
			'ext'=>trim($_GET['ext']),
			'msg'=>dhtmlspecialchars($_GET['msg']),
			'dateline'=>TIMESTAMP,
		);
		if(!$ext[$data['ext']])cpmsg($_lang['error'],'','error');
		$modstr = 'llrwext_'.$data['ext'];
		if (!class_exists($modstr,false))cpmsg($_lang['error'],'','error');
		$obj = new $modstr();
		if(!method_exists($obj,'setdata'))cpmsg($_lang['error'],'','error');
		$return= $obj->setdata();
		$data['data'] = serialize($return);
		C::t('#dc_signin#dc_signin_llreward')->insert($data);
		cpmsg($_lang['succeed'], 'action=plugins&operation=config&do='.$pluginid.'&identifier=dc_signin&pmod=llreward', 'succeed');
	}
	showformheader('plugins&operation=config&identifier=dc_signin&pmod=llreward&act=add');
	showtableheader($_lang['llrewardruleadd'], '');
	$ltype = array();
	foreach($_lang['lltype'] as $k=>$v)$ltype[]=array($k,$v);
	showsetting($_lang['llreward_type'], array('ltype',$ltype), '', 'select');
	showsetting($_lang['llreward_count'], 'count', '7', 'number');
	array_unshift($ext,array('',$_lang['selectreward']));
	showsetting($_lang['llreward_ext'], array('ext',$ext), '', 'select','','','','onchange="ajaxget(\''.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=dc_signin&pmod=llreward&act=getext&ext=\'+this.value, \'extinfo\');"');
	echo'</tbody><tbody id="extinfo" class="sub">';
	
	echo'</tbody><tbody>';
	showsetting($_lang['llreward_msg'], 'msg', '', 'text','','',$_lang['llreward_msg_1']);
	showsubmit('submit');
	showtablefooter();
	showformfooter();
	dexit();
}elseif($_GET['act']=='edit'){
	$id=dintval($_GET['id']);
	$llr=C::t('#dc_signin#dc_signin_llreward')->fetch($id);
	if(empty($llr))cpmsg($_lang['error'],'','error');
	$ext = get_ext();
	if(submitcheck('submit')){
		$data = array(
			'ltype'=>trim($_GET['ltype']),
			'count'=>dintval($_GET['count']),
			'ext'=>trim($_GET['ext']),
			'msg'=>dhtmlspecialchars($_GET['msg']),
		);
		if(!$ext[$data['ext']])cpmsg($_lang['error'],'','error');
		$modstr = 'llrwext_'.$data['ext'];
		if (!class_exists($modstr,false))cpmsg($_lang['error'],'','error');
		$obj = new $modstr();
		if(!method_exists($obj,'setdata'))cpmsg($_lang['error'],'','error');
		$return= $obj->setdata();
		$data['data'] = serialize($return);
		C::t('#dc_signin#dc_signin_llreward')->update($id,$data);
		cpmsg($_lang['succeed'], 'action=plugins&operation=config&do='.$pluginid.'&identifier=dc_signin&pmod=llreward', 'succeed');
	}
	showformheader('plugins&operation=config&identifier=dc_signin&pmod=llreward&act=edit&id='.$id);
	showtableheader($_lang['edit'], '');
	$ltype = array();
	foreach($_lang['lltype'] as $k=>$v)$ltype[]=array($k,$v);
	showsetting($_lang['llreward_type'], array('ltype',$ltype), $llr['ltype'], 'select');
	showsetting($_lang['llreward_count'], 'count', $llr['count'], 'number');
	array_unshift($ext,array('',$_lang['selectreward']));
	showsetting($_lang['llreward_ext'], array('ext',$ext), $llr['ext'], 'select','','','','onchange="ajaxget(\''.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=dc_signin&pmod=llreward&act=getext&ext=\'+this.value, \'extinfo\');"');
	echo'</tbody><tbody id="extinfo" class="sub">';
	if($ext[$llr['ext']]){
		C::import('llrwext/'.$llr['ext'],'plugin/dc_signin/extend/llreward',false);
		$modstr = 'llrwext_'.$llr['ext'];
		if (class_exists($modstr,false)){
			$obj = new $modstr();
			if(method_exists($obj,'getdata')){
				$llrewarddata = dunserialize($llr['data']);
				$obj->getdata();
			}
		}
	}
	echo'</tbody><tbody>';
	showsetting($_lang['llreward_msg'], 'msg', $llr['msg'], 'text','','',$_lang['llreward_msg_1']);
	showsubmit('submit');
	showtablefooter();
	showformfooter();
	dexit();
}elseif($_GET['act']=='getext'){
	$ext = trim($_GET['ext']);
	include template('common/header_ajax');
	if(!$ext||!preg_match("/^[a-z0-9_\-]+$/i", $ext)){
		echo'<tr><td clas="td27" colspan="2">'.$_lang['selectrewardright'].'</td></tr>';
	}else{
		C::import('llrwext/'.$ext,'plugin/dc_signin/extend/llreward',false);
		$modstr = 'llrwext_'.$ext;
		if (class_exists($modstr,false)){
			$obj = new $modstr();
			if(method_exists($obj,'getdata'))
				$obj->getdata();
			else
				echo'<tr><td clas="td27" colspan="2">'.$_lang['selectrewardright'].'</td></tr>';
		}else{
			echo'<tr><td clas="td27" colspan="2">'.$_lang['selectrewardright'].'</td></tr>';
		}
			
	}
	include template('common/footer_ajax');
	
	
}
showtips($_lang['llrewardtips']);
showtableheader($_lang['llrewardrule'].'(<a href="'.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=dc_signin&pmod=llreward&act=add">'.$_lang['llrewardruleadd'].')', '');
showsubtitle(array('',$_lang['llreward_type'],$_lang['llreward_count'],$_lang['llreward_msg'],$_lang['llreward_dateline'],$_lang['extend_caozuo']));
$data = C::t('#dc_signin#dc_signin_llreward')->range(0,0);
foreach($data as $k => $d){
	showtablerow('', array('width="20"'), 
			array('',
			$_lang['lltype'][$d['ltype']],
			$d['count'],
			$d['msg'],
			dgmdate($d['dateline']),
			'<a href="'.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=dc_signin&pmod=llreward&act=edit&id='.$k.'">'.$_lang['edit'].'</a>   <a href="'.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=dc_signin&pmod=llreward&act=delete&id='.$k.'">'.$_lang['delete'].'</a>'));
	
}
showtablefooter();
function get_ext(){
	global $llrewardlang;
	$arr = array();
	$entrydir = DISCUZ_ROOT.'./source/plugin/dc_signin/extend/llreward/llrwext';
	if(file_exists($entrydir)) {
		$d = dir($entrydir);
		while($f = $d->read()) {
			if($f!='.'&&$f!='..'&&!is_dir($entrydir.'/'.$f)){
				if(preg_match("/llrwext_([a-z0-9]+).php$/i", $f,$m)){
					C::import('llrwext/'.$m[1],'plugin/dc_signin/extend/llreward',false);
					$modstr = 'llrwext_'.$m[1];
					if (class_exists($modstr,false)){
						$obj = new $modstr();
						$arr[$m[1]] = array($m[1],$llrewardlang[$obj->title]?$llrewardlang[$obj->title]:$obj->title);
					}
				}
			}
		}
	}
	return $arr;
}
?>