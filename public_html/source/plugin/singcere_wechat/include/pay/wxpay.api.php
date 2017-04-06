<?php

/**
 *      (C)2007-2012 singcere.net
 *      This is a freeware, But you have no right to modify or distribute
 *      $Id$
 */


/**
 * 接口访问类，包含所有微信支付API列表的封装，类中方法为static方法，
 * 每个接口有默认超时时间（除提交被扫支付为10s，上报超时时间为1s外，其他均为6s）
 * 接口编码均为UTF-8输入输出
 * @author widyhu
 */
class WxPayApi
{
    public static $senceid = array('JSAPI' => 1, 'NATIVE' => 2, 'APP' => 3, 'MICROPAY' => 4);

    /**
     * 企业红包发放,其中mch_billno、send_name、re_openid、total_amount、total_num、wishing、act_name必填
     *
     * @param WxPayData $inputObj
     * @param int $timeOut
     *
     * @throws WxPayException
     * @return 成功时返回，其他抛异常
     */

    public static function sendredpack($inputObj, $timeOut = 6)
    {
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
        //检测必填参数
        if (!$inputObj->mch_billno) {
            throw new WxPayException("缺少商户订单号必填参数mch_billno！");
        } else if (!$inputObj->send_name) {
            throw new WxPayException("缺少红包发送者名称必填参数send_name！");
        } else if (!$inputObj->re_openid) {
            throw new WxPayException("缺少接受红包的用户必填参数re_openid！");
        } else if (!$inputObj->total_amount) {
            throw new WxPayException("缺少红包总金额必填参数total_amount！");
        } else if (!$inputObj->total_num) {
            throw new WxPayException("缺少红包发放总人数必填参数total_num！");
        } else if (!$inputObj->wishing) {
            throw new WxPayException("缺少红包祝福语必填参数wishing！");
        } else if (!$inputObj->act_name) {
            throw new WxPayException("缺少活动名称必填参数act_name！");
        }

        list($fromType, $fromId) = array($inputObj->fromtype, $inputObj->fromid);
        unset($inputObj->fromtype, $inputObj->fromid);

        $inputObj->wxappid = APPID;//公众账号ID
        $inputObj->mch_id = MCHID;//商户号
        $inputObj->client_ip = $_SERVER['REMOTE_ADDR'];//终端ip
        $inputObj->nonce_str = WxPayData::makeNonceStr();//随机字符串

        //签名
        $inputObj->sign = $inputObj->makeSign();
        $xml = $inputObj->toXml();
        $startTimeStamp = self::getMillisecond();//请求开始时间
        $response = self::postXmlCurl($xml, $url, true, $timeOut);
        $result = WxPayData::resultInit($response, false);

        if ($result['result_code'] == 'SUCCESS') {
            C::t('#singcere_wechat#singcere_wechat_redpack')->insert(array(
                're_openid'    => $inputObj->re_openid,
                'mch_billno'   => $inputObj->mch_billno,
                'send_name'    => dhtmlspecialchars(diconv($inputObj->send_name, 'utf-8')),
                'total_amount' => $result['total_amount'],
                'total_num'    => intval($inputObj->total_num),
                'amt_type'     => dhtmlspecialchars($inputObj->amt_type),
                'wishing'      => dhtmlspecialchars(diconv($inputObj->wishing, 'utf-8')),
                'act_name'     => dhtmlspecialchars(diconv($inputObj->act_name, 'utf-8')),
                'remark'       => intval(diconv($inputObj->remark, 'utf-8')),
                'fromtype'     => $fromType,
                'fromid'       => $fromId,
                'send_listid'  => $result['send_listid'],
                'dateline'     => TIMESTAMP,
            ), true, true);
        }

        self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间

        return $result;
    }

