<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: menu_setting.inc.php 34754 2014-07-29 03:16:20Z nemohou $
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$langs = &$scriptlang['singcere_wechat'];
require_once DISCUZ_ROOT . './source/plugin/singcere_wechat/class/wechat.lib.class.php';

$menusetting = (array)unserialize($_G['setting']['singcere_wechat_selfmenu']);
$wechatsetting = (array)unserialize($_G['setting']['singcere_wechat']);
$wechatsetting['wechat_appsecret'] = authcode($wechatsetting['wechat_appsecret'], 'DECODE', $_G['config']['security']['authkey']);

if(!$wechatsetting['wechat_appId'] || !$wechatsetting['wechat_appsecret']) {
	cpmsg($langs['admincp_selfmenu_alert'], '', 'error');
}

if(submitcheck('loadsubmit', true)) {
    if(!$_GET['confirmed']) {
        cpmsg(lang('plugin/singcere_wechat', 'admincp_selfmenu_loadconfirm'), "action=plugins&operation=config&do=$plugin[pluginid]&identifier=$plugin[identifier]&pmod=$module[name]&loadsubmit=yes", 'form');
    }
    $wechat_client = new WeChatClient($wechatsetting['wechat_appId'], $wechatsetting['wechat_appsecret']);
    $data = $wechat_client->getMenu();
    if(!$data) {
        cpmsg($langs['admincp_selfmenu_loadfailed'], '', 'error');
    }
    $menusetting = sc_diconv($data['menu'], 'utf-8');
    
    foreach($menusetting['button'] as $fkey => &$fmenu) {
        $fmenu['displayorder'] = $fkey;
        $fmenu['keyurl'] = $fmenu['type'] == 'view' ? $fmenu['url'] : $fmenu['key'];
        foreach($fmenu['sub_button'] as $skey => &$smenu) {
            $smenu['displayorder'] = $skey;
            $smenu['keyurl'] = $smenu['type'] == 'view' ? $smenu['url'] : $smenu['key'];
        }
    }
    
    $settings = array('singcere_wechat_selfmenu' => serialize($menusetting));
    C::t('common_setting')->update_batch($settings);
    updatecache('setting');
    cpmsg('setting_update_succeed', "action=plugins&operation=config&do=$pluginid&identifier=$plugin[identifier]&pmod=$module[name]", 'succeed');
} else if(submitcheck('menusubmit') || submitcheck('pubsubmit')) {
    if(!empty($_GET['newbutton'])) {
        foreach($_GET['newbutton']['name'] as $k => $name) {
            $button = array(
                'displayorder' => $_GET['newbutton']['displayorder'][$k],
                'name' => $name,
                'keyurl' => $_GET['newbutton']['keyurl'][$k],
            );
            $menusetting['button'][] = $button;
        }
    }
    
    foreach($_GET['button'] as $k => $value) {
        if($value['sub_button']) {
            foreach($value['sub_button'] as $sk => $v) {
                if($v['delete']) {
                    unset($value['sub_button'][$sk]);
                }
            }
        }
        if($value['delete']) {
            unset($menusetting['button'][$k]);
            continue;
        }
        $menusetting['button'][$k] = $value;
        if(!empty($_GET['newsub_button'][$k])) {
            foreach($_GET['newsub_button'][$k]['name'] as $sk => $name) {
                $sub_button = array(
                    'displayorder' => $_GET['newsub_button'][$k]['displayorder'][$sk],
                    'name' => $name,
                    'keyurl' => $_GET['newsub_button'][$k]['keyurl'][$sk],
                );
                $menusetting['button'][$k]['sub_button'][] = $sub_button;
            }
        }
        if(count($menusetting['button'][$k]['sub_button']) > 7) {
            cpmsg($langs['admincp_selfmenu_submax'], '', 'error');
        }
        usort($menusetting['button'][$k]['sub_button'], 'buttoncmp');
    }
    
    if(count($menusetting['button']) > 3) {
        cpmsg($langs['admincp_selfmenu_topmax'], '', 'error');
    }
    
    usort($menusetting['button'], 'buttoncmp');
    
    $settings = array('singcere_wechat_selfmenu' => serialize($menusetting));
    C::t('common_setting')->update_batch($settings);
    updatecache('setting');
    
    if(submitcheck('pubsubmit')) {
        if(!$menusetting['button']) {
            cpmsg($langs['admincp_selfmenu_error'], '', 'error');
        }
        $pubmenu = array('button' => array());
        foreach($menusetting['button'] as $button) {
            if(!$button['sub_button']) {
                if(!$button['name']) {
                    cpmsg($langs['admincp_selfmenu_emptytitle'], '', 'error');
                }
                if(!$button['keyurl']) {
                    cpmsg($langs['admincp_selfmenu_emptyvalue'], '', 'error');
                }
                $parse = parse_url($button['keyurl']);
                $item = array(
                    'type' => $parse['host'] ? 'view' : 'click',
                    'name' => convertname($button['name']),
                    $parse['host'] ? 'url' : 'key' => $button['keyurl']
                );
                $pubmenu['button'][] = $item;
            } else {
                if(!$button['name']) {
                    cpmsg($langs['admincp_selfmenu_emptytitle'], '', 'error');
                }
                $sub_buttons = array();
                foreach($button['sub_button'] as $sub_button) {
                    if(!$sub_button['name']) {
                        cpmsg($langs['admincp_selfmenu_emptytitle'], '', 'error');
                    }
                    if(!$sub_button['keyurl']) {
                        cpmsg($langs['admincp_selfmenu_emptyvalue'], '', 'error');
                    }
                    $parse = parse_url($sub_button['keyurl']);
                    $item = array(
                        'type' => $parse['host'] ? 'view' : 'click',
                        'name' => convertname($sub_button['name']),
                        $parse['host'] ? 'url' : 'key' => $sub_button['keyurl']
                    );
                    $sub_buttons[] = $item;
                }
                $item = array(
                    'name' => convertname($button['name']),
                    'sub_button' => $sub_buttons
                );
                $pubmenu['button'][] = $item;
            }
        }

        $wechat_client = new WeChatClient($wechatsetting['wechat_appId'], $wechatsetting['wechat_appsecret']);
    
        if($wechat_client->setMenu($pubmenu)) {
            cpmsg($langs['admincp_selfmenu_success'], "action=plugins&operation=config&do=$pluginid&identifier=$plugin[identifier]&pmod=$module[name]", 'succeed');
        } else {
            cpmsg($langs['admincp_selfmenu_failed'].'£¨'.$wechat_client->error().'£©', '', 'error');
        }
    } else {
        cpmsg('setting_update_succeed', "action=plugins&operation=config&do=$pluginid&identifier=$plugin[identifier]&pmod=$module[name]", 'succeed');
    }
} else {
    
    
    
    
    showformheader("plugins&operation=config&do=$plugin[pluginid]&identifier=$plugin[identifier]&pmod=$module[name]");
    showtableheader();
    echo '<tr class="header"><th class="td25"></th><th>'.$lang['display_order'].'</th><th style="width:350px">'.$langs['admincp_selfmenu_title'].'</th><th>'.$langs['admincp_selfmenu_value'].'</th></tr>';
    
    foreach($menusetting['button'] as $k => $button) {
        $disabled = !empty($button['sub_button']) ? 'disabled' : '';
        showtablerow('', array('', 'class="td23 td28"', '', 'class=""'), array(
            "<input class=\"checkbox\" type=\"checkbox\" name=\"button[$k][delete]\" value=\"yes\" $disabled>",
            "<input type=\"text\" class=\"txt\" size=\"3\" name=\"button[$k][displayorder]\" value=\"$button[displayorder]\">",
            "<div class=\"parentnode\"><input type=\"text\" class=\"txt\" size=\"30\" name=\"button[$k][name]\" value=\"".dhtmlspecialchars($button['name'])."\"></div>",
            "<input type=\"text\" class=\"\" size=\"80\" name=\"button[$k][keyurl]\" value=\"".dhtmlspecialchars($button['keyurl'])."\">",
        ));
        if(!empty($button['sub_button'])) {
            foreach($button['sub_button'] as $sk => $sub_button) {
                showtablerow('', array('', 'class="td23 td28"', '', 'class=""'), array(
                    "<input class=\"checkbox\" type=\"checkbox\" name=\"button[$k][sub_button][$sk][delete]\" value=\"yes\">",
                    "<input type=\"text\" class=\"txt\" size=\"3\" name=\"button[$k][sub_button][$sk][displayorder]\" value=\"$sub_button[displayorder]\">",
                    "<div class=\"node\"><input type=\"text\" class=\"txt\" size=\"30\" name=\"button[$k][sub_button][$sk][name]\" value=\"".dhtmlspecialchars($sub_button['name'])."\"></div>",
                    "<input type=\"text\" class=\"\" size=\"80\" name=\"button[$k][sub_button][$sk][keyurl]\" value=\"".dhtmlspecialchars($sub_button['keyurl'])."\">",
                ));
            }
        }
        echo '<tr><td></td><td></td><td colspan="2"><div class="lastnode"><a href="###" onclick="addrow(this, 1, '.$k.')" class="addtr">'.$langs['admincp_selfmenu_addrow_2'].'</a></div></td></tr>';
    }
    echo '<tr><td></td><td class="td23 td28"></td><td colspan="2"><div><a href="###" onclick="addrow(this, 0, 0)" class="addtr">'.$langs['admincp_selfmenu_addrow_1'].'</a></div></td></tr>';
    
    echo <<<EOT
<script type="text/JavaScript">
var rowtypedata = [
[[1,''], [1,'<input name="newbutton[displayorder][]" value="" size="3" type="text" class="txt">', 'td23 td28'], [1, '<input name="newbutton[name][]" value="" size="30" type="text" class="txt">'], [1, '<input name="newbutton[keyurl][]" value="" size="30" type="text" class="txt">', 'td29']],
[[1,''], [1,'<input name="newsub_button[{1}][displayorder][]" value="" size="3" type="text" class="txt">', 'td23 td28'], [1, '<div class=\"node\"><input name="newsub_button[{1}][name][]" value="" size="30" type="text" class="txt"></div>'], [1, '<input name="newsub_button[{1}][keyurl][]" value="" size="30" type="text" class="txt">', 'td29']],
];
</script>
EOT;
    
    showsubmit('menusubmit', $langs['admincp_selfmenu_save'], 'del', '<input type="submit" class="btn" name="pubsubmit" value="'.$langs['admincp_selfmenu_pub'].'" />&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" class="btn" name="loadsubmit" value="'.$langs['admincp_selfmenu_load'].'" />');
    showtablefooter();
    showformfooter();
    
}


function sc_diconv($str, $in_charset, $out_charset = CHARSET) {

    if (strtolower($in_charset) != strtolower($out_charset)) {
        return eval('return ' . diconv(var_export($str, true) . ';', $in_charset, $out_charset));
    }
    return $str;
}

function convertname($str) {
	return urlencode(diconv($str, CHARSET, 'UTF-8'));
}

function buttoncmp($a, $b) {
	return $a['displayorder'] > $b['displayorder'] ? 1 : -1;
}

?>