<?php
/**
 *      (C)2007-2012 singcere.net
 *      This is a freeware, But you have no right to modify or distribute
 *      $Id$
 */

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

define('IN_WXPAY', true);

/*
|--------------------------------------------------------------------------
| 从singcere_wechat中加载配置参数
|--------------------------------------------------------------------------
*/
global $_G;

if (!isset($_G['singcere_wechat'])) {
    $_G['singcere_wechat']['setting'] = unserialize($_G['setting']['singcere_wechat']);
    $_G['singcere_wechat']['setting']['wechat_appsecret'] = authcode($_G['singcere_wechat']['setting']['wechat_appsecret'], 'DECODE', $_G['config']['security']['authkey']);
    $_G['singcere_wechat']['setting']['wechat_mchkey'] = authcode($_G['singcere_wechat']['setting']['wechat_mchkey'], 'DECODE', $_G['config']['security']['authkey']);
}

define('APPID', $_G['singcere_wechat']['setting']['wechat_appId']);
define('APPSECRET', $_G['singcere_wechat']['setting']['wechat_appsecret']);
define('MCHID', $_G['singcere_wechat']['setting']['wechat_mchid']);
define('KEY', $_G['singcere_wechat']['setting']['wechat_mchkey']);
define('SSLCERT_PATH', dirname(__FILE__) . '/../include/pay/cacert/apiclient_cert.pem');
define('SSLKEY_PATH', dirname(__FILE__) . '/../include/pay/cacert/apiclient_key.pem');
define('CURL_TIMEOUT', 30);


/**
 * TODO：这里设置代理机器，只有需要代理的时候才设置，不需要代理，请设置为0.0.0.0和0
 * 本例程通过curl使用HTTP POST方法，此处可修改代理服务器，
 * 默认CURL_PROXY_HOST=0.0.0.0和CURL_PROXY_PORT=0，此时不开启代理（如有需要才设置）
 * @var unknown_type
 */
define('CURL_PROXY_HOST', '0.0.0.0');
define('CURL_PROXY_PORT', 0);

/**
 * TODO：接口调用上报等级，默认紧错误上报（注意：上报超时间为【1s】，上报无论成败【永不抛出异常】，
 * 不会影响接口调用流程），开启上报之后，方便微信监控请求调用的质量，建议至少
 * 开启错误上报。
 * 上报等级，0.关闭上报; 1.仅错误出错上报; 2.全量上报
 * @var int
 */
define('REPORT_LEVENL', 1);

/*
|--------------------------------------------------------------------------
| 多种支付方式的实现类
|--------------------------------------------------------------------------
*/
require_once DISCUZ_ROOT . "source/plugin/singcere_wechat/include/pay/wxpay.api.php";

/**
 * JSAPI支付实现类
 * 该类实现了从微信公众平台获取code、通过code获取openid和access_token、
 * 生成jsapi支付js接口所需的参数、生成获取共享收货地址所需的参数
 * 该类是微信支付提供的样例程序，商户可根据自己的需求修改，或者使用lib中的api自行开发
 * @author widy
 */
class JsApiPay
{
    /**
     * 网页授权接口微信服务器返回的数据，返回样例如下
     * {
     *  "access_token":"ACCESS_TOKEN",
     *  "expires_in":7200,
     *  "refresh_token":"REFRESH_TOKEN",
     *  "openid":"OPENID",
     *  "scope":"SCOPE",
     *  "unionid": "o6_bmasdasdsad6_2sgVt7hMZOPfL"
     * }
     * 其中access_token可用于获取共享收货地址
     * openid是微信支付jsapi支付接口必须的参数
     * @var array
     */
    public $data = null;

