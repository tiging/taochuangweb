<?php

/**
 *      (C)2007-2012 singcere.net
 *      This is a freeware, But you have no right to modify or distribute
 *      $Id$
 */

if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}

$langs = &$scriptlang['singcere_wechat'];
$setting = (array)unserialize($_G['setting']['singcere_wechat']);

if(!submitcheck('discuzsubmit')) {
    
    $groupselect = array();
    foreach(C::t('common_usergroup')->range_orderby_credit() as $group) {
        if($group['type'] != 'member' || $_G['setting']['newusergroupid'] == $group['groupid']) {
            $groupselect[$group['type']] .= '<option value="'.$group['groupid'].'"'.($setting['discuz_newusergroupid'] == $group['groupid'] ? ' selected' : '').'>'.$group['grouptitle'].'</option>';
        }
    }
    $usergroups = '<select name="setting[discuz_newusergroupid]"><option value="">'.cplang('plugins_empty').'</option>'.
        '<optgroup label="'.$lang['usergroups_member'].'">'.$groupselect['member'].'</optgroup>'.
        ($groupselect['special'] ? '<optgroup label="'.$lang['usergroups_special'].'">'.$groupselect['special'].'</optgroup>' : '').
        ($groupselect['specialadmin'] ? '<optgroup label="'.$lang['usergroups_specialadmin'].'">'.$groupselect['specialadmin'].'</optgroup>' : '').
        '<optgroup label="'.$lang['usergroups_system'].'">'.$groupselect['system'].'</optgroup></select>';
    
    $apicredits = '<option value="0">'.cplang('none').'</option>';
    foreach($_G['setting']['extcredits'] as $i => $credit) {
        $extcredit = 'extcredits'.$i.' ('.$credit['title'].')';
        $apicredits .= '<option value="'.$i.'" '.($i == intval($setting['discuz_credit']) ? 'selected' : '').'>'.$extcredit.'</option>';
    }
    
    showformheader('plugins&operation=config&do='.$pluginid.'&identifier='.$plugin[identifier].'&pmod='.$module['name']);
    
    showtableheader('1. '.$langs['admincp_discuz_tit_login']);
    showsetting($langs['admincp_discuz_allowregister'], 'setting[discuz_allowregister]', $setting['discuz_allowregister'], 'radio', 0, 1, $langs['admincp_discuz_allowregister_tips']);
    showsetting($langs['admincp_disucz_disableregrule'], 'setting[discuz_disableregrule]', $setting['discuz_disableregrule'], 'radio', 0, 0, $langs['admincp_disucz_disableregrule_tips']);
    showsetting($langs['admincp_discuz_allownewname'], 'setting[discuz_allownewusername]', $setting['discuz_allownewusername'], 'radio', 0, 0, $langs['discuz_allownewusername_tips']);
    showsetting($langs['admincp_discuz_defaultgroupid'], '', '', $usergroups, 0, 0, $langs['admincp_discuz_defaultgroupid_tips']);
    
    
    
    showsetting($langs['admincp_discuz_credit'], '', '', '<select name="setting[discuz_credit]">'.$apicredits.'</select>', 0, 0, $langs['admincp_discuz_credit_tips']);
    showsetting($langs['admincp_discuz_regreward'], 'setting[discuz_regreward]', $setting['discuz_regreward'], 'text', 0, 0, $langs['admincp_discuz_regreward_tips']);
    showtagfooter('tbody');
    
    showtableheader('2. '.$langs['admincp_discuz_tit_loginbar']);
    showsetting($langs['admincp_discuz_tit_showloginbar'], 'setting[discuz_loginbar]', $setting['discuz_loginbar'], 'radio', 0, 0, $langs['admincp_discuz_tit_showloginbar_tips']);
    showtablefooter();
    
    showtableheader('3. '.$langs['admincp_discuz_tit_wxjs']);
    showsetting($langs['admincp_discuz_loadwxjs'], 'setting[discuz_loadwxjs]', $setting['discuz_loadwxjs'], 'radio', 0, 0, $langs['admincp_discuz_loadwxjs_tips']);
    showtablefooter();
        
    
    
    showtableheader();
    showsubmit('discuzsubmit');
    showtablefooter();
    
    showformfooter();
} else {
    
    $settings = array('singcere_wechat' => serialize($_GET['setting'] + $setting));
    C::t('common_setting')->update_batch($settings);
    updatecache('setting');
    
    cpmsg('setting_update_succeed', 'action=plugins&operation=config&do='.$pluginid.'&identifier='.$plugin[identifier].'&pmod='.$module['name'], 'succeed');
}





?>
