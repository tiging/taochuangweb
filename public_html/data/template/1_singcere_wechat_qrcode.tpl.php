<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); hookscriptoutput('qrcode');?><?php include template('common/header'); ?><div class="f_c">
<?php if($_G['uid'] && !$qrauth) { ?>
<h3 class="flb">
<em id="return_<?php echo $_GET['handlekey'];?>"><?php if($_G['uid']) { ?>绑定微信账号<?php } else { ?>微信账号登录<?php } ?></em>
<span>
<a href="javascript:;" class="flbc" onclick="hideWindow('<?php echo $_GET['handlekey'];?>')" title="关闭">关闭</a>
</span>
</h3>
<form id="confirmform" method="post" autocomplete="off" action="plugin.php?id=singcere_wechat:bind&amp;infloat=yes&amp;confirmsubmit=yes" onsubmit="ajaxpost('confirmform', 'return_<?php echo $_GET['handlekey'];?>', 'return_<?php echo $_GET['handlekey'];?>', 'onerror');return false;">
<div class="c cl">
<?php if(!empty($_GET['infloat'])) { ?><input type="hidden" name="handlekey" value="<?php echo $_GET['handlekey'];?>" /><?php } ?>
<input type="hidden" name="formhash" value="<?php echo FORMHASH;?>" />
请输入您的论坛登录密码以确认身份
<div class="rfm mtn">
<table>
<tr>
<th><label for="passwordconfirm">密码:</label></th>
<td><input type="password" id="passwordconfirm" name="passwordconfirm" size="30" class="px p_fre" tabindex="1" /></td>
<td class="tipcol"></td>
</tr>
</table> 
</div>
<div class="rfm mbw bw0">
<table width="100%">
<tr>
<th>&nbsp;</th>
<td>
<button class="pn pnc" type="submit" name="confirmsubmit" value="true" tabindex="1"><strong>提交</strong></button>
</td>
</tr>
</table>
</div> 
</div>
</form>
<?php } if(!$_G['uid'] || $_G['uid'] && $qrauth) { ?>
<h3 class="flb">
<em id="return_<?php echo $_GET['handlekey'];?>"><?php if($_G['uid']) { ?>绑定微信账号<?php } else { ?>微信账号登录<?php } ?></em>
<span>
<a href="javascript:;" class="flbc" onclick="clearTimeout(wechat_checkST);hideWindow('<?php echo $_GET['handlekey'];?>')" title="关闭">关闭</a>
</span>
</h3>
<div class="c" align='center'>
<img src="<?php echo $qrcodeurl;?>" width="250" height="250" />
<br />
<?php if($ticket) { ?>
<h1 class="xs1 xi1">微信扫一扫绑定/登录</h1>
<?php } else { ?>
请在扫描关注后输入以下数字码, 自动登录<br />
<h1 class="xs1 xi1"><?php echo $code;?></h1>
<?php } ?>
</div>
<?php } ?>
</div>


<script type="text/javascript">
<?php if(!$_G['uid'] || $_G['uid'] && $qrauth) { ?>
var wechat_checkST = null, wechat_checkCount = 0;
function wechat_checkstart() {
wechat_checkST = setTimeout(function () {wechat_check()}, 3500);
}
function wechat_check() {
var x = new Ajax();
x.get('plugin.php?id=singcere_wechat:bind&check=<?php echo $codeenc;?>', function(s, x) {
s = trim(s);
if(s != 'done') {
if(s == '1') {
wechat_checkstart();
}
wechat_checkCount++;
if(wechat_checkCount >= 30) {
clearTimeout(wechat_checkST);
hideWindow('<?php echo $_GET['handlekey'];?>');
}
} else {
clearTimeout(wechat_checkST);
location.href = location.href;
}
});
}
wechat_checkstart();
<?php } if($_G['uid'] && !$qrauth) { ?>
function succeedhandle_<?php echo $_GET['handlekey'];?>() {
hideWindow('<?php echo $_GET['handlekey'];?>');
showWindow('singcere_wechat_bind', 'plugin.php?id=singcere_wechat:bind');

}
<?php } ?>
</script><?php include template('common/footer'); ?>