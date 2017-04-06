<?php
/**
 *      (C)2007-2012 singcere.net
 *      This is a freeware, But you have no right to modify or distribute
 *      $Id$
 */

if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}

$langs = &$scriptlang['singcere_wechat'];
require libfile('function/admincp', 'plugin/singcere_wechat');

require DISCUZ_ROOT . 'source/plugin/singcere_wechat/class/wxpay.class.php';

$op = in_array($_GET['op'], array('pay', 'redpack')) ? $_GET['op'] : 'pay';


admincp_showsubmenu(null, array(
    array($langs['admincp_pay_pay'], "plugins&operation=config&do=$pluginid&identifier=$plugin[identifier]&pmod=$module[name]&op=pay", $op == 'pay'),
    array($langs['admincp_pay_redpack'], "plugins&operation=config&do=$pluginid&identifier=$plugin[identifier]&pmod=$module[name]&op=redpack", $op == 'redpack'),

));

echo <<<EOD
<script type="text/javascript">
function togglemore(k) {
	var more = $('more_'+k);
	if(more.style.display == 'none') {
		more.style.display = '';
	} else {
		more.style.display = 'none';
	}
}
</script>
EOD;


$page = max(1, intval($_GET['page']));
$perpage = 15;


if ($op == 'pay') {
    $count = C::t('#singcere_wechat#singcere_wechat_pay')->count_by_search(array());
    showformheader("");
    showtableheader();
    echo '<tr class="header"><th width="250">'.$langs['admincp_pay_out_trade_no'].'</th><th  width="100">'.$langs['admincp_pay_sence'].'</th><th width="100">'.$langs['admincp_total_fee'].'</th><th width="100">'.$langs['admincp_refund_fee'].'</th><th width="200">openid</th><th width="120">'.$langs['admincp_pay_dateline'].'</th><th>TYPE</th><th>ID</th></tr>';
    if ($count) {
        $paylist = C::t('#singcere_wechat#singcere_wechat_pay')->fetch_all_by_search(array(), 'dateline DESC', ($page - 1) * $perpage, $perpage);
        $multipage = multi($count, $perpage, $page, ADMINSCRIPT . "?action=plugins&operation=config&do=$plugin[pluginid]&identifier=$plugin[identifier]&pmod=$module[name]&op=pay");
        foreach ($paylist as $id => $pay) {
            showtablerow('', array(), array(
                '<a href="javascript:;" onclick="togglemore('.$id.')">'.$pay['out_trade_no'].'</a>',
                array_search($pay['senceid'], WxPayApi::$senceid),
                "&yen; " . $pay['total_fee'] / 100,
                "&yen; " . $pay['refund_fee'] / 100,
                $pay['openid'] . "<br>" . $pay['unionid'],
                dgmdate($pay['dateline'], 'Ymd H:i:s'),
                $pay['fromtype'],
                ($pay['fromid'] ? $pay['fromid'] : '')
            ));

            echo '<tbody id="more_'.$id.'" style="display:none;">';
            echo "<tr><td colspan='15'>".($pay[uid] ? "username={ <a href='home.php?mod=space&uid=$pay[uid]'>$pay[username]</a> };\t" : "\t")."transaction_id={ $pay[transaction_id] };\tprepay_id={ $pay[prepay_id] };\tfee_type={ $pay[fee_type] };</td></tr>";
            echo '</tbody>';
        }
    }
    showsubmit('', '', '', '', $multipage);
    showtablefooter();
    showformfooter();

} else {
    $count = C::t('#singcere_wechat#singcere_wechat_redpack')->count_by_search(array());


    showformheader("");
    showtableheader();
    echo '<tr class="header"><th width="250">'.$langs['admincp_pay_out_trade_no'].'</th><th width="150">'.$langs['admincp_pay_send_name'].'</th><th width="100">'.$langs['admincp_pay_total_amount'].'</th><th width="100">'.$langs['admincp_pay_total_num'].'</th><th width="200">RE_OPENID</th><th>'.$langs['admincp_pay_dateline'].'</th><th>TYPE</th><th>ID</th></tr>';

    if ($count) {
        $packlist = C::t('#singcere_wechat#singcere_wechat_redpack')->fetch_all_by_search(array(), 'dateline DESC', ($page - 1) * $perpage, $perpage);
        $multipage = multi($count, $perpage, $page, ADMINSCRIPT . "?action=plugins&operation=config&do=$plugin[pluginid]&identifier=$plugin[identifier]&pmod=$module[name]&op=redpack");
        foreach ($packlist as $id => $pack) {
            showtablerow('', array(), array(
                '<a href="javascript:;" onclick="togglemore('.$id.')">'.$pack['mch_billno'].'</a>',
                $pack['send_name'],
                "&yen; " . $pack['total_amount'] / 100,
                $pack['total_num'],
                $pack['re_openid'],
                dgmdate($pack['dateline'], 'Ymd H:i:s'),
                $pack['fromtype'],
                ($pack['fromid'] ? $pack['fromid'] : ''),

            ));
            echo '<tbody id="more_'.$id.'" style="display:none;">';
            echo "<tr><td colspan='15'>send_listid={ $pack[send_listid] };\twishing={ $pack[wishing] };\tact_name={ $pack[act_name] };\tamt_type={ $pack[amt_type] };\tremark={ $pack[remark] };</td></tr>";
            echo '</tbody>';
        }
    }
    showsubmit('', '', '', '', $multipage);
    showtablefooter();
    showformfooter();

}