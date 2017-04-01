<?php

/**
 *		作者：rpan.cn
 *		版权所有：阿木 & rpancn
 *		QQ:399051063
 *		申明：此插件非开源软件，您不得对插件源代码进行任何形式任何目的的再发布。
 *		=========================================================================
 *			  承接discuz插件、模板仿制定制业务，价格便宜交期快QQ399051063
 *		=========================================================================
 */
if (!defined('IN_DISCUZ')) {
    exit('Access Denied ROOT');
}

define('DISCUZ_PARTNER', $config['partner']);
define('DISCUZ_SECURITYCODE', $config['securitycode']);

function credit_payurl($pid, $cardvalue, $cardnum, $cardpwd, $amount, $Bank)
{
    global $_G;
    global $orderid, $gid, $days;
    $orderid = dgmdate(TIMESTAMP, 'YmdHis') . random(4);
    $args    = array(
        'Subject' => $_G['uid'] . '-' . $amount . '-' . $_G['clientip'],
        'UserID' => DISCUZ_PARTNER,
        'ChannelID' => $pid,
        'CardValue' => $cardvalue,
        'Bank' => $Bank,
        'CardID' => $cardnum,
        'CardPass' => $cardpwd,
        'OrderID' => $orderid,
        'Price' => $amount,
        'Quantity' => 1,
        'Notify_url' => $_G['siteurl'] . 'source/plugin/rpancn_buy_usergroup/notify_url.php',
        'Return_url' => $_G['siteurl'] . 'home.php?mod=spacecp&ac=usergroup',
        'Notic' => $_G['uid'] . '-buy:' . $gid . '-' . $days . '-' . $_G['siteurl'],
    );
    return trade_returnurl($args);
}

function trade_returnurl($args)
{
    global $_G;
    ksort($args);
    $urlstr = $sign = '';
    foreach ($args as $key => $val) {
        if ($val != '') {
            $sign .= '&' . $key . '=' . $val;
            $urlstr .= $key . '=' . rawurlencode($val) . '&';
        }
    }
    $sign = substr($sign, 1);
    $sign = md5($sign . DISCUZ_SECURITYCODE);
    return 'http://www.twpal.com/gateway/?' . $urlstr . 'sign=' . $sign . '&sign_type=MD5';
}

function trade_notifycheck()
{
    global $_G;
    if (!empty($_POST)) {
        $notify   = $_POST;
        $location = FALSE;
    } elseif (!empty($_GET)) {
        $notify   = $_GET;
        $location = TRUE;
    } else {
        exit('1Access Denied Notify');
    }
    if (!DISCUZ_SECURITYCODE) {
        exit('2Access Denied Code');
    }
    ksort($notify);
    $sign = '';
    foreach ($notify as $key => $val) {
        if ($key != 'Sign' && $key != 'Sign_Type')
            $sign .= "&$key=$val";
    }
    //print_r($notify);
    //echo substr($sign,1).DISCUZ_SECURITYCODE;
    if ($notify['Sign'] != md5(substr($sign, 1) . DISCUZ_SECURITYCODE)) {
        exit('3Access Denied Sign');
    }
    
    if ($notify['Trade_status'] == 'TRADE_FINISHED' || $notify['Trade_status'] == 'TRADE_SUCCESS') {
        return array(
            'validator' => TRUE,
            'status' => $notify['Trade_status'],
            'order_no' => $notify['OrderID'],
            'price' => $notify['Total_fee'],
            //'trade_no'	=> $notify['OrderID'],
            'notify' => 'success',
            'location' => $location
        );
    } else {
        return array(
            'validator' => FALSE,
            'notify' => 'fail',
            'location' => $location
        );
    }
}


?>