    /**
     * 统一下单，WxPayUnifiedOrder中out_trade_no、body、total_fee、trade_type必填
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     *
     * @param WxPayData $inputObj
     * @param int $timeOut
     *
     * @throws WxPayException
     * @return 成功时返回，其他抛异常
     */
    public static function unifiedOrder($inputObj, $timeOut = 6)
    {
        global $_G;

        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
        //检测必填参数
        if (!$inputObj->out_trade_no) {
            throw new WxPayException("缺少统一支付接口必填参数out_trade_no！");
        } else if (!$inputObj->body) {
            throw new WxPayException("缺少统一支付接口必填参数body！");
        } else if (!$inputObj->total_fee) {
            throw new WxPayException("缺少统一支付接口必填参数total_fee！");
        } else if (!$inputObj->trade_type) {
            throw new WxPayException("缺少统一支付接口必填参数trade_type！");
        } else if (!$inputObj->notify_url) {
            throw new WxPayException("缺少统一支付接口必填参数notify_url！！");
        }

        //关联参数
        if ($inputObj->trade_type == "JSAPI" && !$inputObj->openid) {
            throw new WxPayException("统一支付接口中，缺少必填参数openid！trade_type为JSAPI时，openid为必填参数！");
        }
        if ($inputObj->trade_type == "NATIVE" && !$inputObj->product_id) {
            throw new WxPayException("统一支付接口中，缺少必填参数product_id！trade_type为JSAPI时，product_id为必填参数！");
        }

        list($fromType, $fromId) = array($inputObj->fromtype, $inputObj->fromid);
        unset($inputObj->fromtype, $inputObj->fromid);

        $inputObj->appid = APPID;//公众账号ID
        $inputObj->mch_id = MCHID;//商户号
        $inputObj->spbill_create_ip = $_SERVER['REMOTE_ADDR'];//终端ip
        //$inputObj->SetSpbill_create_ip("1.1.1.1");
        $inputObj->nonce_str = WxPayData::makeNonceStr();//随机字符串

        //签名
        $inputObj->sign = $inputObj->makeSign();
        $xml = $inputObj->toXml();

        $startTimeStamp = self::getMillisecond();//请求开始时间
        $response = self::postXmlCurl($xml, $url, false, $timeOut);
        $result = WxPayData::resultInit($response);

        if ($result['result_code'] == 'SUCCESS') {
            $senceid = self::$senceid[strtoupper($inputObj->trade_type)];
            C::t('#singcere_wechat#singcere_wechat_pay')->insert(array(
                'openid'       => $inputObj->openid,
                'unionid'      => $inputObj->unionid,
                'uid'          => $_G['uid'],
                'username'     => $_G['username'],
                'out_trade_no' => dhtmlspecialchars($inputObj->out_trade_no),
                'product_id'   => dhtmlspecialchars($inputObj->product_id),
                'senceid'      => $senceid,
                'prepay_id'    => $result['prepay_id'],
                'fee_type'     => $inputObj->fee_type ? dhtmlspecialchars($inputObj->fee_type) : 'CNY',
                'total_fee'    => intval($inputObj->total_fee),
                'attach'       => dhtmlspecialchars(diconv($inputObj->attach, 'utf-8')),
                'fromtype'     => $fromType,
                'fromid'       => $fromId,
                'dateline'     => TIMESTAMP,
            ), true, true);
        }
        self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间

        return $result;
    }

    /**
     * 查询订单，WxPayOrderQuery中out_trade_no、transaction_id至少填一个
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     *
     * @param WxPayOrderQuery $inputObj
     * @param int $timeOut
     *
     * @throws WxPayException
     * @return 成功时返回，其他抛异常
     */
    public static function orderQuery($inputObj, $timeOut = 6)
    {
        $url = "https://api.mch.weixin.qq.com/pay/orderquery";
        //检测必填参数
        if (!$inputObj->out_trade_no && !$inputObj->transaction_id) {
            throw new WxPayException("订单查询接口中，out_trade_no、transaction_id至少填一个！");
        }
        $inputObj->appid = APPID;//公众账号ID
        $inputObj->mch_id = MCHID;//商户号
        $inputObj->nonce_str = WxPayData::makeNonceStr();//随机字符串

        $inputObj->sign = $inputObj->makeSign();//签名
        $xml = $inputObj->ToXml();

        $startTimeStamp = self::getMillisecond();//请求开始时间
        $response = self::postXmlCurl($xml, $url, false, $timeOut);
        $result = WxPayData::resultInit($response);
        self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间

        return $result;
    }

