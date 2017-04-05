<?php

class wxNotifyResponse
{
    public function reply($inputObj)
    {
        require_once DISCUZ_ROOT . '/source/plugin/singcere_wechat/class/wxpay.class.php';
        WxPayApi::notify(array($this, 'handler'), $message, true);
    }

    public function handler($result)
    {
        global $_G;

        // 仅处理金额超过100fee 此处注意订单号使用 打赏订单号防止 重复通知时造成的红包多次发送
        if ($result['total_fee'] >= 100 && $_G['singcere_wechat']['setting']['reward_payauthor'] && $_G['singcere_wechat']['setting']['reward_authorscale'] > 0) {

            $scale = min(intval($_G['singcere_wechat']['setting']['reward_authorscale']), 100);
            $pay = C::t('#singcere_wechat#singcere_wechat_pay')->fetch_by_transactionid($result['transaction_id']);
            if ($pay['fromtype'] == 'SC_WECHAT_REWARD') {

                $redpack = C::t('#singcere_wechat#singcere_wechat_redpack')->fetch_by_billno($result['out_trade_no']);
                if(empty($redpack)) {
                    include libfile('function/forum');
                    $thread = get_thread_by_tid($pay['fromid']);
                    $thread && $bind = C::t('#singcere_wechat#singcere_wechat_bind')->fetch_by_uid($thread['authorid']);
                    if($bind) {
                        $input = new WxPayData();
                        $input->mch_billno = $result['out_trade_no'];
                        $input->send_name = diconv(lang('plugin/singcere_wechat', 'reward_redpack_name'), CHARSET, 'utf-8');
                        $input->wishing = diconv(cutstr($thread['subject'], 32, '...'), CHARSET, 'utf-8');
                        $input->act_name = diconv(lang('plugin/singcere_wechat', 'reward_redpack_actname'), CHARSET, 'utf-8');
                        $input->remark = diconv(lang('plugin/singcere_wechat', 'reward_redpack_actname'), CHARSET, 'utf-8');
                        $input->re_openid = $bind['openid'];
                        $input->total_amount = max(100, $result['total_fee'] * $scale / 100);
                        $input->total_num = 1;
                        $input->fromtype = $pay['fromtype'];
                        $input->fromid = $pay['fromid'];
                        $result = WxPayApi::sendredpack($input);
                    }
                }
            }
        }
    }
}