<?php
/**
 * admin_invite.inc.php
 * User: aboc
 * Date: 14-9-3
 * Time: ÏÂÎç11:13
 */
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}
loadcache("cache");

if(submitcheck('del','post')){
    if(!isset($_POST['delete']) || !is_array($_POST['delete'])){
        cpmsg_error(lang('plugin/invite_aboc', 'sc'));
    }
    $_POST['delete'] = array_map("intval",$_POST['delete']);
    DB::delete("invite_aboc","uid IN(".join(',',$_POST['delete']).")");
    cpmsg(lang('plugin/invite_aboc', 'sccg'));
}

$pagenum = 20;
$page = isset($_GET['page'])?intval($_GET['page']):1;
$where = " WHERE 1=1";
if(isset($_GET['fromuser']) && $_GET['fromuser']){
    $where .= " AND m2.username='{$_GET['fromuser']}'";
}
if(isset($_GET['username']) && $_GET['username']){
    $where .= " AND a.username='{$_GET['username']}'";
}
if(isset($_GET['addip']) && $_GET['addip']){
    $where .= " AND a.addip like '{$_GET['addip']}%'";
}
$total = DB::fetch_first("select count(a.uid) as num from " . DB::table("invite_aboc") . " a LEFT JOIN ".DB::table("common_member")." m ON a.uid=m.uid LEFT JOIN ".DB::table("common_member")." m2 ON m2.uid = a.fromuid". $where);
$total = isset($total['num']) ? $total['num'] : 0;
$list = DB::fetch_all("select a.*,m.groupid,m2.username as username2,m2.groupid as groupid2 from " . DB::table("invite_aboc") . " a LEFT JOIN ".DB::table("common_member")." m ON a.uid=m.uid LEFT JOIN ".DB::table("common_member")." m2 ON m2.uid = a.fromuid  $where ORDER BY addtime DESC " . DB::limit(($page - 1) * $pagenum, $pagenum));
$rows = DB::fetch_all('SELECT groupid,grouptitle FROM '.DB::table('common_usergroup'));
$group_list = array();
foreach($rows as $v){
    $group_list[$v['groupid']] = $v['grouptitle'];
}
foreach($list as $k => $v){
    if(isset($group_list[$v['groupid']])){
        $list[$k]['group'] = $group_list[$v['groupid']];
    } else {
        $list[$k]['group'] = lang('plugin/invite_aboc', 'wz');
    }
    if(isset($group_list[$v['groupid2']])){
        $list[$k]['group2'] = $group_list[$v['groupid2']];
    } else {
        $list[$k]['group2'] = lang('plugin/invite_aboc', 'wz');
    }
}
$pagelist = multi($total, $pagenum, $page, "admin.php?action=plugins&operation=config&do=$pluginid&identifier=invite_aboc&pmod=admin_invite",ceil($total/$pagenum), 10,true);//·ÖÒ³

showformheader("plugins&operation=config&do=$pluginid&identifier=invite_aboc&pmod=admin_invite","","sform","get");
?>
<form action="admin.php" method="get">
    <input name="action" value="plugins" type="hidden"/>
    <input name="operation" value="config" type="hidden"/>
    <input name="do" value="<?php echo $plguinid;?>" type="hidden"/>
    <input name="identifier" value="invite_aboc" type="hidden"/>
    <input name="pmod" value="admin_invite" type="hidden"/>
    <table class="tb tb2 ">
    <tbody>
    <tr class="hover">
        <td style="width:430px;">
            <?php echo lang('plugin/invite_aboc', 'ss'); ?>
            <?php echo lang('plugin/invite_aboc', 'by'); ?>:<input type="text" value="" name="username" style="width: 80px;" class="txt">
            <?php echo lang('plugin/invite_aboc', 'yq'); ?>:<input type="text" value="" name="fromuser" style="width: 80px;" class="txt">
            <?php echo lang('plugin/invite_aboc', 'addip'); ?>:<input type="text" value="" name="addip" style="width: 80px;" class="txt">
            <input type="hidden" name="formhash" value="<?php echo FORMHASH;?>"/>
            <input type="submit" name="do_action" value="<?php echo lang('plugin/invite_aboc', 'ss'); ?>" class="btn" />
        </td>
    </tr>
    </tbody>
</table>
<?php
showformfooter();

showformheader("plugins&operation=config&do=$pluginid&identifier=invite_aboc&pmod=admin_invite");
?>
<table class="tb tb2 ">
    <tbody>
    <tr class="header">
        <th style="width:20px;"></th>
        <th><?php echo lang('plugin/invite_aboc', 'uid'); ?></th>
        <th><?php echo lang('plugin/invite_aboc', 'username'); ?></th>
        <th><?php echo lang('plugin/invite_aboc', 'group'); ?></th>
        <th><?php echo lang('plugin/invite_aboc', 'addtime'); ?></th>
        <th><?php echo lang('plugin/invite_aboc', 'addip'); ?></th>
        <th><?php echo lang('plugin/invite_aboc', 'fromuser'); ?></th>
        <th><?php echo lang('plugin/invite_aboc', 'fromgroup'); ?></th>
        <th><?php echo lang('plugin/invite_aboc', 'status'); ?></th>
    </tr>
    <?php
    foreach($list as $v) {
        ?>
        <tr class="hover" id="reply_<?php echo $v['uid'];?>">
            <td>
                <input type="checkbox" value="<?php echo $v['uid'];?>" name="delete[]" class="checkbox">
            </td>
            <td><?php echo $v['uid'];?></td>
            <td><?php echo $v['username'];?></td>
            <td><?php echo $v['group'];?></td>
            <td><?php echo date("Y-m-d H:i:s",$v['addtime']);?></td>
            <td><?php echo $v['addip'];?></td>
            <td><?php echo $v['username2'];?></td>
            <td><?php echo $v['group2'];?></td>
            <td><?php echo $v['status']?lang('plugin/invite_aboc', 'good'):lang('plugin/invite_aboc', 'bad');?></td>
        </tr>
    <?php
    }
    ?>
    <tr><td class="td25">&nbsp;</td><td colspan="15"><div class="fixsel">
                <input type="hidden" name="formhash" value="<?php echo FORMHASH;?>" />
                <input type="submit" value="<?php echo lang('plugin/invite_aboc', 'delete'); ?>" name="del" id="submit_submit" class="btn">
            </div></td></tr>
    </tbody>
</table>
<?php
showformfooter();
?>
<table>
    <tbody>
    <tr>
        <td colspan="5">
            <div class="cuspages right">
                <?php echo $pagelist; ?>
            </div>
        </td>
    </tr>
    </tbody>
</table>