    /**
     * 关闭订单，WxPayCloseOrder中out_trade_no必填
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     *
     * @param WxPayCloseOrder $inputObj
     * @param int $timeOut
     *
     * @throws WxPayException
     * @return 成功时返回，其他抛异常
     */
    public static function closeOrder($inputObj, $timeOut = 6)
    {
        $url = "https://api.mch.weixin.qq.com/pay/closeorder";
        //检测必填参数
        if (!$inputObj->out_trade_no) {
            throw new WxPayException("订单查询接口中，out_trade_no必填！");
        }
        $inputObj->appid = APPID;//公众账号ID
        $inputObj->mch_id = MCHID;//商户号
        $inputObj->nonce_str = WxPayData::makeNonceStr();//随机字符串

        $inputObj->sign = $inputObj->makeSign();//签名
        $xml = $inputObj->toXml();

        $startTimeStamp = self::getMillisecond();//请求开始时间
        $response = self::postXmlCurl($xml, $url, false, $timeOut);
        $result = WxPayData::resultInit($response);
        self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间

        return $result;
    }

    /**
     * 申请退款，WxPayRefund中out_trade_no、transaction_id至少填一个且
     * out_refund_no、total_fee、refund_fee、op_user_id为必填参数
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     *
     * @param WxPayRefund $inputObj
     * @param int $timeOut
     *
     * @throws WxPayException
     * @return 成功时返回，其他抛异常
     */
    public static function refund($inputObj, $timeOut = 6)
    {
        $url = "https://api.mch.weixin.qq.com/secapi/pay/refund";
        //检测必填参数
        if (!$inputObj->out_trade_no && !$inputObj->transaction_id) {
            throw new WxPayException("退款申请接口中，out_trade_no、transaction_id至少填一个！");
        } else if (!$inputObj->out_refund_no) {
            throw new WxPayException("退款申请接口中，缺少必填参数out_refund_no！");
        } else if (!$inputObj->total_fee) {
            throw new WxPayException("退款申请接口中，缺少必填参数total_fee！");
        } else if (!$inputObj->refund_fee) {
            throw new WxPayException("退款申请接口中，缺少必填参数refund_fee！");
        } else if (!$inputObj->op_user_id) {
            throw new WxPayException("退款申请接口中，缺少必填参数op_user_id！");
        }
        $inputObj->appid = APPID;//公众账号ID
        $inputObj->mch_id = MCHID;//商户号
        $inputObj->nonce_str = WxPayData::makeNonceStr();//随机字符串

        $inputObj->sign = $inputObj->makeSign();//签名
        $xml = $inputObj->toXml();
        $startTimeStamp = self::getMillisecond();//请求开始时间
        $response = self::postXmlCurl($xml, $url, true, $timeOut);
        $result = WxPayData::resultInit($response);

        if ($result['result_code'] == 'SUCCESS') {  // 退款申请接收成功
            C::t('#singcere_wechat#singcere_wechat_pay')->update_by_tradeno($result['out_trade_no'], array('refund_fee' => $result['refund_fee']), true);
        }
        self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间
        return $result;
    }

