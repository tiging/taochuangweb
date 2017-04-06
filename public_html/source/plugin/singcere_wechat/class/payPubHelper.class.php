<?php

/**
 *      (C)2007-2012 singcere.net
 *      This is a freeware, But you have no right to modify or distribute
 *
 *      $Id$
 */

global $_G;
define('APPID', $_G['singcere_wechat']['setting']['wechat_appId']);
define('APPSECRET', $_G['singcere_wechat']['setting']['wechat_appsecret']);
define('MCHID', $_G['singcere_wechat']['setting']['wechat_mchid']);
define('KEY', $_G['singcere_wechat']['setting']['wechat_mchkey']);
define('SSLCERT_PATH', DISCUZ_ROOT . '/source/plugin/singcere_wechat/include/pay/cacert/apiclient_cert.pem');
define('SSLKEY_PATH', DISCUZ_ROOT . '/source/plugin/singcere_wechat/include/pay/cacert/apiclient_key.pem');
define('CURL_TIMEOUT', 30);

class SDKRuntimeException extends Exception {
    public function errorMessage() {
        return $this->getMessage();
    }
}

/**
 * ���нӿڵĻ���
 */
class Common_util_pub {

    function trimString($value) {
        $ret = null;
        if (null != $value) {
            $ret = $value;
            if (strlen($ret) == 0) {
                $ret = null;
            }
        }
        return $ret;
    }

    /**
     * ���ã���������ַ�����������32λ
     */
    public function createNoncestr($length = 32) {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i ++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * ���ã���ʽ��������ǩ��������Ҫʹ��
     */
    function formatBizQueryParaMap($paraMap, $urlencode) {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v) {
            if ($urlencode) {
                $v = urlencode($v);
            }
            // $buff .= strtolower($k) . "=" . $v . "&";
            $buff .= $k . "=" . $v . "&";
        }
        $reqPar;
        if (strlen($buff) > 0) {
            $reqPar = substr($buff, 0, strlen($buff) - 1);
        }
        return $reqPar;
    }

    /**
     * ���ã�����ǩ��
     */
    public function getSign($Obj) {
        global $_G;
        foreach ($Obj as $k => $v) {
            $Parameters[$k] = $v;
        }
        // ǩ������һ�����ֵ����������
        ksort($Parameters);
        $String = $this->formatBizQueryParaMap($Parameters, false);
        // echo '��string1��'.$String.'</br>';
        // ǩ�����������string�����KEY
        $String = $String . "&key=" . KEY;
        // echo "��string2��".$String."</br>";
        // ǩ����������MD5����
        $String = md5($String);
        // echo "��string3�� ".$String."</br>";
        // ǩ�������ģ������ַ�תΪ��д
        $result_ = strtoupper($String);
        // echo "��result�� ".$result_."</br>";
        return $result_;
    }

    /**
     * ���ã�arrayתxml
     */
    function arrayToXml($arr) {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
        }
        $xml .= "</xml>";
        return $xml;
    }

    /**
     * ���ã���xmlתΪarray
     */
    public function xmlToArray($xml) {
        // ��XMLתΪarray
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $array_data;
    }

    /**
     * ���ã���post��ʽ�ύxml����Ӧ�Ľӿ�url
     */
    public function postXmlCurl($xml, $url, $second = 30) {
        // ��ʼ��curl
        $ch = curl_init();
        // ���ó�ʱ
        curl_setopt($ch, CURLOP_TIMEOUT, $second);
        // �������ô�������еĻ�
        // curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
        // curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        // ����header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        // Ҫ����Ϊ�ַ������������Ļ��
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        // post�ύ��ʽ
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        // ����curl
        $data = curl_exec($ch);
        curl_close($ch);
        // ���ؽ��
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            echo "curl����������:$error" . "<br>";
            echo "<a href='http://curl.haxx.se/libcurl/c/libcurl-errors.html'>����ԭ���ѯ</a></br>";
            curl_close($ch);
            return false;
        }
    }

    /**
     * ���ã�ʹ��֤�飬��post��ʽ�ύxml����Ӧ�Ľӿ�url
     */
    function postXmlSSLCurl($xml, $url, $second = 30) {
        global $_conf;
        $ch = curl_init();
        // ��ʱʱ��
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        // �������ô�������еĻ�
        // curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
        // curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        // ����header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        // Ҫ����Ϊ�ַ������������Ļ��
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        // ����֤��
        // ʹ��֤�飺cert �� key �ֱ���������.pem�ļ�
        // Ĭ�ϸ�ʽΪPEM������ע��
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLCERT, SSLCERT_PATH);
        // Ĭ�ϸ�ʽΪPEM������ע��
        curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLKEY, SSLKEY_PATH);
        // post�ύ��ʽ
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        $data = curl_exec($ch);
        // ���ؽ��
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            echo "curl����������:$error" . "<br>";
            echo "<a href='http://curl.haxx.se/libcurl/c/libcurl-errors.html'>����ԭ���ѯ</a></br>";
            curl_close($ch);
            return false;
        }
    }

    /**
     * ���ã���ӡ����
     */
    function printErr($wording = '', $err = '') {
        print_r('<pre>');
        echo $wording . "</br>";
        var_dump($err);
        print_r('</pre>');
    }
}

