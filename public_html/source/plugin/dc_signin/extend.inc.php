<?php
/**
 *      [Discuz!] (C)2015-2099 DARK Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: extend.inc.php 7093 2015-01-23 10:28:15Z wang11291895@163.com $
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
$_lang = lang('plugin/dc_signin');
loadcache('dc_signinextend');
$config = $_G['cache']['dc_signinextend'];
if($_GET['act']=='install'){
	$f=trim($_GET['f']);
	if(submitcheck('confirmed')){
		if(!install($f))
			cpmsg($_lang['installerror'], 'action=plugins&operation=config&do='.$pluginid.'&identifier=dc_signin&pmod=extend', 'error');
		cpmsg($_lang['installsucceed'], 'action=plugins&operation=config&do='.$pluginid.'&identifier=dc_signin&pmod=extend', 'succeed',array('identify' =>$f));
	}
	cpmsg($_lang['intstallcheck'],'action=plugins&operation=config&do='.$pluginid.'&identifier=dc_signin&pmod=extend&act=install&f='.$f,'form', array('identify' => $f));
}elseif($_GET['act']=='upgrade'){
	$f=trim($_GET['f']);
	if(!$config[$f])
		cpmsg($_lang['error'], 'action=plugins&operation=config&do='.$pluginid.'&identifier=dc_signin&pmod=extend', 'error');
	if(submitcheck('confirmed')){
		if(!upgrade($f))
			cpmsg($_lang['ugradeerror'], 'action=plugins&operation=config&do='.$pluginid.'&identifier=dc_signin&pmod=extend', 'error');
		cpmsg($_lang['ugradesucceed'], 'action=plugins&operation=config&do='.$pluginid.'&identifier=dc_signin&pmod=extend', 'succeed',array('title' =>$config[$f]['title']));
	}
	cpmsg($_lang['ugradecheck'],'action=plugins&operation=config&do='.$pluginid.'&identifier=dc_signin&pmod=extend&act=upgrade&f='.$f,'form', array('title' =>$config[$f]['title']));
}elseif($_GET['act']=='uninstall'){
	$f=trim($_GET['f']);
	if(!$config[$f])
		cpmsg($_lang['error'], 'action=plugins&operation=config&do='.$pluginid.'&identifier=dc_signin&pmod=extend', 'error');
	if(submitcheck('confirmed')){
		$paytitle = $config[$f]['title'];
		if(!uninstall($f))
			cpmsg($_lang['uninstallerror'], 'action=plugins&operation=config&do='.$pluginid.'&identifier=dc_signin&pmod=extend', 'error');
		cpmsg($_lang['uninstallsucceed'], 'action=plugins&operation=config&do='.$pluginid.'&identifier=dc_signin&pmod=extend', 'succeed',array('title' =>$paytitle));
	}
	cpmsg($_lang['unintstallcheck'],'action=plugins&operation=config&do='.$pluginid.'&identifier=dc_signin&pmod=extend&act=uninstall&f='.$f,'form', array('title' =>$config[$f]['title']));
}elseif($_GET['act']=='set'){
	$f=trim($_GET['f']);
	$m=trim($_GET['m']);
	if(!$config[$f])
		cpmsg($_lang['error'], 'action=plugins&operation=config&do='.$pluginid.'&identifier=dc_signin&pmod=extend', 'error');
	C::import('extend/admin','plugin/dc_signin',false);
	C::import($f.'/admin','plugin/dc_signin/extend',false);
	$modstr = $f.'_admin';
	if (class_exists($modstr,false)){
		$mobj = new $modstr();
		if(empty($m))
			$m = 'mrun';
		else
			$m = 'm'.$do;
		if(in_array($m,get_class_methods($mobj))){
			$mobj->$m();exit;
		}
	}
	cpmsg($_lang['error'], 'action=plugins&operation=config&do='.$pluginid.'&identifier=dc_signin&pmod=extend', 'error');
}
$payarr = array();
$payerror = false;
$entrydir = DISCUZ_ROOT.'./source/plugin/dc_signin/extend';
C::import('extend/install','plugin/dc_signin',false);
if(file_exists($entrydir)) {
	$d = dir($entrydir);
	while($f = $d->read()) {
		if($f!='.'&&$f!='..'&&is_dir($entrydir.'/'.$f)){
			if(!preg_match("/^[a-z0-9_\-]+$/i", $f))continue;
			C::import($f.'/install','plugin/dc_signin/extend',false);
			$modstr = $f.'_install';
			if (class_exists($modstr,false)){
				$obj = new $modstr();
				$payarr[$f] = array(
					'title'=>$obj->title,
					'des'=>$obj->des,
					'author'=>$obj->author,
					'version'=>$obj->version,
					'com'=>$obj->com,
				);
			}
		}
	}
}
showtableheader($_lang['extend_isinstall'], '');
showsubtitle(array('',$_lang['extend_title'],$_lang['extend_des'],$_lang['extend_author'],$_lang['extend_version'],$_lang['extend_caozuo']));
foreach($config as $k => $c){
	$verchk = false;
	if($payarr[$k]){
		$verchk = $payarr[$k]['version']>$c['version'];
		unset($payarr[$k]);
	}else{
		$payerror = true;
		unset($config[$k]);
		continue;
	}
	showtablerow('', array('width="20"', 'width="100"','class="td28"','class="td28"','class="td28"'), 
			array('',
			$c['title'],
			$c['des'],
			$c['author'],
			$c['version'],
			'<a href="'.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=dc_signin&pmod=extend&act=set&f='.$k.'">'.$_lang['set'].'</a>  '.($verchk?'<a href="?action=plugins&operation=config&do='.$pluginid.'&identifier=dc_signin&pmod=extend&act=upgrade&f='.$k.'" style="color:#FF0000">['.$_lang['upgrade'].']</a> ':'').' <a href="'.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=dc_signin&pmod=extend&act=uninstall&f='.$k.'">'.$_lang['uninstall'].'</a>'));
}
showtablefooter();
showtableheader($_lang['extend_isnoinstall'], '');
showsubtitle(array('',$_lang['extend_title'],$_lang['extend_des'],$_lang['extend_author'],$_lang['extend_version'],$_lang['extend_caozuo']));
$entrydir = DISCUZ_ROOT.'./source/plugin/dc_signin/extend';
C::import('extend/install','plugin/dc_signin',false);
foreach($payarr as $k => $c){
	showtablerow('', array('width="20"', 'width="100"','class="td28"','class="td28"', 'class="td28"'), array('',$c['title'],$c['des'],$c['author'],$c['version'],'<a href="'.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=dc_signin&pmod=extend&act=install&f='.$k.'">'.$_lang['install'].'</a>'));
}
showtablefooter();
if($payerror){
	writeconfig($config);
}
function uninstall($name){
	global $config;
	if(!preg_match("/^[a-z0-9_\-]+$/i", $name))return;
	C::import('extend/install','plugin/dc_signin',false);
	C::import($name.'/install','plugin/dc_signin/extend',false);
	$modstr = $name.'_install';
	if (class_exists($modstr,false)){
		$mobj = new $modstr();
		if(in_array('uninstall',get_class_methods($mobj))){
			if($mobj->uninstall()===false)
				return false;
		}
		unset($config[$name]);
		return writeconfig($config);
	}
	return false;
}
function install($name){
	global $config;
	if($config[$name]||!preg_match("/^[a-z0-9_\-]+$/i", $name))
		return;
	C::import('extend/install','plugin/dc_signin',false);
	C::import($name.'/install','plugin/dc_signin/extend',false);
	$modstr = $name.'_install';
	if (class_exists($modstr,false)){
		$mobj = new $modstr();
		if(in_array('install',get_class_methods($mobj))){
			if($mobj->install()===false)
				return false;
		}
		$config[$name]['title']=$mobj->title;
		$config[$name]['des']=$mobj->des;
		$config[$name]['author']=$mobj->author;
		$config[$name]['version']=$mobj->version;
		$config[$name]['com']=$mobj->com;
		return writeconfig($config);
	}
	return false;
}
function upgrade($name){
	global $config;
	if(!$config[$name]||!preg_match("/^[a-z0-9_\-]+$/i", $name))
		return;
	C::import('extend/install','plugin/dc_signin',false);
	C::import($name.'/install','plugin/dc_signin/extend',false);
	$modstr = $name.'_install';
	if (class_exists($modstr,false)){
		$mobj = new $modstr();
		if(in_array('upgrade',get_class_methods($mobj))){
			if($mobj->upgrade($config[$name]['version'])===false)
				return false;
		}
		$config[$name]['title']=$mobj->title;
		$config[$name]['des']=$mobj->des;
		$config[$name]['author']=$mobj->author;
		$config[$name]['version']=$mobj->version;
		$config[$name]['com']=$mobj->com;
		return writeconfig($config);
	}
	return false;
}
function writeconfig($config){
	savecache('dc_signinextend', $config);
	return true;
}
?>