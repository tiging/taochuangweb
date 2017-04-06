<?php

/**
 *      (C)2007-2012 singcere.net
 *      This is a freeware, But you have no right to modify or distribute
 *
 *      $Id$
 */


if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}

$setting = (array)unserialize($_G['setting']['singcere_wechat']);
$langs = &$scriptlang['singcere_wechat'];
$pluginver = $_G['setting']['plugins']['version'];
$op = in_array($_GET['op'], array('setting', 'list', 'send')) ? $_GET['op'] : 'setting';

admincp_showsubmenu(null, array(
	array($langs['admincp_tmplmsg_setting'], "plugins&operation=config&do=$pluginid&identifier=$plugin[identifier]&pmod=$module[name]&op=setting", $op == 'setting'),
	array($langs['admincp_tmplmsg_list'], "plugins&operation=config&do=$pluginid&identifier=$plugin[identifier]&pmod=$module[name]&op=list", $op == 'list'),
	array($langs['admincp_tmplmsg_send'], "plugins&operation=config&do=$pluginid&identifier=$plugin[identifier]&pmod=$module[name]&op=send", $op == 'send'),
));

if(submitcheck('tmplsubmit')) {
	foreach($_GET['setting']['tmpl_template'] as $key => $t) {
		if($t['id']) {
		    $t['enable'] = $t['enable'] == 1 ?  1 : 0;
			$setting['tmpl_template'][$key] =  array_merge($setting['tmpl_template'][$key], $t);
		}
	}

	$settings = array('singcere_wechat' => serialize($setting));
	C::t('common_setting')->update_batch($settings);
	updatecache('setting');
	cpmsg('setting_update_succeed', 'action=plugins&operation=config&do='.$pluginid.'&identifier='.$plugin['identifier'].'&pmod='.$module[name].'&op=setting', 'succeed');
}


check_default_tmpl();


