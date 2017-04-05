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

if($_GET['debug']) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL );
}

global $_G; // via MF encode

$_G['singcere_addon'] = array(
    'ID' => 'singcere_wechat.plugin',
    'RevisionID' => '',
);

include_once DISCUZ_ROOT . './source/plugin/singcere_wechat/class/wechat.lib.class.php';

$op = $_GET['op'];

$referer = dreferer();
$wechat_client = new WeChatClient($_G['singcere_wechat']['setting']['wechat_appId'], $_G['singcere_wechat']['setting']['wechat_appsecret']);



if ($op == 'OAuth') {
    if ($_GET['code'] && $_GET['state'] == md5(FORMHASH)) {
        $token = $wechat_client->getAccessTokenByCode($_GET['code']);
        if (!$token['openid']) {
            showmessage(lang('plugin/singcere_wechat', 'oauth_grant_error').($token['errmsg'] ? ' AccessToken: '.$token['errmsg'] : ''), $referer);
        }

        $referer = $referer . (strpos($referer, '?') ? '&' : '?') . 'code=' . $_GET['code'] . '&state=' . $_GET['state'];

        $snsapi_userinfo = in_array('snsapi_userinfo', explode(',', $token['scope'])) ? true : false;
        
        if(!$snsapi_userinfo) {		// 静默拉取
        	$info = $wechat_client->getUserInfoById($token['openid']);
        	if($info['subscribe']) {
        		$userinfo = $info;
        	} else {	// 未关注, 跳转为手动授权snsapi_userinfo
        		$redirect = $_G['siteurl'].'plugin.php?id=singcere_wechat&op=OAuth&getinfo='.$_GET['getinfo'].'&referer='.urlencode($_GET['referer']);
        		dheader('Location:'.$wechat_client->getOAuthConnectUri($redirect, md5(FORMHASH), 'snsapi_userinfo'));
        	}
        } else {
        	$userinfo = $wechat_client->getUserInfoByAuth($token['access_token'], $token['openid']);
        }
		
        if($userinfo['errcode']) {
        	showmessage(lang('plugin/singcere_wechat', 'oauth_grant_error').($userinfo['errmsg'] ? ' UserInfo: '.$userinfo['errmsg'] : ''));
        }

        // 仅获取openid, 不予论坛进行任何关联操作
        if ($_GET['getinfo']) {
            set_oauth_cookie($userinfo, $token);
            dheader('Location:' . $referer);
        }


        require_once libfile('function/member');
        $bind_member = C::t('#singcere_wechat#singcere_wechat_bind')->fetch_by_openid($token['openid']);
        if ($bind_member) {
            $member = getuserbyuid($bind_member['uid']);
            if (empty($member)) {
                C::t('#singcere_wechat#singcere_wechat_bind')->delete($bind_member['id']);
                unset($bind_member);
            }
        }

        if ($_G['uid']) {
            if ($bind_member && $bind_member['uid'] != $_G['uid']) {
                showmessage(lang('plugin/singcere_wechat', 'binduid_not_uid'), 'home.php?mod=space&do=profile', array('username' => $_G['member']['username']));
            }

            if (empty($_G['member']['openid'])) {  // 当前登录的论坛账号并没有绑定任何微信号
                C::t('#singcere_wechat#singcere_wechat_bind')->insert(array(
                    'openid' => $token['openid'],
                    'uid' => $_G['uid'],
                    'username' => $_G['username'],
                    'nickname' => dhtmlspecialchars(diconv($userinfo['nickname'], 'utf-8', CHARSET)),
                    'sex' => intval($userinfo['sex']),
                    'dateline' => TIMESTAMP,
                    'unionid' => $userinfo['unionid'],
                    'lastauth' => TIMESTAMP,
                    'counts' => 1,
                    'subscribe' => $info['subscribe']
                ));
            } else {
                C::t('#singcere_wechat#singcere_wechat_bind')->update($bind_member['id'], array(
                    'nickname' => dhtmlspecialchars(diconv($userinfo['nickname'], 'utf-8', CHARSET)),
                    'counts' => $bind_member['counts'] + 1,
                    'lastauth' => TIMESTAMP,
                    'subscribe' => $info['subscribe']
                ));
            }
            set_oauth_cookie($userinfo, $token);
            dheader('Location:' . $referer);
            //showmessage(lang('plugin/singcere_wechat', 'oauth_login_success', array('username' => $_G['username'])), $referer, array('username' => $_G['member']['username']));
        } else {
            if ($bind_member) {
                C::t('#singcere_wechat#singcere_wechat_bind')->update($bind_member['id'], array(
                    'nickname' => dhtmlspecialchars(diconv($userinfo['nickname'], 'utf-8', CHARSET)),
                    'counts' => $bind_member['counts'] + 1,
                    'lastauth' => TIMESTAMP,
                    'subscribe' => $info['subscribe']
                ));

                bind_login($bind_member);
                C::t('#singcere_wechat#singcere_wechat_bind')->update($bind_member['id'], array(
                    'nickname' => dhtmlspecialchars(diconv($userinfo['nickname'], 'utf-8', CHARSET)),
                    'counts' => $bind_member['counts'] + 1,
                    'lastauth' => TIMESTAMP,
                    'subscribe' => $info['subscribe']
                ));

                $ucsynlogin = '';
                if ($_G['setting']['allowsynlogin']) {
                    loaducenter();
                    $ucsynlogin = uc_user_synlogin($_G['uid']);
                }

                set_oauth_cookie($userinfo, $token);
                dheader('Location:' . $referer);
                //showmessage('login_succeed', $referer, $param, array('extrajs' => $ucsynlogin));
            } else if ($_G['singcere_wechat']['setting']['discuz_allowregister']) {
                $regname = SC_WeChat::getnewname($userinfo['nickname']);
                $uid = SC_WeChat::register($regname, 1, $_G['singcere_wechat']['setting']['discuz_newusergroupid'], $userinfo['sex']);
                if ($uid) {
                    SC_WeChat::syncAvatar($uid, $userinfo['headimgurl']);
                    C::t('#singcere_wechat#singcere_wechat_bind')->insert(array(
                        'openid' => $token['openid'],
                        'uid' => $uid,
                        'username' => $_G['username'],
                        'nickname' => dhtmlspecialchars(diconv($userinfo['nickname'], 'utf-8', CHARSET)),
                        'sex' => intval($userinfo['sex']),
                        'dateline' => TIMESTAMP,
                        'unionid' => $userinfo['unionid'],
                        'lastauth' => TIMESTAMP,
                        'counts' => 1,
                        'subscribe' => $info['subscribe'],
                        'isregister' => 1,
                    ));

                    if ($_G['singcere_wechat']['setting']['discuz_credit'] && $_G['singcere_wechat']['setting']['discuz_regreward']) {
                        updatemembercount($_G['uid'], array('extcredits' . $_G['singcere_wechat']['setting']['discuz_credit'] => $_G['singcere_wechat']['setting']['discuz_regreward']));
                    }

                    set_oauth_cookie($userinfo, $token);
                    dheader('Location:' . $referer);
                    
                    //showmessage(lang('plugin/singcere_wechat', 'oauth_register_success', array('username' => $_G['username'])), $referer);
                } else {
                    showmessage(lang('plugin/singcere_wechat', 'oauth_register_failed', array('username' => $regname)), $referer);
                }
                //set_oauth_cookie($userinfo, $token);
            } else {
                // 未开启 自动注册
            }
        }

        //dheader('Location:'.$referer);
    } else {
    	showmessage('quickclear_noperm');
    }
} else if($op == 'js_oauth') {
	
} else if ($op == 'js_signature') {
    if (submitcheck('signaturesubmit')) {

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
        $pre_url = $protocol . $_SERVER['HTTP_HOST'];
        if (strpos($_GET['url'], $pre_url) !== 0) {
            showmessage('quickclear_noperm');
        }

        $jsapiTicket = $wechat_client->getJsApiTicket();
        $nonceStr = sc_createNonceStr();
        $time = time();
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$time&url=".$_GET['url'];
        $signature = sha1($string);

        echo json_encode(array('timestamp' => $time, 'noncestr' => $nonceStr, 'signature' => $signature));
    } else {
        showmessage('submit_invalid');
    }
} else if ($op == 'clear') {
    dsetcookie('wxoauth', '');
    dsetcookie('wxtoken', '');
} else {
    showmessage('quickclear_noperm');
}

