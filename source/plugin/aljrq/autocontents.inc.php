<?php
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
echo '<table class="tb tb2 " id="tips">
	<tr>
		<th  class="partition">'.lang('plugin/aljrq','autocentents_1').'</th>
	</tr>
	<tr>
		<td class="tipsblock" s="1">
			<ul id="tipslis">
				<li>'.lang('plugin/aljrq','autocentents_2').'</li>
				<li>'.lang('plugin/aljrq','autocentents_3').'</li>
			</ul>
		</td>
	</tr>
</table><br>';
if(file_exists(DISCUZ_ROOT . './data/sysdata/cache_fidcache.php')){
	include_once DISCUZ_ROOT . './data/sysdata/cache_fidcache.php';
}else if(file_exists(DISCUZ_ROOT . './data/cache/cache_fidcache.php')){
	include_once DISCUZ_ROOT . './data/cache/cache_fidcache.php';
}
$do=daddslashes($_GET['do']);
$op=daddslashes($_GET['op']);
if(!$op){
	$op='fids_dz';
}
$config = array();
foreach($pluginvars as $key => $val) {

	$config[$key] = $val['value'];
	
}
$_G['cache']['plugin']['aljrq'] = $config;
include template ( 'aljrq:nav_huifu' );
if($op=='fids_dz'){
	if(submitcheck('editsubmit')){
		$fidcache['fids']=$_GET['varsnew'];
		require_once libfile('function/cache');
		writetocache('fidcache', getcachevars(array('fidcache' => $fidcache)));  
		cpmsg(lang('plugin/aljrq','aljrq1'),'action=plugins&operation=config&do=$do&identifier=aljrq&pmod=autocontents&op=fids_dz');

	}
	$fids = $config['bankuai'];
	$fids = unserialize ($fids);
	$query=DB::query("select fid,name from ".DB::table('forum_forum'));
	while($arr=DB::fetch($query)){
		$forums[$arr[fid]]=$arr[name];
	}
	include template('aljrq:fids_dz');
}else if($op=='tids_dz'){
	if(submitcheck('editsubmit')){
		$fidcache['tids']=$_GET['varsnew'];
		require_once libfile('function/cache');
		writetocache('fidcache', getcachevars(array('fidcache' => $fidcache)));  
		cpmsg(lang('plugin/aljrq','aljrq1'),'action=plugins&operation=config&do=$do&identifier=aljrq&pmod=autocontents&op=tids_dz');

	}
	$tids = $config['dttid'];
	$tids = str_replace ("£¬", ",", $tids);
	$tids =trim($tids,',');
	$tids = explode(",", $tids);
	
	include template('aljrq:tids_dz');
}
?>