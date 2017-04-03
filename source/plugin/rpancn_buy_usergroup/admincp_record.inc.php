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

if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}
global $_G;
$orderurl = array(
    'alipay' => 'https://www.alipay.com/trade/query_trade_detail.htm?trade_no=',
    'tenpay' => 'https://www.tenpay.com/med/tradeDetail.shtml?trans_id='
);
cpheader();

$_GET = dhtmlspecialchars(daddslashes($_GET));
$_POST = dhtmlspecialchars(daddslashes($_POST));

$perpage = max(20, empty($_GET['perpage']) ? 20 : intval($_GET['perpage']));
$start_limit = ($page - 1) * $perpage;

$rpancn_scriptlang = $scriptlang['rpancn_buy_usergroup'];

showtagheader('div', 'orderlist', TRUE);
showformheader('plugins&identifier=rpancn_buy_usergroup&pmod=admincp_record');
showtableheader($rpancn_scriptlang['rpancn_orders_tips']);
showsubtitle(array(
    '&nbsp;&#21024;',
    $rpancn_scriptlang['rpancn_orders_id'],
    $rpancn_scriptlang['rpancn_order_status'],
    $rpancn_scriptlang['rpancn_orders_buyer'],
    $rpancn_scriptlang['rpancn_buy_groupname'],
    $rpancn_scriptlang['rpancn_expiration'],
    $rpancn_scriptlang['rpancn_orders_price'],
    $rpancn_scriptlang['rpancn_orders_submitdate'],
    $rpancn_scriptlang['rpancn_orders_confirmdate'],
));

	$url = ADMINSCRIPT."?action=plugins&operation=config&do='.$pluginid.'&identifier=rpancn_buy_usergroup&pmod=admincp_record";
	$count = DB::result_first("SELECT COUNT(*) FROM ".DB::table('rpancn_buy_user_group_record')." WHERE 1");

	if($count) {
		$multipage = multi($count, $perpage, $page, $url, 0, 3);
		$orderquery = DB::query("SELECT * FROM " . DB::table('rpancn_buy_user_group_record') . " order by submitdate DESC LIMIT $start_limit, $perpage");
		while ($orders = DB::fetch($orderquery)) {
				$orderslist[] = $orders;
		}
		foreach ($orderslist as $order) {
			switch ($order['status']) {
				case 1:
					$order['orderstatus'] = $rpancn_scriptlang['rpancn_orders_search_status_pending'];
					break;
				case 2:
					$order['orderstatus'] = $rpancn_scriptlang['rpancn_orders_search_status_auto_finished'];
					break;
			}
			$order['submitdate']  = dgmdate($order['submitdate']);
			$order['confirmdate'] = $order['confirmdate'] ? dgmdate($order['confirmdate']) : 'N/A';
			$orderid              = $order['trade_no'];
			$tmp                  = explode("\r\n", $orderid);
			

			$validity             = $order['days'];
			if ($validity == "0") {
				$validity = $rpancn_scriptlang['rpancn_forever'];
				$days = 1;
			} else {
				$validity = $validity . $rpancn_scriptlang['rpancn_day'];
				$days = $validity;
			}
			$orderid = '<a href="' . $orderurl[$tmp[1]] . $tmp[0] . '" target="_blank">' . $orderid . '--------</a>';
			//echo $order[confirmdate];
			if ($order[confirmdate] == 'N/A') {
				$order[confirmdate] = 'N/A (<a href="?action=plugins&operation=config&identifier=rpancn_buy_usergroup&pmod=admincp_record&orderid=' . $order['orderid'] . '">&#34917;&#21333;</a>)';
			} else {
				$order[confirmdate] = $order['confirmdate'] . '<br> (&#34917;&#21333;)';
			}
			showtablerow('', '', array(
				"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"$order[orderid]\" " . ($order['status'] != 1 ? 'disabled' : '') . ">",
				"$order[orderid]<br />$orderid",
				$order[orderstatus],
				"<a href=\"home.php?mod=space&uid=$order[uid]\" target=\"_blank\">$order[uname]</a>",
				"$order[gname]",
				$validity,
				$rpancn_scriptlang['rpancn_rmb'] . " " . $order[pricepday]*$days . " " . $rpancn_scriptlang['rpancn_yuan'],
				$order[submitdate],
				$order[confirmdate]
			));
		}
	}

