<?php

/**
 *      (C)2007-2012 singcere.net
 *      This is a freeware, But you have no right to modify or distribute
 *      $Id$
 */

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

if ($_GET['ac'] == 'qrcode') {
    require_once DISCUZ_ROOT . 'source/plugin/mobile/qrcode.class.php';
    $size = intval($_GET['size']) ? intval($_GET['size']) : 4;

    if (class_exists('QRcode')) {
        QRcode::png($_GET['url'], false, 0, $size, 2);
        exit;
    }

    dheader('location: ' . $_G['siteurl'] . 'static/image/common/none.gif');
} else if ($_GET['ac'] == 'img2wx') {        // 微信外链图处理
    $url = $_GET['url'];
    if (strpos($url, 'http://mmbiz.qpic.cn/') !== 0) {
        dheader('location: ' . $url);
    }
    header("Content-type: image/jpeg");
    $content = dfsockopen($url);
    echo $content;
} else if ($_GET['ac'] == 'seccode') {
    $seccode = authcode(base64_decode($_GET['codehash']), 'DECODE', $_G['config']['security']['authkey']);
    $seccode = $seccode ? $seccode : make_seccode();
    if (!$_G['setting']['nocacheheaders']) {
        @header("Expires: -1");
        @header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", false);
        @header("Pragma: no-cache");
    }

    require_once DISCUZ_ROOT . '/source/plugin/singcere_wechat/class/seccode.class.php';
    $code = new SC_seccode();
    $code->code = $seccode;
    $code->type = 0;
    $code->width = 180;
    $code->height = 100;
    $code->background = $_G['setting']['seccodedata']['background'];
    $code->adulterate = $_G['setting']['seccodedata']['adulterate'];
    $code->ttf = $_G['setting']['seccodedata']['ttf'];
    $code->angle = $_G['setting']['seccodedata']['angle'];
    $code->warping = $_G['setting']['seccodedata']['warping'];
    $code->scatter = 1;
    $code->color = $_G['setting']['seccodedata']['color'];
    $code->size = $_G['setting']['seccodedata']['size'];
    $code->shadow = $_G['setting']['seccodedata']['shadow'];
    $code->animator = $_G['setting']['seccodedata']['animator'];
    $code->fontpath = DISCUZ_ROOT . './static/image/seccode/font/';
    $code->datapath = DISCUZ_ROOT . './static/image/seccode/';
    $code->includepath = DISCUZ_ROOT . './source/class/';
    $code->display();
} else if ($_GET['ac'] == 'phpinfo') {
    list($uid, $timestamp) = explode("\t", authcode(base64_decode($_GET['auth']), 'DECODE'));
    if ($uid == $_G['uid'] && $timestamp + 3600 > TIMESTAMP) {
        phpinfo();
        exit;
    }
    exit('Access Denied');
} else if ($_GET['ac'] == 'curlbaidu') {
    @header('Content-Type: text/html; charset=utf-8');
    $url = 'https://www.baidu.com';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    if (!curl_exec($ch)) {
        echo(curl_error($ch));
        $data = '';
    } else {
        $data = curl_multi_getcontent($ch);
    }
    curl_close($ch);
    echo $data;
    exit;
}