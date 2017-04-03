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

define('IN_API', true);
define('CURSCRIPT', 'api');
require '../../../source/class/class_core.php';
$discuz = C::app();
$discuz->init();

$_GET = dhtmlspecialchars(daddslashes($_GET));
$_POST = dhtmlspecialchars(daddslashes($_POST));

    if (!isset($_G['cache']['plugin'])) {
        loadcache('plugin');
    }
$config = $_G['cache']['plugin']['rpancn_buy_usergroup'];

$pay_type = empty($_GET['attach']) || !preg_match('/^[a-z0-9]+$/i', $_GET['attach']) ? 'alipay' : $_GET['attach'];
require_once DISCUZ_ROOT . '/source/plugin/rpancn_buy_usergroup/model/core.php';
$PHP_SELF = $_SERVER['PHP_SELF'];
$_G['siteurl'] = dhtmlspecialchars('http://'.$_SERVER['HTTP_HOST'].preg_replace("/\/+(source\/plugin\/rpancn_buy_usergroup)?\/*$/i", '', substr($PHP_SELF, 0, strrpos($PHP_SELF, '/'))).'/');
$notifydata = trade_notifycheck();


//pay ok
if($notifydata['validator']) {
	$orderid = $notifydata['order_no'];
	$price = $notifydata['price'];
	$orderquery = DB::query("SELECT * FROM " . DB::table('rpancn_buy_user_group_record') . " where orderid = '".$orderid."'");
	$order = DB::fetch($orderquery);
	$orderprice = floatval($order['pricepday']*($order['days']?$order['days']:1));
	//金额匹配
	if($order && floatval($price- $orderprice)<0.01 ) {
	//if($order && floatval($price) == floatval($order['pricepday']*$order['days']) ) {
		if($order['status'] == 1 && $order['uid'] != 1) {
			$notifydata['trade_no']=$notifydata['trade_no']."\r\nTWPAL";
			//change oder stats
			DB::update('rpancn_buy_user_group_record', array(
				'status' => '2',
				'trade_no' => $notifydata['trade_no'],
				'confirmdate' => $_G['timestamp'],
			), "orderid='".$orderid."'");

			//change groupid
			if($order['days']==0){//设置永不过期
				DB::update('common_member', array(
					'groupid' => $order['gid'],
					'groupexpiry' => 0,
				), "uid='".$order['uid']."'");
			}elseif (is_numeric($order['days']) && $order['days']>0) {//设置有效期
					//主用户组修改
					$common_memberquery = DB::query("SELECT * FROM " . DB::table('common_member') . " where uid = ".$order['uid']."");
					$common_member = DB::fetch($common_memberquery);

					//用户组相同则在原有效期基础上延长
					//$groupexpiry=$common_member['groupexpiry']?$common_member['groupexpiry']:$_G['timestamp'];
					//$exptimenew=$order['days']*86400?($order['days']*86400+$groupexpiry):0;
					$exptimenew=strtotime(date('Y-m-d H:i:s',strtotime("+$order[days] day")));
					$groupid=$common_member['groupid'];//原用户组
					$adminid=$common_member['adminid'];//原管理组

					//更新主用户组和有效期
					DB::update('common_member', array(
						'groupid' => $order['gid'],
						'groupexpiry' => $exptimenew,
					), "uid='".$order['uid']."'");

					//配置过期用户组
					$grouptermsarray['main']['time']=$exptimenew;
					if($groupid!=$order['gid']){//新旧用户组不同
						$grouptermsarray['main']['groupid']=$groupid;
						$grouptermsarray['main']['adminid']=$adminid;
					}
					$grouptermsarray['ext'][$order['gid']]=$exptimenew;
					if(!$exptimenew){
						unset($grouptermsarray['main']['time']);
						unset($grouptermsarray['ext'][$order['gid']]);
					}
					$groupterms=serialize($grouptermsarray);
					DB::update('common_member_field_forum', array(
						'groupterms' => $groupterms,
					), "uid='".$order['uid']."'");
			}
			if($order['extcredits']!="" && $order['extcredits']!="0"){
				$tmp=explode(',', $order['extcredits']);
				foreach ($tmp as $value) {
					$credit=explode(':', $value);
					updatemembercount($order['uid'], array($credit[1] =>$credit['2']));
				}
			}
		}
	}
}
if($notifydata['location']) {
	showmessage('rpancn_buy_usergroup:rpancn_pay_succeed', $_G['siteurl'].'home.php?mod=spacecp&ac=usergroup');
} else {
	exit($notifydata['notify']);
}
?>