showsubmit('delsubmit', 'submit', 'del', '', $multipage, false);
showtablefooter();
showformfooter();
showtagfooter('div');
//manual confirm
if (submitcheck('orderid', 1)) {
    showtagheader('div', 'orderlist', TRUE);
    showformheader('plugins&identifier=rpancn_buy_usergroup&pmod=admincp_record');
    $orderid = $_GET['orderid'];
    //get order
    //$order   = C::t('#rpancn_buy_usergroup#rpancn_buy_user_group_record')->fetch($orderid);
	$orderquery = DB::query("SELECT * FROM " . DB::table('rpancn_buy_user_group_record') . " where orderid = '".$orderid."'");
	$order = DB::fetch($orderquery);

    if ($order['status'] && $order['uid'] != 1) {
        //change status
		//X2
		DB::update('rpancn_buy_user_group_record', array(
			'status' => '2',
			'confirmdate' => $_G['timestamp'],
		), "orderid='".$orderid."'");
		//X2.5
        //C::t('#rpancn_buy_usergroup#groupbuy_order')->update(array(
        //    'status' => '2',
        //    'confirmdate' => $_G['timestamp']
        //), $orderid);
        //change groupid
        //$order['uid'] = 2;
        if ($order['days'] == 0) {
			//X2
			DB::update('common_member', array(
                'groupid' => $order['gid'],
                'groupexpiry' => 0,
			), "uid='".$order['uid']."'");
			//X2.5
            //C::t('common_member')->update($order['uid'], array(
            //    'groupid' => $order['gid'],
            //    'groupexpiry' => 0
            //));
        } elseif (is_numeric($order['days']) && $order['days'] > 0) {
            //主用户组修改
			//X2
			$common_memberquery = DB::query("SELECT * FROM " . DB::table('common_member') . " where uid = ".$order['uid']."");
			$common_member = DB::fetch($common_memberquery);
			//X2.5
            //$common_member = C::t('common_member')->fetch($order['uid']);

            //用户组相同则在原有效期基础上延长
            //$groupexpiry=$common_member['groupexpiry']?$common_member['groupexpiry']:$_G['timestamp'];
            //$exptimenew=$order['days']*86400?($order['days']*86400+$groupexpiry):0;
            $exptimenew    = strtotime(date('Y-m-d H:i:s', strtotime("+$order[days] day")));
            $groupid       = $common_member['groupid']; //原用户组
            $adminid       = $common_member['adminid']; //原管理组

			//更新主用户组和有效期
			//X2
			DB::update('common_member', array(
                'groupid' => $order['gid'],
                'groupexpiry' => $exptimenew,
			), "uid='".$order['uid']."'");
			//X2.5
            //C::t('common_member')->update($order['uid'], array(
            //    'groupid' => $order['gid'],
            //    'groupexpiry' => $exptimenew
            //)); 

            //配置过期用户组
            $grouptermsarray['main']['time'] = $exptimenew;
            if ($groupid != $order['gid']) { //新旧用户组不同
                $grouptermsarray['main']['groupid'] = $groupid;
                $grouptermsarray['main']['adminid'] = $adminid;
            }
            $grouptermsarray['ext'][$order['gid']] = $exptimenew;
            if (!$exptimenew) {
                unset($grouptermsarray['main']['time']);
                unset($grouptermsarray['ext'][$order['gid']]);
            }
            $groupterms = serialize($grouptermsarray);
			//X2
			DB::update('common_member_field_forum', array(
                'groupterms' => $groupterms,
			), "uid='".$order['uid']."'");
			//X2.5
            //C::t('common_member_field_forum')->update($order['uid'], array(
            //    'groupterms' => $groupterms
            //));
        }
        //change extcredits
        if ($order['extcredit'] != "" && $order['extcredit_value'] != "0") {
            $tmp = explode(',', $order['extcredits']);
            foreach ($tmp as $value) {
                $credit = explode(':', $value);
                updatemembercount($order['uid'], array(
                    $credit[1] => $credit['2']
                ));
            }
        }
    }
    ECHO '<div class="infobox"><h4 class="infotitle3">&#34917;&#21333;&#25104;&#21151;</h4><p class="marginbot"><script type="text/javascript">if(history.length > (BROWSER.ie ? 0 : 1)) document.write(\'<a href="javascript:history.go(-1);" class="lightlink">&#28857;&#20987;&#36825;&#37324;&#36820;&#22238;&#19978;&#19968;&#39029;</a>\');</script></p>';
    showtablefooter();
    showformfooter();
    showtagfooter('div');
}
//delete order
if (submitcheck('delsubmit', 1)) {
    showtagheader('div', 'orderlist', TRUE);
    showformheader('plugins&identifier=rpancn_buy_usergroup&pmod=admincp_record');
    $orders = $_GET['delete'];
    foreach ($orders as $orderid) {
		$query      = DB::query("delete FROM " . DB::table('rpancn_buy_user_group_record') . " WHERE orderid='$orderid'");
        //C::t('#rpancn_buy_usergroup#groupbuy_order')->delete_by_orderid($orderid);
    }
    ECHO '<div class="infobox"><h4 class="infotitle3">&#21024;&#38500;&#25104;&#21151;</h4><p class="marginbot"><script type="text/javascript">if(history.length > (BROWSER.ie ? 0 : 1)) document.write(\'<a href="javascript:history.go(-1);" class="lightlink">&#28857;&#20987;&#36825;&#37324;&#36820;&#22238;&#19978;&#19968;&#39029;</a>\');</script></p>';
    showtablefooter();
    showformfooter();
    showtagfooter('div');
}
?>