/**
 * �����ͽӿڵĻ���
 */
class Wxpay_client_pub extends Common_util_pub {

    var $parameters;
 // �������������Ϊ��������
    public $response;
 // ΢�ŷ��ص���Ӧ
    public $result;
 // ���ز���������Ϊ��������
    var $url;
 // �ӿ�����
    var $curl_timeout;
 // curl��ʱʱ��
    
    /**
     * ���ã������������
     */
    function setParameter($parameter, $parameterValue) {
        $this->parameters[$this->trimString($parameter)] = $this->trimString($parameterValue);
    }

    /**
     * ���ã����ñ�����������������ǩ�������ɽӿڲ���xml
     */
    function createXml() {
        $this->parameters["appid"] = APPID; // �����˺�ID
        $this->parameters["mch_id"] = MCHID; // �̻���
        $this->parameters["nonce_str"] = $this->createNoncestr(); // ����ַ���
        $this->parameters["sign"] = $this->getSign($this->parameters); // ǩ��
        return $this->arrayToXml($this->parameters);
    }

    /**
     * ���ã�post����xml
     */
    function postXml() {
        $xml = $this->createXml();
        $this->response = $this->postXmlCurl($xml, $this->url, $this->curl_timeout);
        return $this->response;
    }

    /**
     * ���ã�ʹ��֤��post����xml
     */
    function postXmlSSL() {
        $xml = $this->createXml();
        $this->response = $this->postXmlSSLCurl($xml, $this->url, $this->curl_timeout);
        return $this->response;
    }

    /**
     * ���ã���ȡ�����Ĭ�ϲ�ʹ��֤��
     */
    function getResult() {
        $this->postXml();
        $this->result = $this->xmlToArray($this->response);
        return $this->result;
    }
}

/**
 * ͳһ֧���ӿ���
 */
class UnifiedOrder_pub extends Wxpay_client_pub {

    function __construct() {
        // ���ýӿ�����
        $this->url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
        // ����curl��ʱʱ��
        $this->curl_timeout = CURL_TIMEOUT;
    }

    /**
     * ���ɽӿڲ���xml
     */
    function createXml() {
        try {
            // ���������
            if ($this->parameters["out_trade_no"] == null) {
                throw new SDKRuntimeException("ȱ��ͳһ֧���ӿڱ������out_trade_no��" . "<br>");
            } elseif ($this->parameters["body"] == null) {
                throw new SDKRuntimeException("ȱ��ͳһ֧���ӿڱ������body��" . "<br>");
            } elseif ($this->parameters["total_fee"] == null) {
                throw new SDKRuntimeException("ȱ��ͳһ֧���ӿڱ������total_fee��" . "<br>");
            } elseif ($this->parameters["notify_url"] == null) {
                throw new SDKRuntimeException("ȱ��ͳһ֧���ӿڱ������notify_url��" . "<br>");
            } elseif ($this->parameters["trade_type"] == null) {
                throw new SDKRuntimeException("ȱ��ͳһ֧���ӿڱ������trade_type��" . "<br>");
            } elseif ($this->parameters["trade_type"] == "JSAPI" && $this->parameters["openid"] == NULL) {
                throw new SDKRuntimeException("ͳһ֧���ӿ��У�ȱ�ٱ������openid��trade_typeΪJSAPIʱ��openidΪ���������" . "<br>");
            }
            $this->parameters["appid"] = APPID; // �����˺�ID
            $this->parameters["mch_id"] = MCHID; // �̻���
            $this->parameters["spbill_create_ip"] = $_SERVER['REMOTE_ADDR']; // �ն�ip
            $this->parameters["nonce_str"] = $this->createNoncestr(); // ����ַ���
            $this->parameters["sign"] = $this->getSign($this->parameters); // ǩ��
            return $this->arrayToXml($this->parameters);
        } catch (SDKRuntimeException $e) {
            die($e->errorMessage());
        }
    }

