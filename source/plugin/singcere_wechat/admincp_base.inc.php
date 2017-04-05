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

$setting['wechat_appsecret'] = authcode($setting['wechat_appsecret'], 'DECODE', $_G['config']['security']['authkey']);
$appsecret_mask = $setting['wechat_appsecret'] ? substr($setting['wechat_appsecret'], 0, 2).'****************************'.substr($setting['wechat_appsecret'], -2) : '';
$setting['wechat_mchkey'] = authcode($setting['wechat_mchkey'], 'DECODE', $_G['config']['security']['authkey']);
$mchkey_mask = $setting['wechat_mchkey'] ? substr($setting['wechat_mchkey'], 0, 2).'****************************'.substr($setting['wechat_mchkey'], -2) : '';

$apiurl = $_G['siteurl'].'source/plugin/singcere_wechat/api.php';


if(!submitcheck('basesubmit')) {
	showformheader('plugins&operation=config&do='.$pluginid.'&identifier='.$plugin[identifier].'&pmod=admincp_base', 'enctype');
	
	showtableheader($langs['admincp_base_title_1']);
	showsetting($langs['admincp_base_apiurl'], '', '', '<span style="white-space:nowrap">'.$apiurl.'</span>');
	showsetting($langs['admincp_base_token'], 'setting[wechat_token]', $setting['wechat_token'], 'text', '', 0, $langs['admincp_base_token_tips']);
	showsetting($langs['admincp_base_encodingkey'], 'setting[wechat_aeskey]', $setting['wechat_aeskey'], 'text', '', 0, $langs['admincp_base_encodingkey_tips']);
	showtablefooter();
	
	
	showtableheader($langs['admincp_base_title_2']);
	showsetting(lang('plugin/wechat', 'wechat_appId'), 'setting[wechat_appId]', $setting['wechat_appId'], 'text', '', 0, $langs['admincp_base_appid']);
	showsetting(lang('plugin/wechat', 'wechat_appsecret'), 'setting[wechat_appsecret]', $appsecret_mask, 'text', '', 0, $langs['base_secret']);
	showtablefooter();
	
	showtableheader($langs['admincp_base_title_3']);
	showsetting('MCHID', 'setting[wechat_mchid]', $setting['wechat_mchid'], 'text', '', 0, $langs['admincp_base_mchid']);
	showsetting('KEY', 'setting[wechat_mchkey]', $mchkey_mask, 'text', '', 0, $langs['admincp_base_mchkey']);
	
	
	
	showtableheader($langs['admincp_base_title_4']);
	showsetting($langs['admincp_base_wechatname'], 'setting[wechat_name]', $setting['wechat_name'], 'text');
	showsetting($langs['admincp_base_qrcode'], 'wechat_qrcode', '', 'file', '', 0, imglable($setting['wechat_qrcode']));
	showsetting($langs['admincp_base_shareicon'], 'wechat_shareicon', '', 'file', '', 0, imglable($setting['wechat_shareicon']));
	showsetting($langs['admincp_base_subscribe_url'], 'setting[wechat_subscribe_url]', $setting['wechat_subscribe_url'], 'text');
	showtablefooter();
	
	showtableheader();
	showsubmit('basesubmit');
	showtablefooter();
	
	showformfooter();
} else {
    if($_FILES['wechat_qrcode']['tmp_name']) {
        $upload = new discuz_upload();
        if(!$upload->init($_FILES['wechat_qrcode'], 'common', random(3, 1), random(8)) || !$upload->save()) {
            cpmsg($upload->errormessage(), '', 'error');
        }
        $_GET['setting']['wechat_qrcode'] = 'common/'.$upload->attach['attachment'];
    }
    
    if($_FILES['wechat_shareicon']['tmp_name']) {
        $upload = new discuz_upload();
        if(!$upload->init($_FILES['wechat_shareicon'], 'common', random(3, 1), random(8)) || !$upload->save()) {
            cpmsg($upload->errormessage(), '', 'error');
        }
        $_GET['setting']['wechat_shareicon'] = 'common/'.$upload->attach['attachment'];
    }
    
    
	if($_GET['setting']['wechat_appId'] && $_GET['setting']['wechat_appsecret']) {
		if($_GET['setting']['wechat_appsecret'] != trim($_GET['setting']['wechat_appsecret'])) {
			cpmsg(lang('plugin/singcere_wechat', 'admincp_base_conn_geterror'), '', 'error');
		}
	    $_GET['setting']['wechat_appsecret'] = $_GET['setting']['wechat_appsecret'] == $appsecret_mask ? $setting['wechat_appsecret'] : $_GET['setting']['wechat_appsecret'];
	    	
		require_once DISCUZ_ROOT . './source/plugin/singcere_wechat/class/wechat.lib.class.php';
		$wechat_client = new WeChatClient($_GET['setting']['wechat_appId'], $_GET['setting']['wechat_appsecret']);
		if(!$wechat_client->getAccessToken(1, 1)) {
			cpmsg(lang('plugin/singcere_wechat', 'admincp_base_conn_geterror'), '', 'error');
		}
		
		$_GET['setting']['wechat_appsecret'] = authcode($_GET['setting']['wechat_appsecret'], 'ENCODE', $_G['config']['security']['authkey']);

		$option = array(
			'scene_id' => 100000,
			'expire' => 30,
			'ticketOnly' => 1
		);
		$ticket = $wechat_client->getQrcodeTicket($option);
		if(!$wechat_client->getQrcodeImgUrlByTicket($ticket)) {
			cpmsg(lang('plugin/wechat', 'wechat_at_qrgeterror'), '', 'error');
		}
	}
	
	if($_GET['setting']['wechat_mchid'] && $_GET['setting']['wechat_mchkey']) {
	    $_GET['setting']['wechat_mchkey'] = $_GET['setting']['wechat_mchkey'] == $mchkey_mask ? $setting['wechat_mchkey'] : $_GET['setting']['wechat_mchkey'];
	    $_GET['setting']['wechat_mchkey'] = authcode($_GET['setting']['wechat_mchkey'], 'ENCODE', $_G['config']['security']['authkey']);
	}

	$_GET['setting']['wechat_token'] = $_GET['setting']['wechat_token'] ? $_GET['setting']['wechat_token'] : random(16);
	$_GET['setting']['wechat_aeskey'] = $_GET['setting']['wechat_appsecret'] ? $_GET['setting']['wechat_aeskey'] : random(43);
	
	$settings = array('singcere_wechat' => serialize($_GET['setting'] + $setting));
	C::t('common_setting')->update_batch($settings);
	updatecache('setting');
	
	cpmsg('setting_update_succeed', 'action=plugins&operation=config&do='.$pluginid.'&identifier='.$plugin['identifier'].'&pmod=admincp_base', 'succeed');
}


function imglable($src, $comment = '') {
    global $_G;
    return $src ? "<img width='128' height='128' src='{$_G[setting][attachurl]}$src' /><label>".$comment.'</label>' : ''; 
}