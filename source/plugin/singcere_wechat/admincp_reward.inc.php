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


$op = in_array($_GET['op'], array('setting', 'filter', 'list')) ? $_GET['op'] : 'setting';


if (submitcheck('settingsubmit')) {

    $_GET['setting']['reward_authorscale'] = intval($_GET['setting']['reward_authorscale']);
    if ($_GET['setting']['reward_payauthor']) {
        if ($_GET['setting']['reward_authorscale'] > 100 || $_GET['setting']['reward_authorscale'] < 0) {
            cpmsg($langs['admincp_reward_scale_error'], '', 'error');
        }
    }
    $default = array();
    foreach(explode(' ', $_GET['setting']['reward_candidate']) as $item) {
        $item = floatval($item);
        if($item > 0) {
            $default[] = $item;
        }
    }

    $default = array_slice($default, 0, 6);
    $_GET['setting']['reward_candidate'] = implode(' ', $default);
    if(empty($_GET['setting']['reward_candidate'])) {
        cpmsg($langs['admincp_reward_candidate_error'], '', 'error');
    }

    if(!$_GET['setting']['reward_color']) {
        $_GET['setting']['reward_color'] = '#f57e42';
    }

    $settings = array('singcere_wechat' => serialize($_GET['setting'] + $setting));
    C::t('common_setting')->update_batch($settings);
    updatecache('setting');

    cpmsg('setting_update_succeed', 'action=plugins&operation=config&do='.$pluginid.'&identifier='.$plugin[identifier].'&pmod='.$module['name'].'&op='.$op, 'succeed');


} else if (submitcheck('filtersubmit')) {

}


if ($op == 'setting') {
    showtips($langs['admincp_reward_tips']);


    showformheader("plugins&operation=config&do=$plugin[pluginid]&identifier=$plugin[identifier]&pmod=$module[name]&op=setting");
    showtableheader($langs['admincp_reward_setting']);
    showsetting($langs['admincp_reward_enabled'], 'setting[reward_enable]', $setting['reward_enable'], 'radio', 0, 1);
    showtablefooter();

    showtableheader('');
    showsetting($langs['admincp_reward_payauthor'], 'setting[reward_payauthor]', $setting['reward_payauthor'], 'radio', 0, 1, $langs['admincp_reward_payauthor_tips']);

    showsetting($langs['admincp_reward_authorscale'], 'setting[reward_authorscale]', $setting['reward_authorscale'], 'text', 0, 0, $langs['admincp_reward_authorscale_tips']);
    showtagfooter('tbody');


    showsetting($langs['admincp_reward_text'], 'setting[reward_tips]', $setting['reward_tips'], 'textarea', 0, 0, $langs['admincp_reward_text_tips']);



    showsetting($langs['admincp_reward_candidate'], 'setting[reward_candidate]', $setting['reward_candidate'], 'text', 0, 0, $langs['admincp_reward_candidate_tips']);



    showsetting($langs['admincp_reward_color'], 'setting[reward_color]', $setting['reward_color'], 'color', 0, 0, $langs['admincp_reward_color_tips']);


    showsubmit('settingsubmit');
    showformfooter();

} else if ($op == 'filter') {

} else {



}