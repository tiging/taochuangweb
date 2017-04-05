<?php
/**
 *	[бћЧызЂВс(invite_aboc.uninstall)] (C)2014-2099 Powered by aboc.
 *	Version: 1.0.0
 *	Date: 2014-8-30 21:01
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$table = DB::table("invite_aboc");
$sql = <<<EOF
DROP TABLE IF EXISTS $table;

EOF;

runquery($sql);
$finish = true;
?>