<?php

/**
 *      (C)2007-2012 singcere.net
 *      This is a freeware, But you have no right to modify or distribute
 *      $Id$
 */

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

if ($_GET['ac'] == 'rewarduser') {
    $perpage = 8;
    $tid = intval($_GET['tid']);
    $page = max(1, intval($_GET['page']));

    $supports = DB::fetch_all("SELECT * FROM %t WHERE fromid = %d AND fromtype = %s AND transaction_id <> '' ORDER BY dateline DESC" . DB::limit(($page - 1) * $perpage, $perpage), array('singcere_wechat_pay', $tid, 'SC_WECHAT_REWARD'));

    include template('common/header_ajax');
    foreach ($supports as $support) {
        echo '<a onmouseover="show_reward_tips(this)" tip="' . ($support['uid'] ? $support['username'] : '&#19968;&#20301;&#27809;&#26377;&#30041;&#19979;&#30165;&#36857;&#30340;&#32593;&#21451;') . ' &#183; ' . strip_tags(dgmdate($support['dateline'], 'u')) . '" class="avatar" href="javascript:void(0)">' . avatar($support['uid'], 'small') . '</a>';
    }
    if (count($supports) == $perpage) {
        $id = 'reward_newlist_' . random(5);
        echo '<span id="' . $id . '"><a class="avatar more" href="javascript:show_reward_user(\'' . $id . '\', ' . ($page + 1) . ');"><i>...</i></a></span>';
    }
    include template('common/footer_ajax');

} else if ($_GET['ac'] == 'rewardpay') {
    include libfile('function/forum');
    $thread = get_thread_by_tid($_GET['tid']);
    if (empty($thread)) {
        showmessage(lang('plugin/singcere_wechat', 'reward_thread_nofound'));
    }

    if (defined('IN_MOBILE') && !IN_WECHAT) {
        showmessage(lang('plugin/singcere_wechat', 'reward_need_in_wechat'));
    }

    if (IN_WECHAT) {
        $openid = sc_wechat_auth(true);
    }

    $values = explode(' ', $_G['singcere_wechat']['setting']['reward_candidate']);

    if (!submitcheck('paysubmit')) {
        $tipsarr = array();
        foreach (explode("\n", $_G['singcere_wechat']['setting']['reward_tips']) as $tips) {
            if ($tips = trim($tips)) {
                $tipsarr[] = $tips;
            }
        }
        $tips = $tipsarr[$thread['tid'] % count($tipsarr)];



        include template('singcere_wechat:rewardpay');
    } else {
        include DISCUZ_ROOT . 'source/plugin/singcere_wechat/class/wxpay.class.php';
        $input = new WxPayData();
        $input->body = diconv(cutstr($thread['subject'], 32, ''), CHARSET, 'utf-8');
        $input->out_trade_no = 'REWARD_' . date('YmdHis') . str_pad(mt_rand(1000000, 9999999), 7, '0', STR_PAD_LEFT);   // size: 28
        $input->total_fee = 1;
        $input->time_start = date('YmdHis');
        $input->time_expire = date('YmdHis', TIMESTAMP + 600);
        $input->notify_url = $_G['siteurl'] . 'source/plugin/singcere_wechat/api.php';

        $input->product_id = $thread['tid'];
        $input->fromtype = 'SC_WECHAT_REWARD';
        $input->fromid = $thread['tid'];

        $_GET['price'] = floatval($_GET['price']);
        if (empty($_GET['price']) || !in_array($_GET['price'], $values)) {
            $_GET['price'] = current($values);
        }

        $input->total_fee = intval($_GET['price']*100);

        if (!IN_WECHAT) {    // native
            $input->trade_type = 'NATIVE';
            try {
                $nativePay = new NativePay();
                $result = $nativePay->getPayUrl($input);
            } catch (WxPayException $e) {
                showmessage(diconv($e->errorMessage(), 'utf-8', CHARSET));
            }

            $result = sc_diconv($result, 'utf-8', CHARSET);
            if ($result['return_code'] == 'FAIL') {
                showmessage($result['return_msg']);
            }

            if ($result['result_code'] == 'FAIL') {
                showmessage($result['err_code_des']);
            }

            include DISCUZ_ROOT . 'source/plugin/singcere_wechat/class/phpqrcode.class.php';
            ob_start();
            QRCode::png($result['code_url'], false, 0, 6, 0);
            $qrcode = base64_encode(ob_get_contents());
            ob_end_clean();
            include template('singcere_wechat:rewardpay');
        } else {    // js
            $input->trade_type = 'JSAPI';
            $input->openid = $openid;

            try {
                $jsPay = new JsApiPay();
                $result = WxPayApi::unifiedOrder($input);

            } catch (WxPayException $e) {
                showmessage(diconv($e->errorMessage(), 'utf-8', CHARSET));
            }

            $result = sc_diconv($result, 'utf-8', CHARSET);
            if ($result['return_code'] == 'FAIL') {
                showmessage($result['return_msg']);
            }

            if ($result['result_code'] == 'FAIL') {
                showmessage($result['err_code_des']);
            }
            try {
                echo $jsPay->getJsApiParameters($result);
            } catch(WxPayException $e) {
                showmessage(diconv($e->errorMessage(), 'utf-8', CHARSET));
            }
            exit;
        }

    }
} else if ($_GET['ac'] == 'orderquery') {
    if ($_GET['formhash'] != FORMHASH || (empty($_GET['out_trade_no']) && empty($_GET['transaction_id']))) {
        $echostr = urldecode(json_encode(array('error' => 1, 'msg' => urlencode(lang('plugin/singcere_wechat', 'reward_noperm')))));
    } else {
        include DISCUZ_ROOT . 'source/plugin/singcere_wechat/class/wxpay.class.php';
        $nativeInput = new WxPayData();
        if ($_GET['transaction_id']) {
            $nativeInput->transaction_id = $_GET['transaction_id'];
        } else {
            $nativeInput->out_trade_no = $_GET['out_trade_no'];
        }

        $result = sc_diconv(WxPayApi::orderQuery($nativeInput), 'utf-8');
        if ($result['return_code'] == 'FAIL') {
            $echostr = json_encode(array('error' => 2, 'msg' => $result['return_msg']));
        } else if ($result['result_code'] == 'FAIL') {
            $echostr = json_encode(array('error' => 3, 'msg' => $result['err_code_des']));
        }
        $echostr = json_encode(array('error' => 0, 'msg' => '', 'trade_state' => $result['trade_state']));
    }

    include template('common/header_ajax');
    echo $echostr;
    include template('common/footer_ajax');
    exit;
}


