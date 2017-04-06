<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$settingfile = DISCUZ_ROOT . './data/sysdata/cache_aljrq_setting.php';
if(file_exists($settingfile)){
	unlink($settingfile);
}
if(file_exists(DISCUZ_ROOT . './data/cache/cache_aljrq_setting.php')){
	$settingfile = DISCUZ_ROOT . './data/cache/cache_aljrq_setting.php';
	unlink($settingfile);
}
$settingfile = DISCUZ_ROOT . './data/sysdata/cache_fidcache.php';
if(file_exists($settingfile)){
	unlink($settingfile);
}
if(file_exists(DISCUZ_ROOT . './data/cache/cache_fidcache.php')){
	$settingfile = DISCUZ_ROOT . './data/cache/cache_fidcache.php';
	unlink($settingfile);
}
DB::query("delete FROM ".DB::table('common_session')." where ip1='0'");
$sql = <<<EOF
drop table IF EXISTS `pre_plugin_ljmajia`;
drop table IF EXISTS `pre_alj_session`;
drop table IF EXISTS `pre_alj_count`;
drop table IF EXISTS `pre_alj_count_pro`;
EOF;
runquery($sql);
$finish = TRUE;
?>