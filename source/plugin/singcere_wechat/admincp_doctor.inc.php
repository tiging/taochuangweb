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

require libfile('function/admincp', 'plugin/singcere_wechat');
require_once DISCUZ_ROOT . './source/plugin/singcere_wechat/class/wechat.lib.class.php';
$_G['singcere_wechat']['setting'] = unserialize($_G['setting']['singcere_wechat']);
$_G['singcere_wechat']['setting']['wechat_appsecret'] = authcode($_G['singcere_wechat']['setting']['wechat_appsecret'], 'DECODE', $_G['config']['security']['authkey']);
$wechat_client = new WeChatClient($_G['singcere_wechat']['setting']['wechat_appId'], $_G['singcere_wechat']['setting']['wechat_appsecret']);

define('URL_API_ROOT', 'https://api.weixin.qq.com');

$langs = &$scriptlang['singcere_wechat'];

if($_GET['op'] == 'checkToken') {
    $token = $_G['singcere_wechat']['setting']['wechat_token'];
    $timestamp = TIMESTAMP;
    $nonce = random(8, true);
    $echostr = random(20);
    
    $tmpArr = array($token, $timestamp, $nonce);
    sort($tmpArr, SORT_STRING);
    $tmpStr = implode( $tmpArr );
    $signature = sha1( $tmpStr );
   
    $url = $_G['siteurl'].'source/plugin/singcere_wechat/api.php?timestamp='.$timestamp.'&nonce='.$nonce.'&echostr='.$echostr.'&signature='.$signature;
    $data = dfsockopen($url);
    ajaxshowheader();
    $dbhost = 'DB Host: '.$_G['config']['db'][1]['dbhost'];
    if($_G['setting']['bbclosed']) {
        echo '<script>$(\'token_check\').innerHTML = "<img align=\'absmiddle\' src=\'static/image/admincp/cloud/wrong.gif\' /> '.$langs['admincp_doctor_error'].' ('.'<a href=\'?action=setting&operation=basic\'>'.cplang('setting_basic_bbclosed').'</a>)";</script>';
    } else if($data == $echostr) {
        echo '<script>$(\'token_check\').innerHTML = "<img align=\'absmiddle\' src=\'static/image/admincp/cloud/right.gif\' /> <a href=\''.$url.'\' target=\'_blank\'>'.$langs['admincp_doctor_right'].'</a> ('.$dbhost.')";</script>';
    } else {
        echo '<script>$(\'token_check\').innerHTML = "<img align=\'absmiddle\' src=\'static/image/admincp/cloud/wrong.gif\' /> <a href=\''.$url.'\' target=\'_blank\'>'.$langs['admincp_doctor_error'].'</a> ('.$dbhost.')";</script>';
    }
    ajaxshowfooter();
} else if($_GET['op'] == 'checkAccessToken') {
    register_shutdown_function('curl_shutdown', 'accesstoken_check');
    ajaxshowheader();
    if(empty($_G['singcere_wechat']['setting']['wechat_appId'])) {
        echo '<script>$(\'accesstoken_check\').innerHTML = "<img align=\'absmiddle\' src=\'static/image/admincp/cloud/wrong.gif\' /> '.$langs['admincp_doctor_appid_empty'].'";</script>';
    } else {
        $cachename = 'wechatat_' . $_G['singcere_wechat']['setting']['wechat_appId'];
        loadcache($cachename);
        $accesstoken_cache = $_G['cache'][$cachename];
        if($accesstoken_cache['token'] && $accesstoken_cache['expiration'] > time() && empty($_GET['focus'])) {
            echo '<script>$(\'accesstoken_check\').innerHTML = "<img align=\'absmiddle\' src=\'static/image/admincp/cloud/right.gif\' /> '.$accesstoken_cache['token'].' ('.$langs['admincp_doctor_expiration'].': '.dgmdate($accesstoken_cache['expiration'], 'Y-m-d H:i').' <a href=\"javascript:;\" onclick=\"focus_update_access_token();\">'.$langs['admincp_doctor_focus_refresh'].'</a>)";</script>';
        } else {
            $url = URL_API_ROOT."/cgi-bin/token?grant_type=client_credential&appid=".$_G['singcere_wechat']['setting']['wechat_appId']."&secret=".$_G['singcere_wechat']['setting']['wechat_appsecret'];
            $json = WeChatClient::get($url);
            $res = json_decode($json, true);
            if (WeChatClient::checkIsSuc($res)) { 
                $accesstoken_cache = array(
                    'token' => $res['access_token'],
                    'expiration' => time() + (int) ($res['expires_in'])
                );
                savecache($cachename, $accesstoken_cache);
                echo '<script>$(\'accesstoken_check\').innerHTML = "<img align=\'absmiddle\' src=\'static/image/admincp/cloud/right.gif\' /> '.$accesstoken_cache['token'].' ('.$langs['admincp_doctor_expiration'].': '.dgmdate($accesstoken_cache['expiration'], 'Y-m-d H:i').')";</script>';
            } else {
                echo '<script>$(\'accesstoken_check\').innerHTML = "<img align=\'absmiddle\' src=\'static/image/admincp/cloud/wrong.gif\' /> '.$res['errmsg'].'";</script>';
            }
        }

    }
    ajaxshowfooter();
} else if($_GET['op'] == 'checkJsTicket') {
    register_shutdown_function('curl_shutdown', 'jsticket_check');
    ajaxshowheader();
    if(empty($_G['singcere_wechat']['setting']['wechat_appId'])) {
        echo '<script>$(\'jsticket_check\').innerHTML = "<img align=\'absmiddle\' src=\'static/image/admincp/cloud/wrong.gif\' /> '.$langs['admincp_doctor_appid_empty'].'";</script>';
    }
    $cachename = 'wechatjst_' . $_G['singcere_wechat']['setting']['wechat_appId'];
    loadcache($cachename);
    $jsticket_cache = $_G['cache'][$cachename];
    if($jsticket_cache['ticket'] && $jsticket_cache['expiration'] > time() && empty($_GET['focus'])) {
        echo '<script>$(\'jsticket_check\').innerHTML = "<img align=\'absmiddle\' src=\'static/image/admincp/cloud/right.gif\' /> '.$jsticket_cache['ticket'].' ('.$langs['admincp_doctor_expiration'].': '.dgmdate($jsticket_cache['expiration'], 'Y-m-d H:i:s').' <a href=\"javascript:;\" onclick=\"focus_update_js_ticket();\">'.$langs['admincp_doctor_focus_refresh'].'</a>)";</script>';
    } else {
        $url = URL_API_ROOT."/cgi-bin/ticket/getticket?type=1&access_token=".$wechat_client->getAccessToken();
        $json = WeChatClient::get($url);
        $res = json_decode($json, true);
        if (WeChatClient::checkIsSuc($res)) { 
            $jsticket_cache = array(
                'ticket' => $res['ticket'],
                'expiration' => time() + (int) ($res['expires_in'])
            );
            savecache($cachename, $jsticket_cache);
            echo '<script>$(\'jsticket_check\').innerHTML = "<img align=\'absmiddle\' src=\'static/image/admincp/cloud/right.gif\' /> '.$jsticket_cache['ticket'].' ('.$langs['admincp_doctor_expiration'].': '.dgmdate($jsticket_cache['expiration'], 'Y-m-d H:i:s').')";</script>';
        } else {
            echo '<script>$(\'jsticket_check\').innerHTML = "<img align=\'absmiddle\' src=\'static/image/admincp/cloud/wrong.gif\' /> '.$res['errmsg'].'";</script>';
        }
    }
    ajaxshowfooter();
} else if($_GET['op'] == 'checkPostHttps') {
    register_shutdown_function('curl_shutdown', 'checkPostHttps');
    ajaxshowheader();
    
    $access_token = $wechat_client->getAccessToken();
    $url = URL_API_ROOT."/cgi-bin/message/template/send?access_token=$access_token";
    $json = json_encode(
        array(
            'touser' => 1,
            'template_id' => 2,
            'topcolor' => '#FFFFFF',
            'data' => $data
        )
    );
    
    $res = scurl_post($url, $json);
    $res = json_decode($res, true);
    if($res['errcode'] && $res['errcode'] != '-9999') {
        echo '<script>$(\'checkPostHttps\').innerHTML = "<img align=\'absmiddle\' src=\'static/image/admincp/cloud/right.gif\' /> '.$lang['available'].'";</script>';
    } else {
        echo '<script>$(\'checkPostHttps\').innerHTML = "<img align=\'absmiddle\' src=\'static/image/admincp/cloud/wrong.gif\' /> '.$res['errmsg'].'";</script>';
    }
    
    ajaxshowfooter();
} else if($_GET['op'] == 'checkAddon') {
    ajaxshowheader();
    require_once libfile('function/cloudaddons');
    require_once DISCUZ_ROOT.'./source/discuz_version.php';
    
    $addonid = 'singcere_wechat.plugin'; 
    $revisionid = '';
    $array = cloudaddons_getmd5($addonid);
    
    $uniqueid = $_G['setting']['siteuniqueid'] ? $_G['setting']['siteuniqueid'] : C::t('common_setting')->fetch('siteuniqueid');
    $data = 'siteuniqueid='.rawurlencode($uniqueid).'&siteurl='.rawurlencode($_G['siteurl']).'&sitever='.DISCUZ_VERSION.'/'.DISCUZ_RELEASE.'&sitecharset='.CHARSET.'&mysiteid='.$_G['setting']['my_siteid'];
    $param = 'data='.rawurlencode(base64_encode($data));
    $param .= '&md5hash='.substr(md5($data.TIMESTAMP), 8, 8).'&timestamp='.TIMESTAMP;
    $cloudaddons_url = CLOUDADDONS_DOWNLOAD_URL.'?'.$param.'&from=s';
    $extra_url = '&mod=app&ac=validator&ver=2&addonid='.$addonid.($array !== false ? '&rid='.($revisionid ? $revisionid : $array['RevisionID']).'&sn='.$array['SN'].'&rd='.$array['RevisionDateline'] : '');
    
    $result = dfsockopen($cloudaddons_url.$extra_url, 0, '', '', false, CLOUDADDONS_DOWNLOAD_IP, 999);
    if($result === '0') {
        echo '<script>$(\'addon_check\').innerHTML = "<img align=\'absmiddle\' src=\'static/image/admincp/cloud/right.gif\' /> '.$langs['admincp_doctor_right'].'";</script>';
    }
    ajaxshowfooter(); 
} 

