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

// if($_GET['unbind'] && $_GET['unbind'] == FORMHASH) {
//     require_once libfile('function/member');
//     C::t('#mobile#common_member_wechat')->delete($_G['uid']);
//     clearcookies();
//     showmessage('wechat:wechat_message_unbinded', dreferer());
// }





if($_G['uid'] && submitcheck('confirmsubmit')) {
    loaducenter();
    list($result) = uc_user_login($_G['uid'], $_GET['passwordconfirm'], 1, 0);
    if($result >= 0) {
        dsetcookie('qrauth', base64_encode(authcode($result, 'ENCODE', $_G['config']['security']['authkey'], 300)));
        showmessage('', dreferer());
    }
    showmessage('login_password_invalid');
}

if(isset($_GET['check'])) {
    $code = authcode(base64_decode($_GET['check']), 'DECODE', $_G['config']['security']['authkey']);
    if($code) {
        $authcode = C::t('#singcere_wechat#singcere_wechat_authcode')->fetch_by_code($code);
        if($authcode['status']) {
            require_once libfile('function/member');
            $member = getuserbyuid($authcode['uid'], 1);
            setloginstatus($member, 1296000);
            $echostr = 'done';
        } else {
            $echostr = '1';//json_encode($authcode);
        }
    } else {
        $echostr = '-1';
    }

    if(!ob_start($_G['gzipcompress'] ? 'ob_gzhandler' : null)) {
        ob_start();
    }

    if($echostr === 'done'){
        C::t('#singcere_wechat#singcere_wechat_authcode')->delete($authcode['sid']);
    }

    include template('common/header_ajax');
    echo $echostr;
    include template('common/footer_ajax');
    exit;
}

if($_G['cookie']['qrauth']) {
	$qrauth = authcode(base64_decode($_G['cookie']['qrauth']), 'DECODE', $_G['config']['security']['authkey']);
	
}

list($codeenc, $code) = SC_WeChat::getqrcode();
include_once DISCUZ_ROOT . './source/plugin/singcere_wechat/class/wechat.lib.class.php';

$wechat_client = new WeChatClient($_G['singcere_wechat']['setting']['wechat_appId'], $_G['singcere_wechat']['setting']['wechat_appsecret']);
$ticket = $wechat_client->getQrcodeTicket(array('scene_id' => $code, 'expire' => 600, 'ticketOnly' => 1));

$qrcodeurl = $ticket ? 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.urlencode($ticket) : $_G['setting']['attachurl'].$_G['singcere_wechat']['setting']['wechat_qrcode'];


include_once template('singcere_wechat:qrcode');
