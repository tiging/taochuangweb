<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$sql = <<<EOF
DROP TABLE IF EXISTS `pre_dc_signin`;
DROP TABLE IF EXISTS `pre_dc_signin_emot`;
DROP TABLE IF EXISTS `pre_dc_signin_group`;
DROP TABLE IF EXISTS `pre_dc_signin_history`;
EOF;
runquery($sql);
loadcache(array('dc_signinextend'));
C::import('extend/install','plugin/dc_signin',false);
foreach($_G['cache']['dc_signinextend'] as $k=>$ext){
	C::import($k.'/install','plugin/dc_signin/extend',false);
	$modstr = $k.'_install';
	if (class_exists($modstr,false)){
		$mobj = new $modstr();
		if(in_array('uninstall',get_class_methods($mobj))){
			$mobj->uninstall();
		}
	}
}
C::t('common_syscache')->delete(array('dc_signinextend','dc_signinstats');
$finish = TRUE;
?>