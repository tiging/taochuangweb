<?php
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if($_GET['op']=='update'){
	scanfile();
	cpmsg(lang('plugin/aljrq','avatar_6'),'action=plugins&operation=config&identifier=aljrq&pmod=avatar', 'succeed');
}else{
	echo '<table class="tb tb2 " id="tips">
	<tr>
		<th  class="partition">'.lang('plugin/aljrq','avatar_1').'</th>
	</tr>
	<tr>
		<td class="tipsblock" s="1">
			<ul id="tipslis">
				<li>'.lang('plugin/aljrq','avatar_2').'</li>
				<li>'.lang('plugin/aljrq','avatar_3').'</li>
				<li>'.lang('plugin/aljrq','avatar_4').'</li>
				<li>'.lang('plugin/aljrq','avatar_5').'</li>
			</ul>
		</td>
	</tr>
	<tr><td><a href="'.ADMINSCRIPT.'?action=plugins&operation=config&identifier=aljrq&pmod=avatar&op=update"><strong>'.lang('plugin/aljrq','avatar_1').'</strong></a></td></tr>
</table><br>';
}
function scanfile(){
	global $_G;
	$dir = DISCUZ_ROOT.'./source/plugin/aljrq/images/';
	$handle=opendir($dir); 
	$avatar=array();
	while(false!==($file=readdir($handle))){ 
		if(substr_count($file,'.jpg')){
			$avatar[]='source/plugin/aljrq/images/'.$file;
		}
	}
	@require_once libfile('function/cache');	
	writetocache('aljrq', getcachevars(array('avatar' => $avatar))); //将管理中心配置项写入缓存
}
?>