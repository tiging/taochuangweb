<?php
/**
 * invite.inc.php
 * User: aboc
 * Date: 14-9-1
 * Time: ÏÂÎç11:00
 */

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
$invite_aboc = $_G['cache']['plugin']['invite_aboc'];
$invite_aboc['ban_group_id'] = @unserialize($invite_aboc['ban_group_id']);
$invite_aboc['number'] = 20;
$invite_aboc['day'] = 0;
$do = isset($_GET['do']) ? trim($_GET['do']) : '';
$share_url = $_G['siteurl'] . '?fromuid=' . $_G['uid'];
if ($do == "") {
    $is_ban = $invite_aboc['ban_group_id']&&in_array($_G['member']['groupid'],$invite_aboc['ban_group_id'])?1:0;
    $upgrade_group = isset($_G['cache']['usergroups'][$invite_aboc['upgrade_group_id']]['grouptitle']) ? $_G['cache']['usergroups'][$invite_aboc['upgrade_group_id']]['grouptitle'] : '';
    $str = '';
    $copy = lang("home/template", "copy");
    $promotion_url_copied = lang("home/template", "promotion_url_copied");
    $invite_aboc['explain_1'] = lang('plugin/invite_aboc', 'url');
    for ($i = 1; $i <= 1; $i++) {
        if ($invite_aboc['explain_' . $i] != "") {
            $invite_aboc['explain_' . $i] = str_ireplace('{url}', $share_url, $invite_aboc['explain_' . $i]);
            $str .= <<<BBB
<div style="margin-bottom:5px;"><textarea rows="3" cols="86" onclick="this.select();setCopy('{$invite_aboc['explain_' . $i]}', '{$promotion_url_copied}');">{$invite_aboc['explain_' . $i]}</textarea>
<button type="submit" style="vertical-align:top;margin-top:2px;" class="pn vm" onclick="setCopy('{$invite_aboc['explain_' . $i]}', '{$promotion_url_copied}');"><em>{$copy}</em></button></div>
BBB;
        }
    }
}
elseif($do == 'list'){
    $page = isset($_GET['page'])?intval($_GET['page']):1;
    $num = 15;
    $where = " WHERE fromuid='{$_G['uid']}'";
    $total = DB::fetch_first("select count(a.uid) as num from " . DB::table("invite_aboc")." a " . $where);
    $total = isset($total['num']) ? $total['num'] : 0;
    $list = DB::fetch_all("select a.*,m.groupid from " . DB::table("invite_aboc") . " a LEFT JOIN ".DB::table("common_member")." m ON a.uid=m.uid  $where ORDER BY addtime DESC " . DB::limit(($page - 1) * $num, $num));
    foreach($list as $k => $v){
        if(isset($_G['cache']['usergroups'][$v['groupid']]['grouptitle'])){
            $list[$k]['group'] = $_G['cache']['usergroups'][$v['groupid']]['grouptitle'];
        } else {
            $list[$k]['group'] = lang('plugin/invite_aboc', 'wz');
        }
    }
    $pagelist = multi($total, $num, $page, "home.php?mod=spacecp&ac=plugin&id=invite_aboc:invite&do=list", ceil($total / $num),10, true);
    $invite_total = DB::fetch_first("select count(a.uid) as num from " . DB::table("invite_aboc")." a " . $where." AND status='1'");
}