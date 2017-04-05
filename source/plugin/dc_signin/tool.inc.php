<?php


if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
$entrydir = DISCUZ_ROOT.'source/plugin/dc_signin/tool/';
$extends = array();
if(file_exists($entrydir)) {
	$d = dir($entrydir);
	while($f = $d->read()) {
		if(preg_match('/^tool\_(\w+)?\.php$/', $f, $a)){
			if(!in_array($a[1],array('base'))){
				C::import('tool/'.$a[1],'plugin/dc_signin',false);
				$modstr = 'tool_'.$a[1];
				if (class_exists($modstr,false)){
					$mobj = new $modstr();
					$extends[$a[1]] = array('title'=>plang($mobj->title),'des'=>plang($mobj->des));
				}
			}
		}
	}
}
if(!$extends) cpmsg(plang('no_tool'), '', 'error');
$f=trim($_GET['f']);
if($f&&$extends[$f]){
	C::import('tool/'.$f,'plugin/dc_signin',false);
	$modstr = 'tool_'.$f;
	if (class_exists($modstr,false)){
		$mobj = new $modstr();
		if(submitcheck('submit',true)&&FORMHASH==$_GET['formhash']){
			if(in_array('run',get_class_methods($mobj))){
				$mobj->run();
			}
			cpmsg(plang('setsucceed'), 'action=plugins&operation=config&do='.$pluginid.'&identifier=dc_signin&pmod=tool', 'succeed');
		}
		if(in_array('setting',get_class_methods($mobj))){
			$mobj->setting();
		}
	}
	exit;
}

showtableheader(plang('tool_list'));
$cv=explode(',',plang('tool_table'));
showsubtitle(array($cv[0],$cv[1],$cv[2]));
foreach($extends as $k => $v){
	showtablerow('', array('','', 'width="20%"'), array($v['title'],$v['des'], '<a href="?action=plugins&operation=config&identifier=dc_signin&pmod=tool&f='.$k.'">'.plang('tool_config').'</a>'));
}
showtablefooter();
function plang($str) {
	return lang('plugin/dc_signin', $str);
}
?>