<?php

/**
 *      [S!] (C)2007-2014 Singcere Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: cron_singcere_haodian_cleanup_hourly.php 10 2014-02-22 10:03:34Z except10n $
 */

/**
 *      (C)2007-2013 singcere.net
 *      This is a freeware, But you have no right to modify or distribute
 *      
 */

//cronname:cron_singcere_wechat_cleanup
//week:-1 
//day:-1    
//hour:-1  
//minute:05 

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

DB::delete('singcere_wechat_pay', DB::field('transaction_id', '').' AND '.DB::field('dateline', TIMESTAMP - 86400, '<'));


?>
