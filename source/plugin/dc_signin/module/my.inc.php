<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$page = dintval($_GET['page']);
$page = $page?$page:1;
$perpage = 20;
$start = ($page-1)*$perpage;
$qdlists = C::t('#dc_signin#dc_signin_history')->fetch_all_by_where(DB::field('uid',$_G['uid']),$start,$perpage,'DESC');
$qdcount = C::t('#dc_signin#dc_signin_history')->count_by_where(DB::field('uid',$_G['uid']));
$mpurl = 'plugin.php?id=dc_signin&action=qdlist';
$multipage = multi($qdcount, $perpage, $page, $mpurl);
?>