$appService = Cloud::loadClass('Service_App');
$doctorService = Cloud::loadClass('Service_Doctor');
require_once DISCUZ_ROOT.'./source/discuz_version.php';



showtips($langs['admincp_doctor_tips']);
echo '<script type="text/javascript">var disallowfloat = "";</script>';

showtableheader();
showtagheader('tbody', '', true);
showtitle($langs['admincp_doctor_server'].'<div style="float:right;padding-right:10px;"><a target="_blank" href="'."$_G[siteurl]plugin.php?id=singcere_wechat:misc&ac=phpinfo&auth=". base64_encode(authcode($_G['uid'] . "\t" . TIMESTAMP, 'ENCODE')) .'">phpinfo</a></div>');
showtablerow('', array('class="td24"'), array(
    '<strong>'.cplang('cloud_timecheck').'</strong>',
    '<span id="cloud_time_check">' . cplang('cloud_doctor_time_check', array('imgdir' => $_G['style']['imgdir'])) .'</span>',
));

showtablerow('', array('class="td24"'), array(
    '<strong>'.$langs['admincp_doctor_dns'].'</strong>',
    checkDNS('api.weixin.qq.com') ? $lang['cloud_doctor_result_success'].' '.$lang['available'] : $lang['cloud_doctor_result_failure'].' '.$langs['admincp_doctor_dns_failure'].': api.weixin.qq.com'
));

