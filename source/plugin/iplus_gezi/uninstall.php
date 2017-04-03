<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
DB::query("DROP TABLE IF EXISTS ".DB::table('iplus_gezi')."");
if(DISCUZ_VERSION=='X2') $filepath=DISCUZ_ROOT.'./data/cache/cache_iplus_gezi.php';
if(DISCUZ_VERSION=='X2.5'||DISCUZ_VERSION=='X3 Beta'||DISCUZ_VERSION=='X3 RC'||DISCUZ_VERSION=='X3'||DISCUZ_VERSION=='X3.1') $filepath=DISCUZ_ROOT.'./data/sysdata/cache_iplus_gezi.php';
if(file_exists($filepath)) @unlink($filepath);
$finish = TRUE;
?>