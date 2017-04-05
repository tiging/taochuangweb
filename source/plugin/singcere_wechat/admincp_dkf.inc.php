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

$langs = &$scriptlang['singcere_wechat'];
$setting = (array)unserialize($_G['setting']['singcere_wechat']);

if(!submitcheck('submitcheck')) {
    
   
    showtips(lang('plugin/singcere_wechat', 'admincp_dkf_tips', array('siteurl' => $_G['siteurl'])));
    
    showformheader('plugins&operation=config&do='.$pluginid.'&identifier='.$plugin[identifier].'&pmod='.$module['name']);
    showtableheader($langs['admincp_dkf_header']);
    
    $priority = '<select name="setting[dkf_priority]"><option value="0" '.(intval($setting['dkf_priority']) == 0 ? 'selected' : '').'>'.$langs['admincp_dkf_priority_0'].'</option>'.
        '<option value="1" '.(intval($setting['dkf_priority']) == 1 ? 'selected' : '').'>'.$langs['admincp_dkf_priority_1'].'</option>'.     
        '</select>';
    
    showsetting($langs['admincp_dkf_allowservice'], 'setting[dkf_allowservice]', $setting['dkf_allowservice'], 'radio', 0, 0, $langs['admincp_dkf_allowservice_tips']);
    showsetting($langs['admincp_dkf_keyword'], 'setting[dkf_keyword]', $setting['dkf_keyword'], 'text', 0, 0, $langs['admincp_dkf_keyword_tips']);
    showsetting($langs['admincp_dkf_priority'], '', '', $priority, 0, 0, '');
    showsetting($langs['admincp_dkf_greet'], 'setting[dkf_greet]', $setting['dkf_greet'], 'text', 0, 0, $langs['admincp_dkf_greet_tips']);
    showsetting($langs['admincp_dkf_offline'], 'setting[dkf_offline]', $setting['dkf_offline'], 'text', 0, 0, $langs['admincp_dkf_offline_tips']);
    
    showsetting($langs['admincp_dkf_worktimelimit'], 'setting[dkf_worktimelimit]', $setting['dkf_worktimelimit'], 'radio', 0, 1, $langs['admincp_dkf_worktimelimit_tips']);
    
    
    $timearr = array('setting[dkf_worktime][]');
    for($i = 0; $i <= 23; $i++) {
        $timearr[1][] = array($i*3600, "$i:00 ~$i:30");
        $timearr[1][] = array($i*3600+1800, "$i:30 ~".($i+1).":00");
    }
    
    showsetting($langs['admincp_dkf_worktime'], $timearr, $setting['dkf_worktime'], 'mselect', 0, 0, '');
    showsetting($langs['admincp_dkf_notworktime'], 'setting[dkf_notworktime]', $setting['dkf_notworktime'], 'text', 0, 0, $langs['admincp_dkf_notworktime_tips']);
    showtagfooter('tbody');
   
    showtablefooter();
    
    
    showtableheader();
    showsubmit('submitcheck');
    showtablefooter();
    
    
    showformfooter();
    
    
} else {
    
    $settings = array('singcere_wechat' => serialize($_GET['setting'] + $setting));
    C::t('common_setting')->update_batch($settings);
    updatecache('setting');
    
    cpmsg('setting_update_succeed', 'action=plugins&operation=config&do='.$pluginid.'&identifier='.$plugin[identifier].'&pmod='.$module['name'], 'succeed');
}