    /**
     * 查询退款
     * 提交退款申请后，通过调用该接口查询退款状态。退款有一定延时，
     * 用零钱支付的退款20分钟内到账，银行卡支付的退款3个工作日后重新查询退款状态。
     * WxPayRefundQuery中out_refund_no、out_trade_no、transaction_id、refund_id四个参数必填一个
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     *
     * @param WxPayRefundQuery $inputObj
     * @param int $timeOut
     *
     * @throws WxPayException
     * @return 成功时返回，其他抛异常
     */
    public static function refundQuery($inputObj, $timeOut = 6)
    {
        $url = "https://api.mch.weixin.qq.com/pay/refundquery";
        //检测必填参数
        if (!$inputObj->out_refund_no &&
            !$inputObj->out_trade_no &&
            !$inputObj->transaction_id &&
            !$inputObj->refund_id
        ) {
            throw new WxPayException("退款查询接口中，out_refund_no、out_trade_no、transaction_id、refund_id四个参数必填一个！");
        }
        $inputObj->appid = APPID;//公众账号ID
        $inputObj->mch_id = MCHID;//商户号
        $inputObj->nonce_str = WxPayData::makeNonceStr();//随机字符串

        $inputObj->sign = $inputObj->makeSign();//签名
        $xml = $inputObj->toXml();

        $startTimeStamp = self::getMillisecond();//请求开始时间
        $response = self::postXmlCurl($xml, $url, false, $timeOut);
        $result = WxPayData::resultInit($response);
        self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间

        return $result;
    }

    /**
     * 下载对账单，WxPayDownloadBill中bill_date为必填参数
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     *
     * @param WxPayDownloadBill $inputObj
     * @param int $timeOut
     *
     * @throws WxPayException
     * @return 成功时返回，其他抛异常
     */
    public static function downloadBill($inputObj, $timeOut = 6)
    {
        $url = "https://api.mch.weixin.qq.com/pay/downloadbill";
        //检测必填参数
        if (!$inputObj->bill_date) {
            throw new WxPayException("对账单接口中，缺少必填参数bill_date！");
        }
        $inputObj->appid = APPID;//公众账号ID
        $inputObj->mch_id = MCHID;//商户号
        $inputObj->nonce_str = WxPayData::makeNonceStr();//随机字符串

        $inputObj->sign = $inputObj->makeSign();//签名
        $xml = $inputObj->toXml();

        $response = self::postXmlCurl($xml, $url, false, $timeOut);
        if (substr($response, 0, 5) == "<xml>") {
            return "";
        }
        return $response;
    }

    /**
     * 提交被扫支付API
     * 收银员使用扫码设备读取微信用户刷卡授权码以后，二维码或条码信息传送至商户收银台，
     * 由商户收银台或者商户后台调用该接口发起支付。
     * WxPayWxPayMicroPay中body、out_trade_no、total_fee、auth_code参数必填
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     *
     * @param WxPayWxPayMicroPay $inputObj
     * @param int $timeOut
     */
    public static function micropay($inputObj, $timeOut = 10)
    {
        $url = "https://api.mch.weixin.qq.com/pay/micropay";
        //检测必填参数
        if (!$inputObj->body) {
            throw new WxPayException("提交被扫支付API接口中，缺少必填参数body！");
        } else if (!$inputObj->out_trade_no) {
            throw new WxPayException("提交被扫支付API接口中，缺少必填参数out_trade_no！");
        } else if (!$inputObj->total_fee) {
            throw new WxPayException("提交被扫支付API接口中，缺少必填参数total_fee！");
        } else if (!$inputObj->auth_code) {
            throw new WxPayException("提交被扫支付API接口中，缺少必填参数auth_code！");
        }

        $inputObj->spbill_create_ip = $_SERVER['REMOTE_ADDR'];//终端ip
        $inputObj->appid = APPID;//公众账号ID
        $inputObj->mch_id = MCHID;//商户号
        $inputObj->nonce_str = WxPayData::makeNonceStr();//随机字符串

        $inputObj->sign = $inputObj->makeSign();//签名
        $xml = $inputObj->toXml();

        $startTimeStamp = self::getMillisecond();//请求开始时间
        $response = self::postXmlCurl($xml, $url, false, $timeOut);
        $result = WxPayData::resultInit($response);
        self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间

        return $result;
    }