    /**
     * 通过跳转获取用户的openid，跳转流程如下：
     * 1、设置自己需要调回的url及其其他参数，跳转到微信服务器https://open.weixin.qq.com/connect/oauth2/authorize
     * 2、微信服务处理完成之后会跳转回用户redirect_uri地址，此时会带上一些参数，如：code
     * @return 用户的openid
     */
    public function getOpenid()
    {
        //通过code获得openid
        if (!isset($_GET['code'])) {
            //触发微信返回code码
            $baseUrl = urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . $_SERVER['QUERY_STRING']);
            $url = $this->__createOauthUrlForCode($baseUrl);
            Header("Location: $url");
            exit();
        } else {
            //获取code码，以获取openid
            $code = $_GET['code'];
            $openid = $this->getOpenidFromMp($code);
            return $openid;
        }
    }

    /**
     * 获取jsapi支付的参数
     *
     * @param array $UnifiedOrderResult 统一支付接口返回的数据
     *
     * @throws WxPayException
     * @return json数据，可直接填入js函数作为参数
     */
    public function getJsApiParameters($UnifiedOrderResult)
    {
        if (!array_key_exists("appid", $UnifiedOrderResult)
            || !array_key_exists("prepay_id", $UnifiedOrderResult)
            || $UnifiedOrderResult['prepay_id'] == ""
        ) {
            throw new WxPayException("参数错误");
        }
        $jsapi = new WxPayData();
        $jsapi->appId = $UnifiedOrderResult["appid"];
        $timeStamp = time();
        $jsapi->timeStamp = "$timeStamp";
        $jsapi->nonceStr = WxPayApi::getNonceStr();
        $jsapi->package = "prepay_id=" . $UnifiedOrderResult['prepay_id'];
        $jsapi->signType = "MD5";
        $jsapi->paySign = $jsapi->MakeSign();
        $parameters = json_encode($jsapi->GetValues());
        return $parameters;
    }

    /**
     * 通过code从工作平台获取openid机器access_token
     *
     * @param string $code 微信跳转回来带上的code
     *
     * @return openid
     */
    public function getOpenidFromMp($code)
    {
        $url = $this->__createOauthUrlForOpenid($code);
        //初始化curl
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->curl_timeout);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if (CURL_PROXY_HOST != "0.0.0.0"
            && CURL_PROXY_PORT != 0
        ) {
            curl_setopt($ch, CURLOPT_PROXY, CURL_PROXY_HOST);
            curl_setopt($ch, CURLOPT_PROXYPORT, CURL_PROXY_PORT);
        }
        //运行curl，结果以jason形式返回
        $res = curl_exec($ch);
        curl_close($ch);
        //取出openid
        $data = json_decode($res, true);
        $this->data = $data;
        $openid = $data['openid'];
        return $openid;
    }

    /**
     * 拼接签名字符串
     *
     * @param array $urlObj
     *
     * @return 返回已经拼接好的字符串
     */
    private function toUrlParams($urlObj)
    {
        $buff = "";
        foreach ($urlObj as $k => $v) {
            if ($k != "sign") {
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     * 获取地址js参数
     * @return 获取共享收货地址js函数需要的参数，json格式可以直接做参数使用
     */
    public function getEditAddressParameters()
    {
        $getData = $this->data;
        $data = array();
        $data["appid"] = APPID;
        $data["url"] = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $time = time();
        $data["timestamp"] = "$time";
        $data["noncestr"] = "1234568";
        $data["accesstoken"] = $getData["access_token"];
        ksort($data);
        $params = $this->toUrlParams($data);
        $addrSign = sha1($params);

        $afterData = array(
            "addrSign"  => $addrSign,
            "signType"  => "sha1",
            "scope"     => "jsapi_address",
            "appId"     => APPID,
            "timeStamp" => $data["timestamp"],
            "nonceStr"  => $data["noncestr"]
        );
        $parameters = json_encode($afterData);
        return $parameters;
    }

    /**
     * 构造获取code的url连接
     *
     * @param string $redirectUrl 微信服务器回跳的url，需要url编码
     *
     * @return 返回构造好的url
     */
    private function __createOauthUrlForCode($redirectUrl)
    {
        $urlObj["appid"] = APPID;
        $urlObj["redirect_uri"] = "$redirectUrl";
        $urlObj["response_type"] = "code";
        $urlObj["scope"] = "snsapi_base";
        $urlObj["state"] = "STATE" . "#wechat_redirect";
        $bizString = $this->toUrlParams($urlObj);
        return "https://open.weixin.qq.com/connect/oauth2/authorize?" . $bizString;
    }

    /**
     * 构造获取open和access_toke的url地址
     *
     * @param string $code ，微信跳转带回的code
     *
     * @return 请求的url
     */
    private function __createOauthUrlForOpenid($code)
    {
        $urlObj["appid"] = APPID;
        $urlObj["secret"] = APPSECRET;
        $urlObj["code"] = $code;
        $urlObj["grant_type"] = "authorization_code";
        $bizString = $this->toUrlParams($urlObj);
        return "https://api.weixin.qq.com/sns/oauth2/access_token?" . $bizString;
    }
}

/**
 * 扫码支付实现类
 * @author widyhu
 */
class NativePay
{
    /**
     * 生成直接支付url，支付url有效期为2小时,模式二
     *
     * @param UnifiedOrderInput $input
     */
    public function getPayUrl($input)
    {
        if ($input->trade_type == "NATIVE") {
            $result = WxPayApi::unifiedOrder($input, $fromType, $fromId);
            return $result;
        }
    }

    /**
     * 生成扫描支付URL,模式一
     *
     * @param BizPayUrlInput $bizUrlInfo
     */
    public function getPrePayUrl($productId)
    {
        $biz = new WxPayData();
        $biz->product_id = $productId;
        $values = WxpayApi::bizpayurl($biz);
        $url = "weixin://wxpay/bizpayurl?" . $this->toUrlParams($values);
        return $url;
    }

    /**
     * 参数数组转换为url参数
     *
     * @param array $urlObj
     */
    private function toUrlParams($urlObj)
    {
        $buff = "";
        foreach ($urlObj as $k => $v) {
            $buff .= $k . "=" . $v . "&";
        }

        $buff = trim($buff, "&");
        return $buff;
    }
}

/**
 * 刷卡支付实现类
 * 该类实现了一个刷卡支付的流程，流程如下：
 * 1、提交刷卡支付
 * 2、根据返回结果决定是否需要查询订单，如果查询之后订单还未变则需要返回查询（一般反复查10次）
 * 3、如果反复查询10订单依然不变，则发起撤销订单
 * 4、撤销订单需要循环撤销，一直撤销成功为止（注意循环次数，建议10次）
 * 该类是微信支付提供的样例程序，商户可根据自己的需求修改，或者使用lib中的api自行开发，为了防止
 * 查询时hold住后台php进程，商户查询和撤销逻辑可在前端调用
 * @author widy
 */
class MicroPay
{
    /**
     * 提交刷卡支付，并且确认结果，接口比较慢
     *
     * @param WxPayMicroPay $microPayInput
     *
     * @throws WxpayException
     * @return 返回查询接口的结果
     */
    public function pay($microPayInput)
    {
        //①、提交被扫支付
        $result = WxPayApi::micropay($microPayInput, 5);
        //如果返回成功
        if (!array_key_exists("return_code", $result)
            || !array_key_exists("out_trade_no", $result)
            || !array_key_exists("result_code", $result)
        ) {
            echo "接口调用失败,请确认是否输入是否有误！";
            throw new WxPayException("接口调用失败！");
        }

        //签名验证
        $out_trade_no = $microPayInput->out_trade_no;

        //②、接口调用成功，明确返回调用失败
        if ($result["return_code"] == "SUCCESS" &&
            $result["result_code"] == "FAIL" &&
            $result["err_code"] != "USERPAYING" &&
            $result["err_code"] != "SYSTEMERROR"
        ) {
            return false;
        }

        //③、确认支付是否成功
        $queryTimes = 10;
        while ($queryTimes > 0) {
            $succResult = 0;
            $queryResult = $this->query($out_trade_no, $succResult);
            //如果需要等待1s后继续
            if ($succResult == 2) {
                sleep(2);
                continue;
            } else if ($succResult == 1) {//查询成功
                return $queryResult;
            } else {//订单交易失败
                return false;
            }
        }

        //④、次确认失败，则撤销订单
        if (!$this->cancel($out_trade_no)) {
            throw new WxpayException("撤销单失败！");
        }

        return false;
    }

    /**
     * 查询订单情况
     *
     * @param string $out_trade_no 商户订单号
     * @param int $succCode        查询订单结果
     *
     * @return 0 订单不成功，1表示订单成功，2表示继续等待
     */
    public function query($out_trade_no, &$succCode)
    {
        $queryOrderInput = new WxPayData();
        $queryOrderInput->out_trade_no = $out_trade_no;
        $result = WxPayApi::orderQuery($queryOrderInput);

        if ($result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS"
        ) {
            //支付成功
            if ($result["trade_state"] == "SUCCESS") {
                $succCode = 1;
                return $result;
            } //用户支付中
            else if ($result["trade_state"] == "USERPAYING") {
                $succCode = 2;
                return false;
            }
        }

        //如果返回错误码为“此交易订单号不存在”则直接认定失败
        if ($result["err_code"] == "ORDERNOTEXIST") {
            $succCode = 0;
        } else {
            //如果是系统错误，则后续继续
            $succCode = 2;
        }
        return false;
    }

    /**
     * 撤销订单，如果失败会重复调用10次
     *
     * @param string $out_trade_no
     * @param 调用深度 $depth
     */
    public function cancel($out_trade_no, $depth = 0)
    {
        if ($depth > 10) {
            return false;
        }

        $clostOrder = new WxPayData();
        $clostOrder->out_trade_no = $out_trade_no;
        $result = WxPayApi::reverse($clostOrder);

        //接口调用失败
        if ($result["return_code"] != "SUCCESS") {
            return false;
        }

        //如果结果为success且不需要重新调用撤销，则表示撤销成功
        if ($result["result_code"] != "SUCCESS"
            && $result["recall"] == "N"
        ) {
            return true;
        } else if ($result["recall"] == "Y") {
            return $this->cancel($out_trade_no, ++$depth);
        }
        return false;
    }
}