    /**
     * ��ȡprepay_id
     */
    function getPrepayId() {
        $this->postXml();
        $this->result = $this->xmlToArray($this->response);
        $prepay_id = $this->result["prepay_id"];
        return $prepay_id;
    }
}

/**
 * ������ѯ�ӿ�
 */
class OrderQuery_pub extends Wxpay_client_pub {

    function __construct() {
        // ���ýӿ�����
        $this->url = "https://api.mch.weixin.qq.com/pay/orderquery";
        // ����curl��ʱʱ��
        $this->curl_timeout = CURL_TIMEOUT;
    }

    /**
     * ���ɽӿڲ���xml
     */
    function createXml() {
        try {
            // ���������
            if ($this->parameters["out_trade_no"] == null && $this->parameters["transaction_id"] == null) {
                throw new SDKRuntimeException("������ѯ�ӿ��У�out_trade_no��transaction_id������һ����" . "<br>");
            }
            $this->parameters["appid"] = APPID; // �����˺�ID
            $this->parameters["mch_id"] = MCHID; // �̻���
            $this->parameters["nonce_str"] = $this->createNoncestr(); // ����ַ���
            $this->parameters["sign"] = $this->getSign($this->parameters); // ǩ��
            return $this->arrayToXml($this->parameters);
        } catch (SDKRuntimeException $e) {
            die($e->errorMessage());
        }
    }
}

/**
 * �˿�����ӿ�
 */
class Refund_pub extends Wxpay_client_pub {

    function __construct() {
        // ���ýӿ�����
        $this->url = "https://api.mch.weixin.qq.com/secapi/pay/refund";
        // ����curl��ʱʱ��
        $this->curl_timeout = CURL_TIMEOUT;
		$this->parameters["op_user_id"]=MCHID;
    }

    /**
     * ���ɽӿڲ���xml
     */
    function createXml() {
        try {
            // ���������
            if ($this->parameters["out_trade_no"] == null && $this->parameters["transaction_id"] == null) {
                throw new SDKRuntimeException("�˿�����ӿ��У�out_trade_no��transaction_id������һ����" . "<br>");
            } elseif ($this->parameters["out_refund_no"] == null) {
                throw new SDKRuntimeException("�˿�����ӿ��У�ȱ�ٱ������out_refund_no��" . "<br>");
            } elseif ($this->parameters["total_fee"] == null) {
                throw new SDKRuntimeException("�˿�����ӿ��У�ȱ�ٱ������total_fee��" . "<br>");
            } elseif ($this->parameters["refund_fee"] == null) {
                throw new SDKRuntimeException("�˿�����ӿ��У�ȱ�ٱ������refund_fee��" . "<br>");
            } elseif ($this->parameters["op_user_id"] == null) {
                throw new SDKRuntimeException("�˿�����ӿ��У�ȱ�ٱ������op_user_id��" . "<br>");
            }
            $this->parameters["appid"] = APPID; // �����˺�ID
            $this->parameters["mch_id"] = MCHID; // �̻���
            $this->parameters["nonce_str"] = $this->createNoncestr(); // ����ַ���
            $this->parameters["sign"] = $this->getSign($this->parameters); // ǩ��
            return $this->arrayToXml($this->parameters);
        } catch (SDKRuntimeException $e) {
            die($e->errorMessage());
        }
    }

    /**
     * ���ã���ȡ�����ʹ��֤��ͨ��
     */
    function getResult() {
        $this->postXmlSSL();
        $this->result = $this->xmlToArray($this->response);
        return $this->result;
    }
}