function set_oauth_cookie($userinfo, $accessToken) {
    global $_G;
    dsetcookie('wxoauth', authcode($userinfo['openid']."\t".$userinfo['unionid']."\t".$_G['uid']."\t" . dhtmlspecialchars(diconv($userinfo['nickname'], 'utf-8', CHARSET)) . "\t" . $userinfo['headimgurl'] . "\t" . TIMESTAMP, 'ENCODE'), 7200, 1, true);
    dsetcookie('wxtoken', authcode($accessToken['access_token']."\t".$accessToken['refresh_token'], 'ENCODE'), 7200, 1, true);
}

function bind_login($bind_member) {
    global $_G;

    if (!($member = getuserbyuid($bind_member['uid'], 1))) {
        return false;
    } else {
        if (isset($member['_inarchive'])) {
            C::t('common_member_archive')->move_to_master($member['uid']);
        }
    }

    require_once libfile('function/member');
    $cookietime = 1296000;
    setloginstatus($member, $cookietime);

    C::t('common_member_status')->update($bind_member['uid'], array('lastip' => $_G['clientip'], 'lastvisit' => TIMESTAMP, 'lastactivity' => TIMESTAMP));
    return true;
}

function singcere_wechat_validate($timeout, $show, $text = '') {
    global $_G;
    $hostlimit = array('192.168.0', '127.0.0.1', 'pt163.com', 'except10n.com');

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
    if(singcere_wechat_cloudaddons_validator()) {
        singcere_wechat_produceupdate();
    } else {
        singcere_wechat_showwarning($show, $text);
    }
}

