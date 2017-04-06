<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
if($mysignin){
	$qdgroup = C::t('#dc_signin#dc_signin_group')->getdata();
	foreach($qdgroup as $gd){
		if($gd['dayslower']>$mysignin['days']){
			$upgrade = $gd;
			break;
		}
	}
}
?>