/*$extensions = get_loaded_extensions();
$curlcheckstr = '';
if(!in_array("curl", $extensions)) {
    $curlcheckstr = 'curl is not loaded';
} else */
if(!function_exists('curl_init')|| !function_exists('curl_exec')) {
    $curlcheckstr = 'curl_init or curl_exec is disabled';
} else {
    $curl_version = curl_version();
    $ssl_version = isset($curl_version['ssl_version']) ? $curl_version['ssl_version'] : "";
    if(!$ssl_version) {
        $curlcheckstr = "miss SSL , curl_version:$ssl_version";
    } 
}

$curlbaidu = ' <a target="_blank" href="' . "$_G[siteurl]plugin.php?id=singcere_wechat:misc&ac=curlbaidu" . '">check: curl baidu via https</a>';


showtablerow('', array('class="td24"'), array(
    '<strong>CURL</strong>',
    (!$curlcheckstr ? $lang['cloud_doctor_result_success'].' '.$curl_version['version'].' , '.$curl_version['ssl_version'].' , '.'protocols: '.implode(',', $curl_version['protocols']) : $lang['cloud_doctor_result_failure'].$curlcheckstr) . $curlbaidu
));


showtagfooter('tbody');



showtagheader('tbody', '', true);
showtitle($langs['admincp_doctor_wechat']);
showtablerow('', array('class="td24"'), array(
    '<strong>Addon</strong>',
    '<span id="addon_check">' . cplang('cloud_doctor_time_check', array('imgdir' => $_G['style']['imgdir'])) .'</span>',
));
showtablerow('', array('class="td24"'), array(
    '<strong>Token</strong>',
    '<span id="token_check">' . cplang('cloud_doctor_time_check', array('imgdir' => $_G['style']['imgdir'])) .'</span>',
));
showtablerow('', array('class="td24"'), array(
    '<strong>AppID & AppSecret</strong>',
    $_G['singcere_wechat']['setting']['wechat_appId'] && $_G['singcere_wechat']['setting']['wechat_appsecret'] ? $lang['cloud_doctor_result_success'].' '.$lang['available'] : $lang['cloud_doctor_result_failure']. " <a href='?action=plugins&operation=config&do=$pluginid&identifier=$plugin[identifier]&pmod=admincp_base'>$langs[admincp_doctor_setting_error]</a>"
));
showtablerow('', array('class="td24"'), array(
    '<strong>AccessToken</strong>',
    '<span id="accesstoken_check">' . cplang('cloud_doctor_time_check', array('imgdir' => $_G['style']['imgdir'])) .'</span>',
));
showtablerow('', array('class="td24"'), array(
    '<strong>JsApiTicket</strong>',
    '<span id="jsticket_check">' . cplang('cloud_doctor_time_check', array('imgdir' => $_G['style']['imgdir'])) .'</span>',
));
showtablerow('', array('class="td24"'), array(
    '<strong>'.$langs['admincp_doctor_post'].'</strong>',
    '<span id="checkPostHttps">' . cplang('cloud_doctor_time_check', array('imgdir' => $_G['style']['imgdir'])) .'</span>',
));
showtablerow('', array('class="td24"'), array(
    '<strong>'.$langs['admincp_doctor_avatar'].'</strong>',
    function_exists('gd_info') && is_writable($_G['setting']['attachdir'].'/temp/') ? $lang['cloud_doctor_result_success'].' '.$lang['available'] : $lang['cloud_doctor_result_failure'].lang('plugin/singcere_wechat', 'admincp_doctor_avatar_error', array('path' => $_G['setting']['attachdir'].'/temp/'))
));
showtagfooter('tbody');




