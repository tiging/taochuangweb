<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); hookscriptoutput('space_notice');
0
|| checktplrefresh('./template/default/home/space_notice.htm', './template/default/home/space_prompt_nav.htm', 1491064929, 'diy', './data/template/1_diy_home_space_notice.tpl.php', './template/default', 'home/space_notice')
;?>
<?php $_G['home_tpl_titles'] = array('����');?><?php include template('common/header'); ?><div id="pt" class="bm cl">
<div class="z">
<a href="./" class="nvhm" title="��ҳ"><?php echo $_G['setting']['bbname'];?></a> <em>&rsaquo;</em>
<span>֪ͨ</span> <em>&rsaquo;</em>
<a href="home.php?mod=space&amp;do=notice">����</a>
</div>
</div>

<style id="diy_style" type="text/css"></style>
<div class="wp">
<!--[diy=diy1]--><div id="diy1" class="area"></div><!--[/diy]-->
</div>

<div id="ct" class="ct2_a wp cl">
<div class="mn">
<div class="bm bw0">
<h1 class="mt"><img alt="pm" src="<?php echo STATICURL;?>image/feed/nts.gif" class="vm" /> ����</h1>
<ul class="tb cl">
<li class="y"><a href="home.php?mod=spacecp&amp;ac=privacy&amp;op=filter" target="_blank" class="xi2">ɸѡ����</a></li>
<?php if($_G['notice_structure'][$view] && ($view == 'mypost' || $view == 'interactive')) { if(is_array($_G['notice_structure'][$view])) foreach($_G['notice_structure'][$view] as $subtype) { ?><li<?php echo $readtag[$subtype];?>><a href="home.php?mod=space&amp;do=notice&amp;view=<?php echo $view;?>&amp;type=<?php echo $subtype;?>"><?php echo lang('template', 'notice_'.$view.'_'.$subtype)?><?php if($_G['member']['newprompt_num'][$subtype]) { ?>(<?php echo $_G['member']['newprompt_num'][$subtype];?>)<?php } ?></a></li>
<?php } } else { ?>
<li class="a"><a href="home.php?mod=space&amp;do=notice&amp;view=<?php echo $view;?>"><?php echo lang('template', 'notice_'.$view)?></a></li>
<?php } ?>
</ul>

