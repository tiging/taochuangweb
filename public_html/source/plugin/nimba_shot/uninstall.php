<?php
/*
 * 主页：http://addon.discuz.com/?@ailab
 * 人工智能实验室：Discuz!应用中心十大优秀开发者！
 * 插件定制 联系QQ594941227
 * From www.ailab.cn
 */
 
if(!defined('IN_ADMINCP')) {
	exit('Access Denied');
}
if(file_exists(DISCUZ_ROOT.'./data/cache/cache_nimba_hoturl.php')) @unlink(DISCUZ_ROOT.'./data/cache/cache_nimba_hoturl.php');
if(file_exists(DISCUZ_ROOT.'./data/sysdata/cache_nimba_hoturl.php')) @unlink(DISCUZ_ROOT.'./data/sysdata/cache_nimba_hoturl.php');

$dir = DISCUZ_ROOT.'./data/sysdata/';
$handle=opendir($dir); 
while(false!==($file=readdir($handle))){ 
	if(substr_count($file,'cache_nimba_shot_')){
		@unlink(DISCUZ_ROOT.'./data/sysdata/'.$file);
	}
}

$finish = TRUE;

?>