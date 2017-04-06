<?php


if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
$_lang = lang('plugin/dc_signin');
if(submitcheck('submit')){
	$delete = dintval($_GET['delete'],true);
	$name = dhtmlspecialchars($_GET['name']);
	$displayorder = dintval($_GET['displayorder'],true);
	$text = dhtmlspecialchars($_GET['text']);
	$icon = dhtmlspecialchars($_GET['icon']);
	$newname = dhtmlspecialchars($_GET['newname']);
	$newdisplayorder = dintval($_GET['newdisplayorder'],true);
	$newicon = dhtmlspecialchars($_GET['newicon']);
	$newtext = dhtmlspecialchars($_GET['newtext']);
	if($delete){
		foreach($delete as $k => $dv){
			unset($name[$k]);
			unset($displayorder[$k]);
			unset($icon[$k]);
			unset($text[$k]);
		}
	}
	foreach($name as $k=>$gv){
		$d = array(
			'name'=>trim($gv),
			'displayorder'=>$displayorder[$k],
			'icon'=>trim($icon[$k]),
			'text'=>trim($text[$k]),
		);
		if(empty($d['name']))unset($d['name']);
		C::t('#dc_signin#dc_signin_emot')->update($k,$d);
	}
	foreach($newname as $k=>$gv){
		$gv = trim($gv);
		if(empty($gv))continue;
		$d = array(
			'name'=>$gv,
			'displayorder'=>$newdisplayorder[$k],
			'icon'=>trim($newicon[$k]),
			'text'=>trim($newtext[$k]),
		);
		C::t('#dc_signin#dc_signin_emot')->insert($d);
	}
	C::t('#dc_signin#dc_signin_emot')->delete($delete);
	cpmsg($_lang['setok'], 'action=plugins&operation=config&do='.$pluginid.'&identifier=dc_signin&pmod=emot', 'succeed');
}
$qde = C::t('#dc_signin#dc_signin_emot')->getdata();
showformheader('plugins&operation=config&do='.$pluginid.'&identifier=dc_signin&pmod=emot');
showtips($_lang['signinemottips']);
showtableheader($_lang['signinemotn'], '');
showsubtitle(array('',$_lang['signinemot']['displayorder'],$_lang['signinemot']['name'], $_lang['signinemot']['text'],$_lang['signinemot']['icon'],''));
foreach($qde as $e){
	showtablerow('', array('class="td25"', '', '', '', ''), array(
				"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[$e[id]]\" value=\"$e[id]\">",
				"<input type=\"text\" size=\"2\"name=\"displayorder[$e[id]]\" value=\"$e[displayorder]\">",
				"<input type=\"text\" size=\"12\" name=\"name[$e[id]]\" value=\"$e[name]\">",
				"<input type=\"text\" size=\"24\" name=\"text[$e[id]]\" value=\"$e[text]\">",
				"<input type=\"text\" size=\"12\"name=\"icon[$e[id]]\" value=\"$e[icon]\">",($e['icon']?"<img src=\"source/plugin/dc_signin/images/emot/$e[icon]\" onload=\"if(this.height>30) {this.resized=true; this.height=30;}\">":""),
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
		var typedata = [\'\',\'<input type="text" name="newdisplayorder[]" value="" size="2"/>\',\'<input type="text" name="newname[]" value="" size="12"/>\',\'<input type="text" name="newtext[]" value="" size="24"/>\',\'<input type="text" name="newicon[]" value="" size="12"/>\',\'\'];
		for(var i = 0; i <= typedata.length - 1; i++) {
			var cell = row.insertCell(i);
			cell.innerHTML = typedata[i];
		}
	}
		</script>';
?>