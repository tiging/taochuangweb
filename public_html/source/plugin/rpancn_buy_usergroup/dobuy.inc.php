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
    exit('Access Denied');
}
global $_G;
//加载配置信息
if (!$_G['uid']) {
    showmessage('not_loggedin', NULL, array(), array(
        'login' => 1
    ));
}
$_GET = dhtmlspecialchars(daddslashes($_GET));
$_POST = dhtmlspecialchars(daddslashes($_POST));

$config = $_G['cache']['plugin']['rpancn_buy_usergroup'];

if ($config[alipayopen] && $config['default'] == 'alipay') {
    $alipay_checked = 'checked';
} elseif ($config[bankopen] && $config['default'] == 'bank') {
    $bank_checked = 'checked';
} elseif ($config[tenpayopen] && $config['default'] == 'tenpay') {
    $tenpay_checked = 'checked';
} else {
    $tenpay_checked = 'checked';
}
if (!$config['is_open']) {
    showmessage(lang('plugin/rpancn_buy_usergroup', 'rpancn_plugin_not_open'), 'home.php?mod=spacecp&ac=usergroup');
}
if (!$_POST['buysubmit']) {
    $gid = intval($_GET['gid']); //action: SHOW,BUY
    
    if (!$gid) {
        return;
    }
    //grouptitle
    $groupquery = DB::query("SELECT grouptitle FROM " . DB::table('common_usergroup') . " where groupid = " . $gid . " ORDER BY groupid");
    $groups     = DB::fetch($groupquery);
    
    $packagequery = DB::query("SELECT * FROM " . DB::table('rpancn_buy_user_group_package') . " where gid = " . $gid . " ORDER BY gid");
    $package      = DB::fetch($packagequery);
    //return xml
    $str          = '<div class="f_c">
		<h3 class="flb">
		<em id="return_group">' . lang('plugin/rpancn_buy_usergroup', 'rpancn_buy_groupname') . ' ' . $groups['grouptitle'] . '</em>
		<span>';
    if ($_G['inajax']) {
        $str .= '<a href="javascript:;" onclick="hideWindow(\'group\');" class="flbc" title="' . lang('plugin/rpancn_buy_usergroup', 'rpancn_close') . '">' . lang('plugin/rpancn_buy_usergroup', 'rpancn_close') . '</a>';
    }
    $str .= '
		</span></h3>

		<!--form id="rpancn_buygroup_' . $gid . '" name="rpancn_buygroup_' . $gid . '" method="post" autocomplete="off" action="plugin.php?id=rpancn_buy_usergroup:dobuy" onsubmit="ajaxpost(\'rpancn_buygroup_' . $gid . '\', \'return_group\', \'return_group\', \'onerror\');return false;"-->
		<form id="rpancn_buygroup_' . $gid . '" name="rpancn_buygroup_' . $gid . '" method="post" autocomplete="off" action="plugin.php?id=rpancn_buy_usergroup:dobuy" >
		<input type="hidden" name="buysubmit" value="true" />
		<input type="hidden" name="gid" value="' . $gid . '" />

		<input type="hidden" name="handlekey" value="group" />
			<div class="c"> <table class="list" cellspacing="0" cellpadding="0" style="width:680px">
					<tr>
						<td style="width:80px">' . lang('plugin/rpancn_buy_usergroup', 'rpancn_day_price') . '</td>
						<td> ' . $package[pricepday] . ' ' . lang('plugin/rpancn_buy_usergroup', 'rpancn_yuan') . '</td>
					</tr>
					<tr>
						<td>' . lang('plugin/rpancn_buy_usergroup', 'rpancn_buy_time') . '</td>
						<td><input type="text" size="2" name="days" value="' . $package[minday] . '" class="px" onkeyup="change_credits_need(this.value)" /> ' . lang('plugin/rpancn_buy_usergroup', 'rpancn_day') . '</td>
					</tr>
				  <tr> <td>' . lang('plugin/rpancn_buy_usergroup', 'rpancn_need_price') . '</td><td><span id="credits_need">'.($package[minday]>=1?$package[minday]:1)*$package[pricepday].'</span>' . lang('plugin/rpancn_buy_usergroup', 'rpancn_yuan') . '</td> </tr>';
				  if ($package[minday]>=1){
					$str .= '
						<script language="javascript">
							var dailyprice = ' . $package[pricepday] . ';
							function change_credits_need(daynum) {
								if(!isNaN(parseInt(daynum)) && parseInt(daynum)) {
									$(\'credits_need\').innerHTML = parseInt(daynum) * dailyprice;
								} else {
									$(\'credits_need\').innerHTML = dailyprice;
								}
							}
							change_credits_need('.$package[minday].');
						</script> ';
				  }
    //奖励
    //$str .= '<tr> <td>' . lang('plugin/rpancn_buy_usergroup', 'rpancn_premium') . '</td><td>' . $ct[0] . ': ' . $ct[2] . '</td> </tr> ';
    //$str .= '<tr> <td></td><td>' . $ct[0] . ': ' . $ct[2] . '</td> </tr> ';
    if (!$config['partner'] || !$config['securitycode']) {
        //错误
        $str .= '
		<tr>
		<td colspan="2">' . lang('plugin/rpancn_buy_usergroup', 'rpancn_pay_error2') . '</td>
		</tr>';
    } else {
        //支付方式
        $str .= '<link rel="stylesheet" type="text/css" href="http://union.tenpay.com/bankList/css_col3.css" />
		<tr> <td>' . lang('plugin/rpancn_buy_usergroup', 'rpancn_buy_type') . '</td>  <td> <table cellspacing="0" cellpadding="0" style="margin:0 auto 10px;width:600px">';
        //财付通
        $str .= '<tr><td>
		<input name="pid" type="radio" value="17" onClick="checkValue(this)" id="J-b2c_ebank-icbc105-21" ' . $tenpay_checked . '/><label class="icon-box21" for="J-b2c_ebank-icbc105-21" ></label>
		</td> </tr> ';
        if ($config[bankopen]) {
            //网银
            $str .= '
			<tr>
				<td>
			<input type="radio" onClick="checkValue(this)" name="pid" id="J-b2c_ebank-icbc105-1" value="ICBC-NET" ' . $bank_checked . '/><label class="icon-box1" for="J-b2c_ebank-icbc105-1" style="width:170px;"></label>
			<input type="radio" onClick="checkValue(this)" name="pid" id="J-b2c_ebank-icbc105-2" value="CMBCHINA-NET" /><label class="icon-box2" for="J-b2c_ebank-icbc105-2" style="width:170px;"></label>
			<input type="radio" onClick="checkValue(this)" name="pid" id="J-b2c_ebank-icbc105-3" value="CCB-NET" /><label class="icon-box3" for="J-b2c_ebank-icbc105-3" style="width:170px;"></label>
			<input type="radio" onClick="checkValue(this)" name="pid" id="J-b2c_ebank-icbc105-4" value="ABC-NET" /><label class="icon-box4" for="J-b2c_ebank-icbc105-4" style="width:170px;"></label>
			<input type="radio" onClick="checkValue(this)" name="pid" id="J-b2c_ebank-icbc105-5" value="BOC-NET" /><label class="icon-box5" for="J-b2c_ebank-icbc105-5" style="width:170px;"></label>
			<input type="radio" onClick="checkValue(this)" name="pid" id="J-b2c_ebank-icbc105-6" value="POST-NET" /><label class="icon-box6" for="J-b2c_ebank-icbc105-6" style="width:170px;"></label>
			<input type="radio" onClick="checkValue(this)" name="pid" id="J-b2c_ebank-icbc105-7" value="SPDB-NET" /><label class="icon-box7" for="J-b2c_ebank-icbc105-7" style="width:170px;"></label>
			<input type="radio" onClick="checkValue(this)" name="pid" id="J-b2c_ebank-icbc105-8" value="GDB-NET" /><label class="icon-box8" for="J-b2c_ebank-icbc105-8" style="width:170px;"></label>
			<input type="radio" onClick="checkValue(this)" name="pid" id="J-b2c_ebank-icbc105-9" value="CEB-NET" /><label class="icon-box9" for="J-b2c_ebank-icbc105-9" style="width:170px;"></label>
			<input type="radio" onClick="checkValue(this)" name="pid" id="J-b2c_ebank-icbc105-10" value="CMBC-NET" /><label class="icon-box10" for="J-b2c_ebank-icbc105-10" style="width:170px;"></label>
			<input type="radio" onClick="checkValue(this)" name="pid" id="J-b2c_ebank-icbc105-11" value="ECITIC-NET" /><label class="icon-box11" for="J-b2c_ebank-icbc105-11" style="width:170px;"></label>
			<input type="radio" onClick="checkValue(this)" name="pid" id="J-b2c_ebank-icbc105-12" value="CIB-NET" /><label class="icon-box12" for="J-b2c_ebank-icbc105-12" style="width:170px;"></label>
			<input type="radio" onClick="checkValue(this)" name="pid" id="J-b2c_ebank-icbc105-13" value="PAB-NET" /><label class="icon-box13" for="J-b2c_ebank-icbc105-13" style="width:170px;"></label>
			<input type="radio" onClick="checkValue(this)" name="pid" id="J-b2c_ebank-icbc105-15" value="COMM-NET" /><label class="icon-box15" for="J-b2c_ebank-icbc105-15" style="width:170px;"></label>
		</td> </tr>';
        }
        if ($config[alipayopen]) {
            //支付宝
            $str .= '<tr><td>
		<input name="pid" type="radio" value="16" onclick="checkValue(this)" id="apitype_alipay" class="vm" ' . $alipay_checked . '/><label class="vm" style="margin-right:18px;width:135px;height:32px;background:#FFF url(static/image/common/alipay_logo.gif) no-repeat;border:1px solid #DDD;display:inline-block;" onclick="" for="apitype_alipay"></label>
		</td> </tr>';
        }
    }
    $str .= '</table>
		</td> </tr>';
    //说明
    $str .= '
		<tr>
		<td colspan="2">' . $config['notice'] . '</td>
		</tr>';
    $str .= '	
		</table>
		</div>
		<p class="ptm pbw hm">
		<strong><button type="submit" name="editsubmit_btn" id="editsubmit_btn" value="true" class="pn pnc" style="padding: 0 10px;line-height: 21px;">' . lang('plugin/rpancn_buy_usergroup', 'rpancn_submit') . '</button></strong>
		</p>
		</form>
		</div>' . "\r\n";
    //跳转到支付页面
    include template('common/header');
    echo $str;
    include template('common/footer');
    
} else {
    $pid = intval($_POST['pid']);
    if (!$pid) {
        $Bank = ($_POST['pid'] == '') ? '' : $_POST['pid'];
        $pid  = 15;
    }
    $days = intval($_POST['days']);
    $gid  = intval($_POST['gid']);
    //require_once DISCUZ_ROOT . '/source/plugin/rpancn_buy_usergroup/core.php';
    require_once DISCUZ_ROOT . '/source/plugin/rpancn_buy_usergroup/model/core.php';
    //id
    if (!($gid)) {
        return;
    }
    //grouptitle
    $groupquery = DB::query("SELECT grouptitle FROM " . DB::table('common_usergroup') . " where groupid = " . $gid . " ORDER BY groupid");
    $groups     = DB::fetch($groupquery);
    //获取信息
    $query      = DB::query("SELECT * FROM " . DB::table('rpancn_buy_user_group_package') . " WHERE gid='$gid'");
    while ($group_package = DB::fetch($query)) {
        $pricepday       = $group_package['pricepday'];
        $extcredit       = $group_package['extcredit'];
        $extcredit_value = $group_package['extcredit_value'];
        $pricepday       = $group_package['pricepday'];
        $minday          = $group_package['minday'];
        //最少购买0天，表示用户组永不过期
        $days = $minday ? $days : 1;
        $price           = floatval($pricepday * $days);
    }
    //构建支付链接
    $requesturl = credit_payurl($pid, '', '', '', $price, $Bank);
    
    //入库
    DB::insert('rpancn_buy_user_group_record', array(
        'orderid' => $orderid,
        'status' => '1',
        'uid' => $_G['uid'],
        'uname' => $_G['username'],
        'gid' => $gid,
        'gname' => $groups['grouptitle'],
        'extcredit' => $extcredit,
        'extcredit_value' => $extcredit_value,
        'pricepday' => $pricepday,
        'days' => $minday ? $days : 0,
        'submitdate' => strip_tags($_G['timestamp']),
        'ip' => strip_tags($_G['clientip'])
    ));
    //print_r($requesturl);
    //die;exit;
    
    //跳转到支付页面
    if ($days >= $minday) {
        //grouprecommend_succeed-'操作成功'
        //location_login_succeed-''
		Header("Location:".$requesturl);
        showmessage(lang('plugin/rpancn_buy_usergroup', 'rpancn_submit') . '....', $requesturl, array(
            'group' => 'wait_redirect'
        ), array(
            'showdialog' => 2,
            'showmsg' => true,
            'locationtime' => true
        ));
    } else {
		showmessage(lang('plugin/rpancn_buy_usergroup', 'rpancn_pay_error_days') . $minday . lang('plugin/rpancn_buy_usergroup', 'rpancn_day'), 'home.php?mod=spacecp&ac=usergroup');
		die;exit;
        include template('common/header');
        echo lang('plugin/rpancn_buy_usergroup', 'rpancn_pay_error_days') . $minday . lang('plugin/rpancn_buy_usergroup', 'rpancn_day');
        include template('common/footer');
    }
}
?>