    /**
     * 撤销订单API接口，WxPayReverse中参数out_trade_no和transaction_id必须填写一个
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     *
     * @param WxPayReverse $inputObj
     * @param int $timeOut
     *
     * @throws WxPayException
     */
    public static function reverse($inputObj, $timeOut = 6)
    {
        $url = "https://api.mch.weixin.qq.com/secapi/pay/reverse";
        //检测必填参数
        if (!$inputObj->out_trade_no && !$inputObj->transaction_id) {
            throw new WxPayException("撤销订单API接口中，参数out_trade_no和transaction_id必须填写一个！");
        }

        $inputObj->appid = APPID;//公众账号ID
        $inputObj->mch_id = MCHID;//商户号
        $inputObj->nonce_str = WxPayData::makeNonceStr();//随机字符串

        $inputObj->sign = $inputObj->makeSign();//签名
        $xml = $inputObj->toXml();

        $startTimeStamp = self::getMillisecond();//请求开始时间
        $response = self::postXmlCurl($xml, $url, true, $timeOut);
        $result = WxPayData::resultInit($response);
        self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间

        return $result;
    }

    /**
     * 测速上报，该方法内部封装在report中，使用时请注意异常流程
     * WxPayReport中interface_url、return_code、result_code、user_ip、execute_time_必填
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     *
     * @param WxPayReport $inputObj
     * @param int $timeOut
     *
     * @throws WxPayException
     * @return 成功时返回，其他抛异常
     */
    public static function report($inputObj, $timeOut = 1)
    {
        $url = "https://api.mch.weixin.qq.com/payitil/report";
        //检测必填参数
        if (!$inputObj->interface_url) {
            throw new WxPayException("接口URL，缺少必填参数interface_url！");
        }
        if (!$inputObj->return_code) {
            throw new WxPayException("返回状态码，缺少必填参数return_code！");
        }
        if (!$inputObj->result_code) {
            throw new WxPayException("业务结果，缺少必填参数result_code！");
        }
        if (!$inputObj->user_ip) {
            throw new WxPayException("访问接口IP，缺少必填参数user_ip！");
        }
        if (!$inputObj->execute_time_) {
            throw new WxPayException("接口耗时，缺少必填参数execute_time_！");
        }

        $inputObj->appid = APPID;//公众账号ID
        $inputObj->mch_id = MCHID;//商户号
        $inputObj->user_ip = $_SERVER['REMOTE_ADDR'];//终端ip
        $inputObj->time = date("YmdHis");//商户上报时间
        $inputObj->nonce_str = WxPayData::makeNonceStr();//随机字符串

        $inputObj->sign = $inputObj->makeSign();//签名
        $xml = $inputObj->toXml();

        $startTimeStamp = self::getMillisecond();//请求开始时间
        $response = self::postXmlCurl($xml, $url, false, $timeOut);
        return $response;
    }

    /**
     * 生成二维码规则,模式一生成支付二维码
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     *
     * @param WxPayBizPayUrl $inputObj
     * @param int $timeOut
     *
     * @throws WxPayException
     * @return 成功时返回，其他抛异常
     */
    public static function bizpayurl($inputObj, $timeOut = 6)
    {
        if (!$inputObj->product_id) {
            throw new WxPayException("生成二维码，缺少必填参数product_id！");
        }

        $inputObj->appid = APPID;//公众账号ID
        $inputObj->mch_id = MCHID;//商户号
        $inputObj->time_stamp = time();//时间戳
        $inputObj->nonce_str = WxPayData::makeNonceStr();//随机字符串

        $inputObj->sign = $inputObj->makeSign();//签名

        return $inputObj->getValues();
    }