/**
 * �˿��ѯ�ӿ�
 */
class RefundQuery_pub extends Wxpay_client_pub {

    function __construct() {
        // ���ýӿ�����
        $this->url = "https://api.mch.weixin.qq.com/pay/refundquery";
        // ����curl��ʱʱ��
        $this->curl_timeout = CURL_TIMEOUT;
    }

    /**
     * ���ɽӿڲ���xml
     */
    function createXml() {
        try {
            if ($this->parameters["out_refund_no"] == null && $this->parameters["out_trade_no"] == null && $this->parameters["transaction_id"] == null && $this->parameters["refund_id "] == null) {
                throw new SDKRuntimeException("�˿��ѯ�ӿ��У�out_refund_no��out_trade_no��transaction_id��refund_id�ĸ���������һ����" . "<br>");
            }
            $this->parameters["appid"] = APPID; // �����˺�ID
            $this->parameters["mch_id"] = MCHID; // �̻���
            $this->parameters["nonce_str"] = $this->createNoncestr(); // ����ַ���
            $this->parameters["sign"] = $this->getSign($this->parameters); // ǩ��
            return $this->arrayToXml($this->parameters);
        } catch (SDKRuntimeException $e) {
            die($e->errorMessage());
        }
    }

    /**
     * ���ã���ȡ�����ʹ��֤��ͨ��
     */
    function getResult() {
        $this->postXmlSSL();
        $this->result = $this->xmlToArray($this->response);
        return $this->result;
    }
}

/**
 * ���˵��ӿ�
 */
class DownloadBill_pub extends Wxpay_client_pub {

    function __construct() {
        // ���ýӿ�����
        $this->url = "https://api.mch.weixin.qq.com/pay/downloadbill";
        // ����curl��ʱʱ��
        $this->curl_timeout = CURL_TIMEOUT;
    }

    /**
     * ���ɽӿڲ���xml
     */
    function createXml() {
        try {
            if ($this->parameters["bill_date"] == null) {
                throw new SDKRuntimeException("���˵��ӿ��У�ȱ�ٱ������bill_date��" . "<br>");
            }
            $this->parameters["appid"] = APPID; // �����˺�ID
            $this->parameters["mch_id"] = MCHID; // �̻���
            $this->parameters["nonce_str"] = $this->createNoncestr(); // ����ַ���
            $this->parameters["sign"] = $this->getSign($this->parameters); // ǩ��
            return $this->arrayToXml($this->parameters);
        } catch (SDKRuntimeException $e) {
            die($e->errorMessage());
        }
    }

    /**
     * ���ã���ȡ�����Ĭ�ϲ�ʹ��֤��
     */
    function getResult() {
        $this->postXml();
        $this->result = $this->xmlToArray($this->result_xml);
        return $this->result;
    }
}

/**
 * ������ת���ӿ�
 */
class ShortUrl_pub extends Wxpay_client_pub {

    function __construct() {
        // ���ýӿ�����
        $this->url = "https://api.mch.weixin.qq.com/tools/shorturl";
        // ����curl��ʱʱ��
        $this->curl_timeout = CURL_TIMEOUT;
    }

    /**
     * ���ɽӿڲ���xml
     */
    function createXml() {
        try {
            if ($this->parameters["long_url"] == null) {
                throw new SDKRuntimeException("������ת���ӿ��У�ȱ�ٱ������long_url��" . "<br>");
            }
            $this->parameters["appid"] = APPID; // �����˺�ID
            $this->parameters["mch_id"] = MCHID; // �̻���
            $this->parameters["nonce_str"] = $this->createNoncestr(); // ����ַ���
            $this->parameters["sign"] = $this->getSign($this->parameters); // ǩ��
            return $this->arrayToXml($this->parameters);
        } catch (SDKRuntimeException $e) {
            die($e->errorMessage());
        }
    }

    /**
     * ��ȡprepay_id
     */
    function getShortUrl() {
        $this->postXml();
        $prepay_id = $this->result["short_url"];
        return $prepay_id;
    }
}

/**
 * ��Ӧ�ͽӿڻ���
 */
class Wxpay_server_pub extends Common_util_pub {

