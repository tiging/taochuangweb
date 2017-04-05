<?php


if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
$_lang = lang('plugin/dc_signin');
if(submitcheck('submit')){
	$delete = dintval($_GET['delete'],true);
	$grouptitle = dhtmlspecialchars($_GET['grouptitle']);
	$dayslower = dintval($_GET['dayslower'],true);
	$icon = dhtmlspecialchars($_GET['icon']);
	$newgrouptitle = dhtmlspecialchars($_GET['newgrouptitle']);
	$newdayslower = dintval($_GET['newdayslower'],true);
	$newicon = dhtmlspecialchars($_GET['newicon']);
	if($delete){
		foreach($delete as $k => $dv){
			unset($grouptitle[$k]);
			unset($dayslower[$k]);
			unset($icon[$k]);
		}
	}
	foreach($grouptitle as $k=>$gv){
		$d = array(
			'grouptitle'=>trim($gv),
			'dayslower'=>$dayslower[$k],
			'icon'=>trim($icon[$k]),
		);
		if(empty($d['grouptitle']))unset($d['grouptitle']);
		C::t('#dc_signin#dc_signin_group')->update($k,$d);
	}
	foreach($newgrouptitle as $k=>$gv){
		$gv = trim($gv);
		if(empty($gv))continue;
		$d = array(
			'grouptitle'=>$gv,
			'dayslower'=>$newdayslower[$k],
			'icon'=>trim($newicon[$k]),
		);
		C::t('#dc_signin#dc_signin_group')->insert($d);
	}
	C::t('#dc_signin#dc_signin_group')->delete($delete);
	cpmsg($_lang['setok'], 'action=plugins&operation=config&do='.$pluginid.'&identifier=dc_signin&pmod=group', 'succeed');
}
$qdgroup = C::t('#dc_signin#dc_signin_group')->getdata();
showformheader('plugins&operation=config&do='.$pluginid.'&identifier=dc_signin&pmod=group');
showtips($_lang['signingrouptips']);
showtableheader($_lang['signingroup'], '');
showsubtitle(array('',$_lang['groupname'],$_lang['dayslower'], $_lang['icon']));
foreach($qdgroup as $g){
	showtablerow('', array('class="td25"', '', '', '', ''), array(
				"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[$g[id]]\" value=\"$g[id]\">",
				"<input type=\"text\" class=\"txt\" size=\"12\" name=\"grouptitle[$g[id]]\" value=\"$g[grouptitle]\">",
				"<input type=\"text\" class=\"txt\" size=\"12\"name=\"dayslower[$g[id]]\" value=\"$g[dayslower]\">",
				"<input type=\"text\" class=\"txt\" size=\"12\"name=\"icon[$g[id]]\" value=\"$g[icon]\">".($g['icon']?"<img src=\"source/plugin/dc_signin/images/icon/$g[icon]\">":""),
			));
}
echo '<tr><td></td><td colspan="5"><div><a href="javascript:;" onclick="addrow(this)" class="addtr">'.$_lang['add'].'</a></div></td></tr>';
showsubmit('submit', 'submit');
showtablefooter();
showformfooter();
echo'<script type="text/javascript">
	function addrow(obj) {
		var table = obj.parentNode.parentNode.parentNode.parentNode.parentNode;
		var row = table.insertRow(obj.parentNode.parentNode.parentNode.rowIndex);
		var typedata = [\'\',\'<input type="text" name="newgrouptitle[]" value="" size="12"/>\',\'<input type="text" name="newdayslower[]" value="" size="12"/>\',\'<input type="text" name="newicon[]" value="" size="12"/>\'];
		for(var i = 0; i <= typedata.length - 1; i++) {
			var cell = row.insertCell(i);
			cell.innerHTML = typedata[i];
		}
	}
		</script>';
?>