    /**
     * 转换短链接
     * 该接口主要用于扫码原生支付模式一中的二维码链接转成短链接(weixin://wxpay/s/XXXXXX)，
     * 减小二维码数据量，提升扫描速度和精确度。
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     *
     * @param WxPayShortUrl $inputObj
     * @param int $timeOut
     *
     * @throws WxPayException
     * @return 成功时返回，其他抛异常
     */
    public static function shorturl($inputObj, $timeOut = 6)
    {
        $url = "https://api.mch.weixin.qq.com/tools/shorturl";
        //检测必填参数
        if (!$inputObj->long_url) {
            throw new WxPayException("需要转换的URL，签名用原串，传输需URL encode！");
        }
        $inputObj->appid = APPID;//公众账号ID
        $inputObj->mch_id = MCHID;//商户号
        $inputObj->nonce_str = WxPayData::makeNonceStr();//随机字符串

        $inputObj->sign = $inputObj->makeSign();//签名
        $xml = $inputObj->toXml();

        $startTimeStamp = self::getMillisecond();//请求开始时间
        $response = self::postXmlCurl($xml, $url, false, $timeOut);
        $result = WxPayData::resultInit($response);
        self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间

        return $result;
    }

    /**
     * 支付结果通用通知
     *
     * @param function $callback
     * 直接回调函数使用方法: notify(you_function);
     * 回调类成员函数方法:notify(array($this, you_function));
     * $callback  原型为：function function_name($data){}
     */
    public static function notify($callback, &$msg, $back = false)
    {
        //获取通知的数据
        $xml = file_get_contents("php://input");
        //如果返回成功则验证签名
        try {
            $result = WxPayData::resultInit($xml);
        } catch (WxPayException $e) {
            $msg = $e->errorMessage();
            return false;
        }

        if ($result['result_code'] == 'SUCCESS') {
            // update_for_notify 仅更新交易号不存在的记录
            C::t('#singcere_wechat#singcere_wechat_pay')->update_for_notify($result['out_trade_no'], array(
                'err_code'       => $result['err_code'],
                'transaction_id' => $result['transaction_id'],
            ), true);
        }

        if ($back) {
            $backXml = new WxPayData();
            $backXml->return_code = 'SUCCESS';
            $backXml->return_msg = 'OK';
            echo $backXml->toXml();
        }

        return call_user_func($callback, $result);
    }

    /**
     * 产生随机字符串，不长于32位
     *
     * @param int $length
     *
     * @return 产生的随机字符串
     */
    public static function getNonceStr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 直接输出xml
     *
     * @param string $xml
     */
    public static function replyNotify($xml)
    {
        echo $xml;
    }

    /**
     * 上报数据， 上报的时候将屏蔽所有异常流程
     *
     * @param string $usrl
     * @param int $startTimeStamp
     * @param array $data
     */
    private static function reportCostTime($url, $startTimeStamp, $data)
    {
        //如果不需要上报数据
        if (REPORT_LEVENL == 0) {
            return;
        }
        //如果仅失败上报
        if (REPORT_LEVENL == 1 &&
            array_key_exists("return_code", $data) &&
            $data["return_code"] == "SUCCESS" &&
            array_key_exists("result_code", $data) &&
            $data["result_code"] == "SUCCESS"
        ) {
            return;
        }

        //上报逻辑
        $endTimeStamp = self::getMillisecond();
        $objInput = new WxPayData();
        $objInput->interface_url = $url;
        $objInput->execute_time_ = $endTimeStamp - $startTimeStamp;
        //返回状态码
        if (array_key_exists("return_code", $data)) {
            $objInput->return_code = $data["return_code"];
        }
        //返回信息
        if (array_key_exists("return_msg", $data)) {
            $objInput->return_msg = $data["return_msg"];
        }
        //业务结果
        if (array_key_exists("result_code", $data)) {
            $objInput->result_code = $data["result_code"];
        }
        //错误代码
        if (array_key_exists("err_code", $data)) {
            $objInput->err_code = $data["err_code"];
        }
        //错误代码描述
        if (array_key_exists("err_code_des", $data)) {
            $objInput->err_code_des = $data["err_code_des"];
        }
        //商户订单号
        if (array_key_exists("out_trade_no", $data)) {
            $objInput->out_trade_no = $data["out_trade_no"];
        }
        //设备号
        if (array_key_exists("device_info", $data)) {
            $objInput->device_info = $data["device_info"];
        }

        try {
            self::report($objInput);
        } catch (WxPayException $e) {
            //不做任何处理
        }
    }

