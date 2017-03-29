<?php

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
$_D['IFRAME']='admin.php?targettplname=&blockclass=&bid=&name=nfocusdeyi&orderby=&ordersc=desc&perpage=20&action=block&operation=jscall&permname=&searchsubmit=%CB%D1%CB%F7';
showtableheader();
	echo '<tr><td>
		<iframe src="'.$_D['IFRAME'].'" style="width:1000px; height:1150px; border:0;" frameborder=0 height=100% scrolling="no"></iframe>
		</td></tr>';
showtablefooter();

?>