if($op == 'setting') {
	showtips($langs['admincp_tmplmsg_setting_tips']);
	showformheader("plugins&operation=config&do=$plugin[pluginid]&identifier=$plugin[identifier]&pmod=$module[name]&op=setting");
	showtableheader($langs['admincp_tmplmsg_setting']);
	echo '<tr class="header"><th class="td25">'.cplang('enable').'</th><th class="td23">'.$langs['admincp_tmplmsg_name'].'</th><th class="td24">'.$langs['admincp_tmplmsg_code'].'</th><th class="td24">'.$langs['admincp_tmplmsg_title'].'</th><th class="td31">'.$langs['admincp_tmplmsg_tid'].'</th><th class="td21">'.$langs['admincp_tmplmsg_sendflood'].'</th><th>'.$langs['admincp_tmplmsg_allowdisable'].'</th></tr>';
	foreach($setting['tmpl_template'] as $key => $tmpl) {
		$checked1 = $setting['tmpl_template'][$key]['enable'] ? 'checked="checked"' : '';
		$checked2 = $setting['tmpl_template'][$key]['allowdisable'] ? 'checked="checked"' : '';
		showtablerow('', array(), array(
		"<input type='checkbox' value='1' name='setting[tmpl_template][$key][enable]' $checked1>",
		$tmpl['name'],
		$tmpl['code'],
		$tmpl['title'],
		"<input type='text' value='{$setting[tmpl_template][$key][id]}' size='60' name='setting[tmpl_template][$key][id]'>",
		"<input type='text' value='{$setting[tmpl_template][$key][sendflood]}' name='setting[tmpl_template][$key][sendflood]'>",
		//"<input type='checkbox' value='1' name='setting[tmpl_template][$key][allowdisable]' $checked2>"
		));
	}
	showsubmit('tmplsubmit', 'config');
	showtablefooter();
	showformfooter();
} else if($op == 'list') {
	$page = max(1, intval($_GET['page']));
	$perpage = 15;
	
	showtips($langs['admincp_tmplmsg_list_tips']);
	showtableheader($langs['admincp_tmplmsg_list']);
	
	echo '<tr><td colspan="15">';
	showformheader("plugins&operation=config&do=$plugin[pluginid]&identifier=$plugin[identifier]&pmod=$module[name]&op=list");
	echo '<br />'.$langs['admincp_tmplmsg_tid'].': <input type="text" name="templateid" value="'.$_GET['templateid'].'" size="55"/> &nbsp; <select name="errcode"><option value = "">'.$langs['admincp_tmplmsg_unlimit'].'</option><option value="1" '.($_GET['errcode'] ? 'selected="selected"' : '').'>'.$langs['admincp_tmplmsg_limit_errorcode'].'</option></select> &nbsp; <select name="status"><option value = "">'.$langs['admincp_tmplmsg_unlimit'].'</option><option value="1" '.($_GET['status'] ? 'selected="selected"' : '').'>'.$langs['admincp_tmplmsg_limit_status'].'</option></select> &nbsp;<input type="submit" name="searchsubmit" value="'.$lang[search].'" class="btn" /><br />';
	showformfooter();
	echo '</td></tr>';
	
	
	$wherearr = array();
	if(!empty($_GET['templateid'])) {
		$_GET['templateid'] = dhtmlspecialchars($_GET['templateid']);
		$wherearr[] = DB::field('template', $_GET['templateid']);
	}
	if(!empty($_GET['errcode'])) {
		$wherearr[] = DB::field('errcode', 0, '<>');
	}
	if(!empty($_GET['status'])) {
		$wherearr[] = DB::field('status', 'success', '<>');
	}
	$wheresql = empty($wherearr)?'1':implode(' AND ', $wherearr);
	
	
	echo '<tr class="header"><th>'.$langs['admincp_tmplmsg_posttime'].'</th><th>'.$langs['admincp_tmplmsg_mid'].'</th><th>'.$langs['admincp_tmplmsg_tid'].'</th><th>'.$langs['admincp_tmplmsg_rtncode'].'</th><th>'.$langs['admincp_tmplmsg_status'].'</th></tr>';
	
	$count = DB::result_first("SELECT COUNT(*) FROM %t WHERE ".$wheresql, array('singcere_wechat_tmplmsg'));
	
	//$count = C::t('#singcere_wechat#singcere_wechat_tmplmsg')->count();
	if($count) {
		//$msglist = C::t('#singcere_wechat#singcere_wechat_tmplmsg')->fetch_all(($page-1)*$perpage, $perpage);
	
		$msglist = DB::fetch_all("SELECT * FROM %t WHERE %i ORDER BY dateline DESC ".DB::limit(($page-1)*$perpage, $perpage), array('singcere_wechat_tmplmsg', $wheresql));
	
		foreach($msglist as $msgid => $msg) {
			$msg['data'] = unserialize($msg['data']);
			showtablerow('', array('class="td21"', 'class="td21"', '', 'class="td25"', 'class="td21"'), array(
			dgmdate($msg['dateline'], 'Y-m-d H:i'),
			$msg['msgid'],
			$msg['template'].' : '.$msg['data']['first']['value'],
			$msg['errcode'],
			($msg['status'] == 'success' ? '<img align="absmiddle" src="static/image/admincp/cloud/right.gif">'.$langs['admincp_tmplmsg_success'] : ' <img align="absmiddle" src="static/image/admincp/cloud/wrong.gif">'.$msg['status'])
			));
		}
		showsubmit('','','','', multi($count, $perpage, $page, ADMINSCRIPT."?action=plugins&operation=config&do=$plugin[pluginid]&identifier=$plugin[identifier]&pmod=$module[name]&op=list"));
	}
	showtablefooter();
} else {
	?>
	
	<script type="text/JavaScript">
        var rowtypedata = [
        [[1,'<input type="text" class="s-td-50" name="newkey[]" value="" />.DATA', ''], 
         [1,'<input type="text" class="td31" name="newvalue[]" value="" placeholder="<?php echo $langs[admincp_tmplmsg_send_tmplvalue]?>"/>', ''], 
         [1,'<input type="text" id="{1}_v" class="td31" name="newrgb[]" value="" placeholder="<?php echo $langs[admincp_tmplmsg_send_tmplcolor]?>" style="float:left; margin-right:10px"/><input id="{1}" onclick="{1}_frame.location=\'static/image/admincp/getcolor.htm?{1}|{1}_v\';showMenu({\'ctrlid\':\'{1}\'})" type="button" class="colorwd" value="" style="background: "><span id="{1}_menu" style="display: none"><iframe name="{1}_frame" src="" frameborder="0" width="210" height="148" scrolling="no"></iframe></span>', ''],
         [5, '<div><span class="lightfont"><?php echo $langs['admincp_cmdaddnotice']; ?></span> <a href="javascript:;" class="deleterow" onClick="deleterow(this)"><?php cplang('delete', null, true); ?></a></div>']]
        ];
    </script>
    <script src="static/js/calendar.js" type="text/javascript"></script>
    <?php 
	
	
    
    if(!submitcheck('sendsubmit', 1)) {
    	
    	$data = unserialize($setting['tmpl_send']);

    	showtips($langs['admincp_tmplmsg_send_tips']);
    	
    	showformheader("plugins&operation=config&do=$plugin[pluginid]&identifier=$plugin[identifier]&pmod=$module[name]&op=send");
    	
    	showtableheader($langs['admincp_tmplmsg_send_filter']);
    	
    	showsetting($langs['admincp_tmplmsg_send_user'], 'limit_user', $data['limit_user'], 'radio', 0, 1, $langs['admincp_tmplmsg_send_user_tips']);
    	showsetting($langs['admincp_tmplmsg_send_uid'], 'limit_user_uid', $data['limit_user_uid'], 'textarea', 0, 0, $langs['admincp_tmplmsg_send_uid_tips']);
    	showtagfooter('tbody');
    	
    	$opstr = '';
    	foreach(array(0,1,2) as $si) {
    		$chk = '';
    		if($data['sex'] == $si) $chk = 'selected="selected"';
    		$opstr .= "<option value='$si' $chk>".($si ? lang('plugin/singcere_wechat', 'admincp_user_sex_'.$si) : cplang('nolimit')).'</option>';
    	}
    	
    	showsetting($langs['admincp_tmplmsg_send_sex'], '', '', '<select name="sex">'.$opstr.'</select>', 0, 0, $langs['admincp_tmplmsg_send_sex_tips']);
    	showsetting($langs['admincp_tmplmsg_send_lastauth'], array('lastauth_before', 'lastauth_after'), array($data['lastauth_before'], $data['lastauth_after']), 'daterange', 0, 0, $langs['admincp_tmplmsg_send_lastauth_tips']);
    	showsetting($langs['admincp_tmplmsg_send_authcounts'], 'counts', $data['counts'], 'text', 0, 0, $langs['admincp_tmplmsg_send_authcounts_tips']);
    	
    	showtablefooter();
    	
    	showtableheader($langs['admincp_tmplmsg_send_tmplsetting']);
    	showsetting($langs['admincp_tmplmsg_send_tmplid'], 'tmplid', $data['tmplid'], 'text', 0, 0, $langs['admincp_tmplmsg_send_tmplid_tips']);
    	showsetting($langs['admincp_tmplmsg_send_tmplurl'], 'url', $data['url'], 'text', 0, 0, $langs['admincp_tmplmsg_send_tmplurl_tips']);
    	showsetting($langs['admincp_tmplmsg_send_tmpltopcolor'], 'topcolor', $data['topcolor'], 'color');
    	
    	echo '<tr><td colspan="2" class="td27" s="1">'.$langs['admincp_tmplmsg_send_tmpldata'].':</td></tr>';
    	foreach($data['newkey'] as $ix => $key) {
    		$id = random(4);
    		showtablerow('', array(), array(
    			'<input type="text" class="s-td-50" name="newkey[]" value="'.$key.'" />.DATA',
    			'<input type="text" class="td31" name="newvalue[]" value="'.$data['newvalue'][$ix].'" placeholder="'.$langs['admincp_tmplmsg_send_tmplvalue'].'"/>',
    			'<input type="text" id="'.$id.'_v" class="td31" name="newrgb[]" value="'.$data['newrgb'][$ix].'" placeholder="'.$langs['admincp_tmplmsg_send_tmplcolor'].'" style="float:left; margin-right:10px"/>
					<input id="'.$id.'" onclick="'.$id.'_frame.location=\'static/image/admincp/getcolor.htm?'.$id.'|'.$id.'_v\';showMenu({\'ctrlid\':\''.$id.'\'})" type="button" class="colorwd" value="" style="background-color:'.$data['newrgb'][$ix].' ">
					<span id="'.$id.'_menu" style="display: none"><iframe name="'.$id.'_frame" src="" frameborder="0" width="210" height="148" scrolling="no"></iframe></span>',
				'<div><a href="javascript:;" class="deleterow" onClick="deleterow(this)">'.cplang('delete').'</a></div>' 
    		));
    	}

    	
    	showtablerow('class="noborder"', array('colspan="15"'), array("<div><a href=\"###\" onclick=\"addrow(this, 0, 'rd' + Math.random().toString(36).substring(4,8))\" class=\"addtr\">".$langs['admincp_tmplmsg_send_tmpladd']."</a></div>"));
    	
    	
    	showsubmit('sendsubmit', $langs['admincp_tmplmsg_send_pub']);
    	showtablefooter();
    	
    	showformfooter();
    	
    } else {
    	$_G['singcere_wechat']['setting'] = $setting;
    	
    	$data = array();
    	$allowpost = array('limit_user', 'limit_user_uid', 'sex', 'lastauth_after', 'lastauth_before', 'counts', 'tmplid', 'url', 'topcolor', 'newkey', 'newvalue', 'newrgb');
    	foreach($_POST as $key => $post) {
    		if(in_array($key, $allowpost)) {
    			$data[$key] = $post;
    		}
    	}
    	
    	if($data) {
    		$setting['tmpl_send'] = serialize($data);
    		C::t('common_setting')->update_batch(array('singcere_wechat' => serialize($setting)));
			updatecache('setting');
    	} else {
    		$data = unserialize($setting['tmpl_send']);
    	}
    	
    	
    	
    	
    	$pertask = isset($_GET['pertask']) ? intval($_GET['pertask']) : 10;
    	$start = isset($_GET['start']) && $_GET['start'] > 0 ? intval($_GET['start']) : 0;
    	$next = $start + $pertask;
    	$nextlink = "action=plugins&operation=$operation&identifier=$plugin[identifier]&pmod=$module[name]&start=$next&pertask=$pertask&sendsubmit=yes&op=send&confirmed=true";
    	$processed = 0;
    	
    	$data['subscribe'] = 1;
    	if($data['limit_user']) {
    		$data['uid'] = dintval(explode(',', $data['limit_user_uid']), true);
    	}
    	
    	if($data['lastauth_before'] && $data['lastauth_after']) {
    		list($data['lastauth_before'], $data['lastauth_after']) = array(strtotime($data['lastauth_before']), strtotime($data['lastauth_after']));
    		$data['lastauth'] = array(min($data['lastauth_before'], $data['lastauth_after']), max($data['lastauth_before'], $data['lastauth_after']));
    	}

    	$data['data'] = array();
    	foreach($data['newkey'] as $ix => $key) {
    		$data['data'][$key] = array('value' => $data['newvalue'][$ix], 'color' => $data['newrgb'][$ix]);
    	}
    	
    	if(empty($data['tmplid']) || empty($data['data'])) {
    		cpmsg($langs['admincp_tmplmsg_send_error'], '', 'error');
    	}
    	
    	if(!$_GET['confirmed']) {
    		$count = C::t('#singcere_wechat#singcere_wechat_bind')->count_by_search($data);
    		cpmsg(lang('plugin/singcere_wechat', 'admincp_tmplmsg_send_confirm', array('count' => $count)), "action=plugins&operation=config&do=$plugin[pluginid]&identifier=$plugin[identifier]&pmod=$module[name]&op=send&sendsubmit=yes", 'form');
    	}
    	
    	
    	$binduser = C::t('#singcere_wechat#singcere_wechat_bind')->fetch_all_by_search($data, 'dateline DESC', $start, $pertask);
    	require_once DISCUZ_ROOT.'source/plugin/singcere_wechat/function/function_common.php';
    	if(count($binduser)) {
    		$processed = 1;
    		foreach($binduser as $user) {
    			sc_message_template($data['tmplid'], 'openid:'.$user['openid'], $data['url'], $data['topcolor'], $data['data']);
    		}
    	}
    	
    	if($processed) {
    		cpmsg($langs['admincp_tmplmsg_send_ing'].cplang('counter_processing', array('current' => $start, 'next' => $next)), $nextlink, 'loading');
    	} else {
    		cpmsg($langs['admincp_tmplmsg_send_finish'], "action=plugins&operation=$operation&identifier=$plugin[identifier]&pmod=$module[name]&op=send", 'succeed');
    	}
    }
}