<?php if($view=='userapp') { ?>

<script type="text/javascript">
function manyou_add_userapp(hash, url) {
if(isUndefined(url)) {
$(hash).innerHTML = "<tr><td colspan=\"2\">�ɹ������˸���Ӧ����Ϣ</td></tr>";
} else {
$(hash).innerHTML = "<tr><td colspan=\"2\">�������������롭��</td></tr>";
}
var x = new Ajax();
x.get('home.php?mod=misc&ac=ajax&op=deluserapp&hash='+hash, function(s){
if(!isUndefined(url)) {
location.href = url;
}
});
}
</script>

<div class="ct_vw cl">
<div class="ct_vw_sd">
<ul class="mtw">
<?php if($list) { ?><li><a href="home.php?mod=space&amp;do=notice&amp;view=userapp">ȫ��Ӧ����Ϣ</a></li><?php } if(is_array($apparr)) foreach($apparr as $type => $val) { ?><li class="mtn">
<a href="home.php?mod=userapp&amp;id=<?php echo $val['0']['appid'];?>&amp;uid=<?php echo $space['uid'];?>" title="<?php echo $val['0']['typename'];?>"><img src="http://appicon.manyou.com/icons/<?php echo $val['0']['appid'];?>" alt="<?php echo $val['0']['typename'];?>" class="vm" /></a>
<a href="home.php?mod=space&amp;do=notice&amp;view=userapp&amp;type=<?php echo $val['0']['appid'];?>"> <?php echo count($val);?> �� <?php echo $val['0']['typename'];?> <?php if($val['0']['type']) { ?>����<?php } else { ?>����<?php } ?></a>
</li>
<?php } ?>
</ul>
</div>
<div class="ct_vw_mn">
<?php if($list) { if(is_array($list)) foreach($list as $key => $invite) { ?><h4 class="mtw mbm">
<a href="home.php?mod=space&amp;do=notice&amp;view=userapp&amp;op=del&amp;appid=<?php echo $invite['0']['appid'];?>" class="y xg1">���Ը�Ӧ�õ���������</a>
<a href="home.php?mod=userapp&amp;id=<?php echo $invite['0']['appid'];?>&amp;uid=<?php echo $space['uid'];?>" title="<?php echo $apparr[$invite['0']['appid']];?>"><img src="http://appicon.manyou.com/icons/<?php echo $invite['0']['appid'];?>" alt="<?php echo $apparr[$invite['0']['appid']];?>" class="vm" /></a>
���� <?php echo count($invite);?> �� <?php echo $invite['0']['typename'];?> <?php if($invite['0']['type']) { ?>����<?php } else { ?>����<?php } ?>
</h4>
<div class="xld xlda"><?php if(is_array($invite)) foreach($invite as $value) { ?><dl class="bbda cl">
<dd class="m avt mbn">
<a href="home.php?mod=space&amp;uid=<?php echo $value['fromuid'];?>"><?php echo avatar($value[fromuid],small);?></a>
</dd>
<dt id="<?php echo $value['hash'];?>">
<div class="xw0 xi3"><?php echo $value['myml'];?></div>
</dt>
</dl>
<?php } ?>
</div>
<?php } if($multi) { ?><div class="pgs cl"><?php echo $multi;?></div><?php } } else { ?>
<div class="emp">û���µ�Ӧ�����������</div>
<?php } ?>
</div>
</div>

<?php } else { if(empty($list)) { ?>
<div class="emp mtw ptw hm xs2">
<?php if($new == 1) { ?>
��ʱû�������ѣ�<a href="home.php?mod=space&amp;do=notice&amp;isread=1">��˲鿴�Ѷ�����</a>
<?php } else { ?>
��ʱû����������
<?php } ?>
</div>
<?php } ?>

<script type="text/javascript">

function deleteQueryNotice(uid, type) {
var dlObj = $(type + '_' + uid);
if(dlObj != null) {
var id = dlObj.getAttribute('notice');
var x = new Ajax();
x.get('home.php?mod=misc&ac=ajax&op=delnotice&inajax=1&id='+id, function(s){
dlObj.parentNode.removeChild(dlObj);
});
}
}

function errorhandle_pokeignore(msg, values) {
deleteQueryNotice(values['uid'], 'pokeQuery');
}
</script>

<?php if($list) { ?>
<div class="xld xlda">
<div class="nts"><?php if(is_array($list)) foreach($list as $key => $value) { ?><dl class="cl <?php if($key==1) { ?>bw0<?php } ?>" <?php echo $value['rowid'];?> notice="<?php echo $value['id'];?>">
<dd class="m avt mbn">
<?php if($value['authorid']) { ?>
<a href="home.php?mod=space&amp;uid=<?php echo $value['authorid'];?>"><?php echo avatar($value[authorid],small);?></a>
<?php } else { ?>
<img src="<?php echo IMGDIR;?>/systempm.png" alt="systempm" />
<?php } ?>
</dd>
<dt>
<a class="d b" href="home.php?mod=spacecp&amp;ac=common&amp;op=ignore&amp;authorid=<?php echo $value['authorid'];?>&amp;type=<?php echo $value['type'];?>&amp;handlekey=addfriendhk_<?php echo $value['authorid'];?>" id="a_note_<?php echo $value['id'];?>" onclick="showWindow(this.id, this.href, 'get', 0);" title="����">����</a>
<span class="xg1 xw0"><?php echo dgmdate($value[dateline], 'u');?></span>
</dt>
<dd class="ntc_body" style="<?php echo $value['style'];?>">
<?php echo $value['note'];?>
</dd>

<?php if($value['from_num']) { ?>
<dd class="xg1 xw0">���� <?php echo $value['from_num'];?> ����֪ͬͨ������</dd>
<?php } ?>

</dl>
<?php } ?>
</div>
</div>

<?php if($view!='userapp' && $space['notifications']) { ?>
<div class="mtm mbm"><a href="home.php?mod=space&amp;do=notice&amp;ignore=all">���� <?php echo $value['from_num'];?> ����֪ͬͨ������ <em>&rsaquo;</em></a></div>
<?php } if($multi) { ?><div class="pgs cl"><?php echo $multi;?></div><?php } } } ?>
</div>
</div>
<div class="appl"><div class="tbn">
<h2 class="mt bbda">֪ͨ</h2>
<ul>
<li <?php echo $opactives['pm'];?>><em class="notice_pm"></em><a href="home.php?mod=space&amp;do=pm">��Ϣ <?php if($newpmcount) { ?><strong class="xi1">(<?php echo $newpmcount;?>)</strong><?php } ?></a></li><?php if(is_array($_G['notice_structure'])) foreach($_G['notice_structure'] as $key => $type) { ?><li <?php echo $opactives[$key];?>><em class="notice_<?php echo $key;?>"></em><a href="home.php?mod=space&amp;do=notice&amp;view=<?php echo $key;?>"><?php echo lang('template', 'notice_'.$key)?><?php if($_G['member']['category_num'][$key]) { ?>(<?php echo $_G['member']['category_num'][$key];?>)<?php } ?></a></li>
<?php } if($_G['setting']['my_app_status']) { ?>
<li<?php echo $actives['userapp'];?>><em class="notice_userapp"></em><a href="home.php?mod=space&amp;do=notice&amp;view=userapp">Ӧ����Ϣ<?php if($mynotice) { ?>(<?php echo $mynotice;?>)<?php } ?></a></li>
<?php } ?>
</ul>
</div><div class="drag">
<!--[diy=diy2]--><div id="diy2" class="area"></div><!--[/diy]-->
</div>

</div>
</div>

<div class="wp mtn">
<!--[diy=diy3]--><div id="diy3" class="area"></div><!--[/diy]-->
</div><?php include template('common/footer'); ?>