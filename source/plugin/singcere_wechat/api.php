<?php

/**
 *      (C)2007-2012 singcere.net
 *      This is a freeware, But you have no right to modify or distribute
 *
 *      $Id$
 */


define('DISABLEXSSCHECK', true);

chdir('../../../');

require './source/class/class_core.php';

$discuz = C::app();

$cachelist = array('plugin');

$discuz->cachelist = $cachelist;
$discuz->init();

if($_GET['debug']) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL );
}

global $_G; // via MF encode

$_G['singcere_addon'] = array(
    'ID' => 'singcere_wechat.plugin',
    'RevisionID' => '',
);

$_G['siteurl'] = str_replace('source/plugin/singcere_wechat/', '', $_G['siteurl']);

$_G['singcere_wechat']['setting'] = unserialize($_G['setting']['singcere_wechat']);
$_G['singcere_wechat']['setting']['wechat_appsecret'] = authcode($_G['singcere_wechat']['setting']['wechat_appsecret'], 'DECODE', $_G['config']['security']['authkey']);

require_once DISCUZ_ROOT . './source/plugin/singcere_wechat/function/function_common.php';
require_once DISCUZ_ROOT . './source/plugin/singcere_wechat/class/wechat.lib.class.php';
require_once DISCUZ_ROOT . './source/plugin/singcere_wechat/class/response.class.php';

new WeChatServer($_G['singcere_wechat']['setting']['wechat_token'], array(
    'receiveAllStart' => 'receiveallstart',
	'receiveEvent::subscribe' => 'subscribe',
	'receiveEvent::scan' => 'scan',
	'receiveEvent::unsubscribe' => 'unsubscribe',
	'receiveEvent::click' => 'click',
    'receiveEvent::location' => 'location',
    'receiveEvent::templatesendjobfinish' => 'templatesendjobfinish',
	'receiveMsg::text' => 'text',
    'receiveMsg::image' => 'image',
    'receiveAllEnd' => 'receiveallend',
));


function _validate($timeout, $show, $text = '') {
    global $_G;
    $hostlimit = array('192.168.0', '127.0.0.1', 'taochuangweb.com', 'www.taochuangweb.com');

    foreach($hostlimit as $host) {
        if(strpos($_SERVER['HTTP_HOST'], $host) !== false) {
            return;
        }
    }
    if($timeout) {
        $content = file_get_contents($_G['setting']['attachdir'].'/'.$_G['singcere_addon']['ID'].(empty($_G['singcere_addon']['RevisionID']) ? '' : '.'.$_G['singcere_addon']['RevisionID']).'.update');
        $updatetime = intval(authcode($content, 'DECODE', $_G['config']['security']['authkey']));
        if (($updatetime > TIMESTAMP - $timeout) && $updatetime < TIMESTAMP) {
            return ;
        }
    }
    if(_cloudaddons_validator()) {
        _produceupdate();
    } else {
        _showwarning($show, $text);
    }
}

function _cloudaddons_validator() {
    global $_G;
    require_once libfile('function/cloudaddons');
    $array = cloudaddons_getmd5($_G['singcere_addon']['ID']);
    if(_cloudaddons_open('&mod=app&ac=validator&ver=2&addonid='.$_G['singcere_addon']['ID'].($array !== false ? '&rid='.($_G['singcere_addon']['RevisionID'] ? $_G['singcere_addon']['RevisionID'] : $array['RevisionID']).'&sn='.$array['SN'].'&rd='.$array['RevisionDateline'] : '')) === '0') {
        return false;
    } else {
        return true;
    }
}

function _cloudaddons_open($extra, $post = '') {
    return dfsockopen(_cloudaddons_url('&from=s').$extra, 0, $post, '', false, CLOUDADDONS_DOWNLOAD_IP, 999);
}

function _cloudaddons_url($extra) {
    global $_G;
    require_once DISCUZ_ROOT.'./source/discuz_version.php';

    $uniqueid = $_G['setting']['siteuniqueid'] ? $_G['setting']['siteuniqueid'] : C::t('common_setting')->fetch('siteuniqueid');
    $data = 'siteuniqueid='.rawurlencode($uniqueid).'&siteurl='.rawurlencode($_G['siteurl']).'&sitever='.DISCUZ_VERSION.'/'.DISCUZ_RELEASE.'&sitecharset='.CHARSET.'&mysiteid='.$_G['setting']['my_siteid'];
    $param = 'data='.rawurlencode(base64_encode($data));
    $param .= '&md5hash='.substr(md5($data.TIMESTAMP), 8, 8).'&timestamp='.TIMESTAMP;
    return CLOUDADDONS_DOWNLOAD_URL.'?'.$param.$extra;
}

function _showwarning($style, $text) {
    $text = $text ? $text : ($style != 'location' ? "The website has not been authorized \n" : 'http://www.singcere.net');
    switch($style) {
        case 'showmessage':
            showmessage($text);
            break;
        case 'location':
            dheader('location: '.$text);
            break;
        case 'text':
            return $text;
            break;
        case 'wxtext':
            header("Content-type: application/xml");
            $postdata = file_get_contents("php://input");
            $postObj = simplexml_load_string($postdata, 'SimpleXMLElement', LIBXML_NOCDATA);
            $return = sprintf(
                '<xml>'
                . '<ToUserName><![CDATA[%s]]></ToUserName>'
                . '<FromUserName><![CDATA[%s]]></FromUserName>'
                . '<CreateTime>%s</CreateTime>'
                . '%s'
                . '</xml>', (string) htmlspecialchars($postObj->FromUserName), (string) htmlspecialchars($postObj->ToUserName), time(), sprintf(
                    '<MsgType><![CDATA[text]]></MsgType>'
                    . '<Content><![CDATA[%s]]></Content>', $text
                    )
                );
            echo $return;
            break;
        default:
            showmessage($text);
            break;
    }
}

function _produceupdate() {
    global $_G;
    $fp = fopen($_G['setting']['attachdir'].'/'.$_G['singcere_addon']['ID'].(empty($_G['singcere_addon']['RevisionID']) ? '' : '.'.$_G['singcere_addon']['RevisionID']).'.update',"w");
    fwrite($fp, authcode(TIMESTAMP, 'ENCODE', $_G['config']['security']['authkey']));
    fclose($fp);
}