function check_default_tmpl() {
    
	global $setting, $langs;
	$default_tmpl = array(
		'login' => array('name' => $langs['admincp_tmplmsg_name_login'], 'code' => 'OPENTM201673425', 'title' => $langs['admincp_tmplmsg_title_login']),
		'pm' => array('name' => $langs['admincp_tmplmsg_name_pm'], 'code' => 'OPENTM200605630', 'title' => $langs['admincp_tmplmsg_title_forumreply']),
		'forumreply' => array('name' => $langs['admincp_tmplmsg_name_forumreply'], 'code' => 'OPENTM200605630', 'title' => $langs['admincp_tmplmsg_title_forumreply']),
		'fact' => array('name' => $langs['admincp_tmplmsg_name_fact'], 'code' => 'OPENTM200605630', 'title' => $langs['admincp_tmplmsg_title_forumreply']),
		'activity' => array('name' => $langs['admincp_tmplmsg_name_activity'], 'code' => 'TM005740', 'title' => $langs['admincp_tmplmsg_title_activity'])
	);
	
	foreach($default_tmpl as $tkey => $tmpl) {
	    $setting['tmpl_template'][$tkey] = (array)$setting['tmpl_template'][$tkey] + $tmpl;
	}

	C::t('common_setting')->update_batch(array('singcere_wechat' => serialize($setting)));
	updatecache('setting');
}