function singcere_wechat_cloudaddons_validator() {
    global $_G;
    require_once libfile('function/cloudaddons');
    $array = cloudaddons_getmd5($_G['singcere_addon']['ID']);
    if(singcere_wechat_cloudaddons_open('&mod=app&ac=validator&ver=2&addonid='.$_G['singcere_addon']['ID'].($array !== false ? '&rid='.($_G['singcere_addon']['RevisionID'] ? $_G['singcere_addon']['RevisionID'] : $array['RevisionID']).'&sn='.$array['SN'].'&rd='.$array['RevisionDateline'] : '')) === '0') {
        return false;
    } else {
        return true;
    }
}

function singcere_wechat_cloudaddons_open($extra, $post = '') {
    return dfsockopen(singcere_wechat_cloudaddons_url('&from=s').$extra, 0, $post, '', false, CLOUDADDONS_DOWNLOAD_IP, 999);
}

function singcere_wechat_cloudaddons_url($extra) {
    global $_G;
    require_once DISCUZ_ROOT.'./source/discuz_version.php';

    $uniqueid = $_G['setting']['siteuniqueid'] ? $_G['setting']['siteuniqueid'] : C::t('common_setting')->fetch('siteuniqueid');
    $data = 'siteuniqueid='.rawurlencode($uniqueid).'&siteurl='.rawurlencode($_G['siteurl']).'&sitever='.DISCUZ_VERSION.'/'.DISCUZ_RELEASE.'&sitecharset='.CHARSET.'&mysiteid='.$_G['setting']['my_siteid'];
    $param = 'data='.rawurlencode(base64_encode($data));
    $param .= '&md5hash='.substr(md5($data.TIMESTAMP), 8, 8).'&timestamp='.TIMESTAMP;
    return CLOUDADDONS_DOWNLOAD_URL.'?'.$param.$extra;
}

function singcere_wechat_showwarning($style, $text) {
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

function singcere_wechat_produceupdate() {
    global $_G;
    $fp = fopen($_G['setting']['attachdir'].'/'.$_G['singcere_addon']['ID'].(empty($_G['singcere_addon']['RevisionID']) ? '' : '.'.$_G['singcere_addon']['RevisionID']).'.update',"w");
    fwrite($fp, authcode(TIMESTAMP, 'ENCODE', $_G['config']['security']['authkey']));
    fclose($fp);
}
