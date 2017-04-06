<?php

/**
 *      (C)2007-2012 singcere.net
 *      This is a freeware, But you have no right to modify or distribute
 *
 *      $Id$
 */

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

list($authcode, $timestamp) = explode("\t", authcode(base64_decode($_GET['auth']), 'DECODE', $_G['config']['security']['authkey']));

if(!$authcode || $timestamp != $_GET['timestamp']) {
    showmessage('quickclear_noperm');
}


if($_GET['do'] == 'template') {
    include_once DISCUZ_ROOT . './source/plugin/singcere_wechat/class/wechat.lib.class.php';

    $wechat_client = new WeChatClient($_G['singcere_wechat']['setting']['wechat_appId'], $_G['singcere_wechat']['setting']['wechat_appsecret']);
    $result = $wechat_client->sendTemplateMsg($_POST['templateid'], $_POST['openid'], $_POST['url'], $_POST['topcolor'], $_POST['data']);
    
    if($result['msgid']) {
        C::t('#singcere_wechat#singcere_wechat_tmplmsg')->insert(array(
            'msgid' => intval($result['msgid']),
            'openid' => $_POST['openid'],
            'template' => $_POST['templateid'],
            'url' => $_POST['url'],
            'topcolor' => $_POST['topcolor'],
            'data' => serialize($_POST['data']),
            'errcode' => $result['errcode'],
            'dateline' => TIMESTAMP
        ));
     
        $result['errmsg'] = WeChatClient::$ERRCODE_MAP[$result['errcode']];
    }
    
    echo json_encode($result);
}