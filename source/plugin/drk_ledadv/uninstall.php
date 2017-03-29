<?php

/*
 * CopyRight  : (C)2012-2099 HaoTeam.
 * Document   : uninstall.php
 * Created on : 2012-10-19, 15:47:34
 * Author     : ÒªÃüµÄ¾Æ¹í(Drunkard)
 * Description: This is NOT a freeware, use is subject to license terms.
 */

if (!defined('IN_DISCUZ') && !defined('IN_ADMINCP')) {
    exit('Aecsse Denied');
}

loadcache('drk_plugin');
$mycache = dunserialize($_G['setting']['drk_plugin']);
unset($mycache['drk_ledadv']);
$data['svalue'] = serialize($mycache);
$data['skey'] = 'drk_plugin';
DB::insert("common_setting", $data, 0, 1);
$finish = TRUE;
?>