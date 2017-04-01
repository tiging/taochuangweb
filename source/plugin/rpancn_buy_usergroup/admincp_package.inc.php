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

$rpancn_scriptlang = $scriptlang['rpancn_buy_usergroup'];
$url               = "action=plugins&operation=config&do=$pluginid&identifier=rpancn_buy_usergroup&pmod=admincp_package";

if (!submitcheck('cpsubmit')) {
    $group_packages = '';
    $query          = DB::query("SELECT * FROM " . DB::table('rpancn_buy_user_group_package') . " ORDER BY gid");
    while ($package_data = DB::fetch($query)) {
        $group_packages .= showtablerow('', array(
            'class="td25"',
            'class="td28"'
        ), array(
            "<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"$package_data[gid]\">",
            "<input type=\"text\" class=\"txt\" size=\"2\" name=\"package[$package_data[gid]][sort_id]\" value=\"$package_data[sort_id]\">",
            "<input type=\"text\" class=\"txt\" size=\"5\" name=\"package[$package_data[gid]][gid]\" value=\"$package_data[gid]\">",
            "<input type=\"text\" class=\"txt\" size=\"10\" name=\"package[$package_data[gid]][pricepday]\" value=\"$package_data[pricepday]\">" . $rpancn_scriptlang['rpancn_yuan'] . "",
            "<input type=\"text\" class=\"txt\" size=\"10\" name=\"package[$package_data[gid]][minday]\" value=\"$package_data[minday]\">" . $rpancn_scriptlang['rpancn_day'] . ""
            //暂不处理
            //"<input type=\"text\" class=\"txt\" size=\"10\" name=\"extcredit[$package_data[groupid]]\" value=\"$package_data[extcredit]\">",
            //"<input type=\"text\" class=\"txt\" size=\"10\" name=\"extcredit_value[package_data[groupid]]\" value=\"package_data[extcredit_value]\">",
        ), TRUE);
    }
    
    echo "
			<script type=\"text/JavaScript\">
				var rowtypedata = [
					[
						[1, '', 'td25'],
						[1, '<input type=\"text\" class=\"txt\" size=\"2\" name=\"newo_sort_id[]\" value=\"0\">', 'td28'],
						[1, '<input type=\"text\" class=\"txt\" size=\"15\" name=\"new_gid[]\">'],
						[1, '<input type=\"text\" class=\"txt\" size=\"15\" name=\"new_pricepday[]\">" . $rpancn_scriptlang['rpancn_yuan'] . "'],
						[1, '<input type=\"text\" class=\"txt\" size=\"15\" name=\"new_minday[]\">" . $rpancn_scriptlang['rpancn_day'] . "'],
						//[1, '<input type=\"text\" class=\"txt\" size=\"15\" name=\"new_extcredit[]\">'],
						//[1, '<input type=\"text\" class=\"txt\" size=\"15\" name=\"new_extcredit_value[]\">'],
					],
				];
			</script>
		";
    
    showformheader("plugins&operation=config&identifier=rpancn_buy_usergroup&pmod=admincp_package&submit=1");
    showtableheader('<strong>' . $rpancn_scriptlang['rpancn_admincp_copyright'] . '</strong>  <a href="admin.php?action=usergroups" hidefocus="true" class="tabon" target="_blank"><em onclick="menuNewwin(this)"></em><font color = red >' . $rpancn_scriptlang['rpancn_admincp_view'] . '</font></a><br>'.$rpancn_scriptlang['rpancn_admincp_minday'].'<font color = red >&#35774;&#32622;&#20026;&#48;&#34920;&#31034;&#27704;&#19981;&#36807;&#26399;</font>');
    showsubtitle(array(
        $rpancn_scriptlang['rpancn_admincp_delete'],
        $rpancn_scriptlang['rpancn_admincp_sort'],
        $rpancn_scriptlang['rpancn_admincp_gid'],
        $rpancn_scriptlang['rpancn_admincp_pricepday'],
        $rpancn_scriptlang['rpancn_admincp_minday']
        /*'奖励', ''*/
    ));
    echo $group_packages;
    echo '<tr><td></td><td colspan="6"><div><a href="#" onclick="addrow(this, 0)" class="addtr">' . $rpancn_scriptlang['rpancn_admincp_add'] . '</a></div></td></tr>';
    showsubmit('cpsubmit', 'submit', 'del');
    
    showtablefooter();
    showformfooter();
} else {
    if ($gids = dimplode($_GET['delete'])) {
        DB::query("DELETE FROM " . DB::table('rpancn_buy_user_group_package') . " WHERE gid=($gids)");
    }
    
    if (is_array($_GET['package'])) {
        foreach ($_GET['package'] as $id => $val) {
            DB::update('rpancn_buy_user_group_package', array(
                'sort_id' => $val['sort_id'],
                'gid' => $val['gid'],
                'pricepday' => $val['pricepday'],
                'minday' => $val['minday'],
                //'extcredit' => $_GET['extcredit'][$id],
                //'extcredit_value' => $_GET['extcredit_value'][$id],
            ), "gid='" . $val['gid'] . "'");
        }
    }
    
    if (is_array($_GET['new_gid'])) {
        foreach ($_GET['new_gid'] as $key => $value) {
            $new_gid = trim($value);
            if ($new_gid) {
                $query = DB::query("SELECT gid FROM " . DB::table('rpancn_buy_user_group_package') . " WHERE gid='$new_gid' LIMIT 1");
                if (DB::num_rows($query)) {
                    cpmsg($rpancn_scriptlang['rpancn_admincp_gid_repeat'], $url, 'error');
                }
                DB::insert('rpancn_buy_user_group_package', array(
                    'sort_id' => $_GET['new_sort_id'][$key],
                    'gid' => $_GET['new_gid'][$key],
                    'pricepday' => $_GET['new_pricepday'][$key],
                    'minday' => $_GET['new_minday'][$key]
                    //'extcredit' => $_GET['new_extcredit'][$key],
                    //'extcredit_value' => $_GET['new_extcredit_value'][$key],
                ));
            } else {
                cpmsg($rpancn_scriptlang['rpancn_admincp_gid_null'], $url, 'error');
            }
        }
    }
    $cacheechos    = array();
    $cacheechokeys = array();
    $querycache    = DB::query("SELECT * FROM " . DB::table('rpancn_buy_user_group_package') . " ORDER BY gid");
    while ($cacheecho = DB::fetch($querycache)) {
        $cacheechos[$cacheecho['gid']] = $cacheecho;
        $cacheechokeys[]               = $cacheecho['gid'];
        
    }
    C::t('common_setting')->update('rpancn_buy_user_group_package', $cacheechos);
    updatecache('setting');
    
    cpmsg($rpancn_scriptlang['rpancn_admincp_success'], $url, 'succeed');
    
}
?>