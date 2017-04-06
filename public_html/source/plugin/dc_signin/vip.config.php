<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
loadcache('dc_signinextend');
if(!$_G['cache']['dc_signinextend']['vipreward'])return;
$viprewardpath = DISCUZ_ROOT.'./source/plugin/dc_signin/data/vipreward.config.php';
$viprewardconfig = @include $viprewardpath;
if(!$viprewardconfig['open']||!$viprewardconfig['extcredit']||!$viprewardconfig['dcvip'])return;
return array(
	'vipsigin'=>array(
		'title'=>lang('plugin/dc_signin','vipsignin').'('.$_G['setting']['extcredits'][$viprewardconfig['extcredit']]['title'].')',
		'des'=>'vipsigninmsg',
		'type'=>'number',
	),
);
?>