    /**
     * 以post方式提交xml到对应的接口url
     *
     * @param string $xml   需要post的xml数据
     * @param string $url   url
     * @param bool $useCert 是否需要证书，默认不需要
     * @param int $second   url执行超时时间，默认30s
     *
     * @throws WxPayException
     */
    private static function postXmlCurl($xml, $url, $useCert = false, $second = 45)
    {
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);

        //如果有配置代理这里就设置代理
        if (CURL_PROXY_HOST != "0.0.0.0"
            && CURL_PROXY_PORT != 0
        ) {
            curl_setopt($ch, CURLOPT_PROXY, CURL_PROXY_HOST);
            curl_setopt($ch, CURLOPT_PROXYPORT, CURL_PROXY_PORT);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);//严格校验
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, false);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ($useCert == true) {
            //设置证书
            //使用证书：cert 与 key 分别属于两个.pem文件
            curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLCERT, SSLCERT_PATH);
            curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLKEY, SSLKEY_PATH);
        }
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            throw new WxPayException("curl出错，错误码 $error");
        }
    }

    /**
     * 获取毫秒级别的时间戳
     */
    private static function getMillisecond()
    {
        //获取毫秒的时间戳
        $time = explode(" ", microtime());
        $time = $time[1] . ($time[0] * 1000);
        $time2 = explode(".", $time);
        $time = $time2[0];
        return $time;
    }
}


class WxPayData implements ArrayAccess
{
    protected $values = array();


    public function offsetExists($offset)
    {
        return isset($this->values[$offset]);
    }


    public function offsetGet($offset)
    {
        return $this->values[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->values[$offset] = $value;
    }


    public function offsetUnset($offset)
    {
        unset($this->values[$offset]);
    }


    public function __get($name)
    {
        return $this->values[$name];
    }

    public function __set($name, $value)
    {
        $this->values[$name] = $value;
    }

    public function __isset($name)
    {
        return array_key_exists($name, $this->values);
    }

    public function __unset($name)
    {
        unset($this->values[$name]);
    }

    public function __call($name, $arguments)
    {
        throw new WxPayException("方法不存在!");
    }

    /**
     * 获取设置的值
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * 设置value
     */
    public function setValues($values)
    {
        $this->values = $values;
    }

    /**
     * 输出xml字符
     * @throws WxPayException
     **/
    public function toXml()
    {
        if (!is_array($this->values)
            || count($this->values) <= 0
        ) {
            throw new WxPayException("数组数据异常！");
        }

        $xml = "<xml>";
        foreach ($this->values as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

    /**
     * 将xml转为array
     *
     * @param string $xml
     *
     * @throws WxPayException
     */
    public function fromXml($xml)
    {
        if (!$xml) {
            throw new WxPayException("xml数据异常！");
        }
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $this->values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $this->values;
    }

    public static function resultInit($xml, $checkSign = true)
    {
        $data = new self();
        $data->fromXml($xml);
        if ($data->values['return_code'] != 'SUCCESS') {
            return $data->getValues();
        }

        $checkSign && $data->checkSign();
        return $data->getValues();
    }

    /**
     * 生成签名
     * @return 签名，本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
     */
    public function makeSign()
    {
        //签名步骤一：按字典序排序参数
        ksort($this->values);
        $string = $this->toUrlParams();
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=" . KEY;
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }

    public function checkSign()
    {
        if (!$this->sign) {
            throw new WxPayException("签名缺失！");
        }

        $sign = $this->makeSign();
        if ($this->sign == $sign) {
            return true;
        }
        throw new WxPayException("签名错误！");
    }

    public static function makeNonceStr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 格式化参数格式化成url参数
     */
    public function toUrlParams()
    {
        $buff = "";
        foreach ($this->values as $k => $v) {
            if ($k != 'sign' && $v != "" && !is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }
}

class WxPayException extends Exception
{
    public function errorMessage()
    {
        return $this->getMessage();
    }
}