    public $data;
 // ���յ������ݣ�����Ϊ��������
    var $returnParameters;
 // ���ز���������Ϊ��������
    
    /**
     * ��΢�ŵ�����xmlת���ɹ������飬�Է������ݴ���
     */
    function saveData($xml) {
        $this->data = $this->xmlToArray($xml);
    }

    function checkSign() {
        $tmpData = $this->data;
        unset($tmpData['sign']);
        $sign = $this->getSign($tmpData); // ����ǩ��
        if ($this->data['sign'] == $sign) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * ��ȡ΢�ŵ���������
     */
    function getData() {
        return $this->data;
    }

    /**
     * ���÷���΢�ŵ�xml����
     */
    function setReturnParameter($parameter, $parameterValue) {
        $this->returnParameters[$this->trimString($parameter)] = $this->trimString($parameterValue);
    }

    /**
     * ���ɽӿڲ���xml
     */
    function createXml() {
        return $this->arrayToXml($this->returnParameters);
    }

    /**
     * ��xml���ݷ���΢��
     */
    function returnXml() {
        $returnXml = $this->createXml();
        return $returnXml;
    }
}

/**
 * ͨ��֪ͨ�ӿ�
 */
class Notify_pub extends Wxpay_server_pub {
}

/**
 * JSAPI֧������H5��ҳ�˵���֧���ӿ�
 */
class JsApi_pub extends Common_util_pub {

    var $code;
 // code�룬���Ի�ȡopenid
    var $openid;
 // �û���openid
    var $parameters;
 // jsapi��������ʽΪjson
    var $prepay_id;
 // ʹ��ͳһ֧���ӿڵõ���Ԥ֧��id
    var $curl_timeout;
 // curl��ʱʱ��
    

    /**
     * ���ã����ɿ��Ի��code��url
     */
    function createOauthUrlForCode($redirectUrl) {
        $urlObj["appid"] = APPID;
        $urlObj["redirect_uri"] = "$redirectUrl";
        $urlObj["response_type"] = "code";
        $urlObj["scope"] = "snsapi_base";
        $urlObj["state"] = "STATE" . "#wechat_redirect";
        $bizString = $this->formatBizQueryParaMap($urlObj, false);
        return "https://open.weixin.qq.com/connect/oauth2/authorize?" . $bizString;
    }

    /**
     * ���ã����ɿ��Ի��openid��url
     */
    function createOauthUrlForOpenid() {
        $urlObj["appid"] = APPID;
        $urlObj["secret"] = APPSECRET;
        $urlObj["code"] = $this->code;
        $urlObj["grant_type"] = "authorization_code";
        $bizString = $this->formatBizQueryParaMap($urlObj, false);
        return "https://api.weixin.qq.com/sns/oauth2/access_token?" . $bizString;
    }

    /**
     * ���ã�ͨ��curl��΢���ύcode���Ի�ȡopenid
     */
    function getOpenid() {
        $url = $this->createOauthUrlForOpenid();
        // ��ʼ��curl
        $ch = curl_init();
        // ���ó�ʱ
        curl_setopt($ch, CURLOP_TIMEOUT, $this->curl_timeout);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        // ����curl�������jason��ʽ����
        $res = curl_exec($ch);
        curl_close($ch);
        // ȡ��openid
        $data = json_decode($res, true);
        $this->openid = $data['openid'];
        return $this->openid;
    }

    /**
     * ���ã�����prepay_id
     */
    function setPrepayId($prepayId) {
        $this->prepay_id = $prepayId;
    }

    /**
     * ���ã�����code
     */
    function setCode($code_) {
        $this->code = $code_;
    }

    /**
     * ���ã�����jsapi�Ĳ���
     */
    public function getParameters() {
        $jsApiObj["appId"] = APPID;
        $timeStamp = time();
        $jsApiObj["timeStamp"] = "$timeStamp";
        $jsApiObj["nonceStr"] = $this->createNoncestr();
        $jsApiObj["package"] = "prepay_id=$this->prepay_id";
        $jsApiObj["signType"] = "MD5";
        $jsApiObj["paySign"] = $this->getSign($jsApiObj);
        $this->parameters = json_encode($jsApiObj);
        
        return $this->parameters;
    }
}


