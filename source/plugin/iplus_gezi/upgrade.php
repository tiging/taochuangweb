<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$upgrade_style=0;
$query = DB::query("SHOW COLUMNS FROM ".DB::table('iplus_gezi'));
while($temp = DB::fetch($query)) {
	if($temp['Field']== 'style') {
		$upgrade_style=1;
		break;
	}
}
if($upgrade_style==0){
	DB::query("ALTER table ".DB::table('iplus_gezi')." ADD `style` varchar(255) NOT NULL;");
}
$finish = TRUE;
?>