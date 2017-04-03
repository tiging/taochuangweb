<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: uninstall.php 6752 2010-03-25 08:47:54Z cnteacher $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$sql = "delete from ".DB::table('common_block')." where name like '%nfocusdeyi%'";

runquery($sql);

$sql = "delete from ".DB::table('common_block')." where name like '%it618_firstnfocus_deyi%'";

runquery($sql);

$finish = TRUE;

?>