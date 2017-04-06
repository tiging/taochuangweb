<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$orderby = trim($_GET['orderby']);
$orderbyarray = array('days','monthdays','credit');
if(!in_array($orderby,$orderbyarray))$orderby = 'days';
$qdlists = C::t('#dc_signin#dc_signin')->getrange(0,20,$orderby,'DESC');
$qddj = C::t('#dc_signin#dc_signin_group')->getdata();
?>