<?php
/**
 *      (C)2007-2012 singcere.net
 *      This is a freeware, But you have no right to modify or distribute
 *
 *      $Id$
 */


/**
 * 
 * 微信授权登录, 跳转回当前页面
 * @param getinfo 仅获取微信账户资料
 *  
 */
function sc_wechat_auth($baseapi = true, $getinfo = false) {
    global $_G;
   
    if(!IN_WECHAT) {
        return ;
    }
    
    list($openid, $unionid, $uid, $time) = getcookie('wxoauth') ? explode("\t", authcode(getcookie('wxoauth'), 'DECODE')) : array();
    if($openid && $_G['uid'] == $uid) {
        return $openid;
    } else {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        if (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
            $requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
        } elseif (isset($_SERVER['REQUEST_URI'])) {
            $requestUri = $_SERVER['REQUEST_URI'];
        } elseif (isset($_SERVER['REDIRECT_URL'])) {
            $requestUri = $_SERVER['REDIRECT_URL'];
        }  elseif (isset($_SERVER['ORIG_PATH_INFO'])) {
            $requestUri = $_SERVER['ORIG_PATH_INFO'];
            if (!empty($_SERVER['QUERY_STRING'])) {
                $requestUri .= '?' . $_SERVER['QUERY_STRING'];
            }
        }
        $url = $protocol.$_SERVER['HTTP_HOST'].$requestUri;
        
        include_once DISCUZ_ROOT . './source/plugin/singcere_wechat/class/wechat.lib.class.php';
        $wechat_client = new WeChatClient($_G['singcere_wechat']['setting']['wechat_appId'], $_G['singcere_wechat']['setting']['wechat_appsecret']);
        
        $redirect = $_G['siteurl'].'plugin.php?id=singcere_wechat&op=OAuth&getinfo='.$_GET['getinfo'].'&referer='.urlencode($url);
        dheader('Location:'.$wechat_client->getOAuthConnectUri($redirect, md5(FORMHASH), 'snsapi_base'));
    }
}

/*
 * 依赖bind表所记录的注册会员与微信绑定信息
 *
 * */
function sc_load_subscribe($sync = false) {
    global $_G;

    $subscribe = 0;

    if(!$_G['uid']) {return 0;}
    $bindmember = C::t('#singcere_wechat#singcere_wechat_bind')->fetch_by_uid($_G['uid']);
    if($sync && $bindmember) {
        include_once DISCUZ_ROOT . './source/plugin/singcere_wechat/class/wechat.lib.class.php';
        $wechat_client = new WeChatClient($_G['singcere_wechat']['setting']['wechat_appId'], $_G['singcere_wechat']['setting']['wechat_appsecret']);
        $info = $wechat_client->getUserInfoById($bindmember['openid']);
        if($info['subscribe'] != $bindmember['subscribe']) {
            C::t('#singcere_wechat#singcere_wechat_bind')->update($bindmember['id'], array('subscribe' => $info['subscribe']));
        }

        $subscribe = $info['subscribe'];
    } else if($bindmember) {
        $subscribe = $bindmember['subscribe'];
    }

    return $subscribe;
}


function sc_wechat_notification($type, $data) {
    global $_G;
    
    $template = $_G['singcere_wechat']['setting']['tmpl_template'][$type];
    if(!$template['enable'] || !$template['id']) {
        return false;
    }
    
    list($stype, $value) = explode(':', $data['to']);
    $openid = '';
    if($stype == 'openid') {
        $openid = $value;
    } else {
        $bindmember = C::t('#singcere_wechat#singcere_wechat_bind')->fetch_by_uid($value);
        $openid = $bindmember['openid'];
        $data['to'] = 'openid:'.$openid;
    }
    
    if($template['allowdisable']) {
        $bindmember = $bindmember ? $bindmember : C::t('#singcere_wechat#singcere_wechat_bind')->fetch_by_openid($openid);
        $bindmember['setting'] = unserialize($bindmember['setting']);
        if(in_array($type, (array)$bindmember['setting']['template']['disable'])) {
            return false;
        }
    }

    $template['sendflood'] = intval($template['sendflood']);
    
    
    if($template['sendflood'] > 0) {
        $sended = C::t('#singcere_wechat#singcere_wechat_tmplmsg')->fetch_first_by_openid($openid);

        if($sended['dateline'] + $template['sendflood'] > TIMESTAMP) {
            return false;
        }
    }
    
    sc_message_template($template['id'], $data['to'], $data['url'], $data['color'], $data['data']);
    return true;
}


/*
 * 发送模板消息
 * 发起新请求, 防止堵塞
 * 
 * */
function sc_message_template($templateid, $to, $url, $color, $data) {
    global $_G;

    list($type, $value) = explode(':', $to);
    $openid = '';
    if($type == 'openid') {
        $openid = $value;
    } else {
        $bindmember = C::t('#singcere_wechat#singcere_wechat_bind')->fetch_by_uid($value);
        $openid = $bindmember['openid'];
    }
    
    if(!$openid) {
        return false;
    }

    $post = array(
        'templateid' => $templateid,
        'openid' => $openid,
        'url' => $url,
        'topcolor' => $color,
        'data' => $data 
    ); 
    
//     if(defined('IN_MOBILE')) {
//         $post = sc_diconv($post, CHARSET, 'UTF-8');
//     }
    $authcode = base64_encode(authcode($_G['singcere_wechat']['setting']['wechat_token']."\t".TIMESTAMP, 'ENCODE', $_G['config']['security']['authkey'], '60')); 

    include_once libfile('function/filesock', 'plugin/singcere_wechat');
    sc_dfsockopen($_G['siteurl'].'plugin.php?id=singcere_wechat:message&do=template&auth='.$authcode.'&timestamp='.TIMESTAMP, 0, $post, '', false, '', 0, false, 'URLENCODE', true);
}

function sc_diconv($str, $in_charset, $out_charset = CHARSET) {

    if (strtolower($in_charset) != strtolower($out_charset)) {
        return eval('return ' . diconv(var_export($str, true) . ';', $in_charset, $out_charset));
    }
    return $str;
}

function sc_createNonceStr($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
        $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
}


