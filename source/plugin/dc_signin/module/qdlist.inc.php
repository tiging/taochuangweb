<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$page = dintval($_GET['page']);
$page = $page?$page:1;
$perpage = 20;
$start = ($page-1)*$perpage;
$qdlists = C::t('#dc_signin#dc_signin_history')->range($start,$perpage,'DESC');
$qdcount = C::t('#dc_signin#dc_signin_history')->count();
$mpurl = 'plugin.php?id=dc_signin&action=qdlist';
$multipage = multi($qdcount, $perpage, $page, $mpurl);

?>