showtablefooter();
$doctorService->showCloudDoctorJS();

$adminscript = ADMINSCRIPT;
$output = <<<EOF
<script type="text/javascript">
    ajaxget("{$adminscript}?action=plugins&operation=config&do=$pluginid&identifier=$plugin[identifier]&pmod=admincp_doctor&op=checkAddon");
    ajaxget("{$adminscript}?action=plugins&operation=config&do=$pluginid&identifier=$plugin[identifier]&pmod=admincp_doctor&op=checkToken");
    ajaxget("{$adminscript}?action=plugins&operation=config&do=$pluginid&identifier=$plugin[identifier]&pmod=admincp_doctor&op=checkAccessToken");
    ajaxget("{$adminscript}?action=plugins&operation=config&do=$pluginid&identifier=$plugin[identifier]&pmod=admincp_doctor&op=checkJsTicket");
    ajaxget("{$adminscript}?action=plugins&operation=config&do=$pluginid&identifier=$plugin[identifier]&pmod=admincp_doctor&op=checkPostHttps");
        
    function focus_update_access_token() {
        $('accesstoken_check').innerHTML = '<img src="static/image/common/loading.gif" class="vm"> wait...';
        ajaxget("{$adminscript}?action=plugins&operation=config&do=$pluginid&identifier=$plugin[identifier]&pmod=admincp_doctor&op=checkAccessToken&focus=true");
    }  

    function focus_update_js_ticket() {
        $('jsticket_check').innerHTML = '<img src="static/image/common/loading.gif" class="vm"> wait...';
        ajaxget("{$adminscript}?action=plugins&operation=config&do=$pluginid&identifier=$plugin[identifier]&pmod=admincp_doctor&op=checkJsTicket&focus=true");
    }
        
</script>
EOF;

echo $output;

function checkDNS($url) {
    if (empty($url)) {
        return false;
    }
    $matches = parse_url($url);
    $host = !empty($matches['host']) ? $matches['host'] : $matches['path'];
    if (!$host) {
        return false;
    }
    $ip = gethostbyname($host);
    if ($ip == $host) {
        return false;
    } else {
        return $ip;
    }
}

function curl_shutdown($id) {
    $error = error_get_last();
    
    if ($error['type'] &~ E_NOTICE &~ E_DEPRECATED) {
        ob_end_clean();
        ajaxshowheader();
        echo '<script>$(\''.$id.'\').innerHTML = "<img align=\'absmiddle\' src=\'static/image/admincp/cloud/wrong.gif\' /> '.$error['type'].': '.$error['message'].'";</script>';
        ajaxshowfooter();
    } 
}


 