function admincp_showsubmenu($title, $menus = array(), $right = '', $replace = array()) {

	$s = '<div style="margin-top:5px;">' . $right . '<ul class="tab1">';
	foreach ($menus as $k => $menu) {
		if (is_array($menu[0])) {
			$s .= '<li id="addjs' . $k . '" class="' . ($menu[1] ? 'current' : 'hasdropmenu') . '" onmouseover="dropmenu(this);"><a href="#"><span>' . cplang($menu[0]['menu']) . '<em>&nbsp;&nbsp;</em></span></a><div id="addjs' . $k . 'child" class="dropmenu" style="display:none;">';
			if (is_array($menu[0]['submenu'])) {
				foreach ($menu[0]['submenu'] as $submenu) {
					$s .= $submenu[1] ? '<a href="' . ADMINSCRIPT . '?action=' . $submenu[1] . '" class="' . ($submenu[2] ? 'current' : '') . '" onclick="' . $submenu[3] . '">' . cplang($submenu[0]) . '</a>' : '<a><b>' . cplang($submenu[0]) . '</b></a>';
				}
			}
			$s .= '</div></li>';
		} else {
			$s .= '<li' . ($menu[2] ? ' class="current"' : '') . '><a href="' . (!$menu[4] ? ADMINSCRIPT . '?action=' . $menu[1] : $menu[1]) . '"' . (!empty($menu[3]) ? ' target="_blank"' : '') . '><span>' . cplang($menu[0]) . '</span></a></li>';
		}
	}
	$s .= '</ul></div>';

	echo!empty($menus) ? '<div class="itemtitle">' . $s . '</div>' : $s;
}
