<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); hookscriptoutput('spacecp');?>
<?php if($_GET['pluginop'] == 'share') { include template('common/header'); if($_GET['sh_type'] == 3) { ?>
<h3 class="flb">
<?php if($_GET['sh_type'] == 3) { ?>
<em id="return_<?php echo $_GET['handlekey'];?>">分享到QQ空间</em>
<?php } if($_G['inajax']) { ?>
<span><a href="javascript:void(0);" onclick="hideWindow('<?php echo $_GET['handlekey'];?>');" class="flbc" title="关闭">关闭</a></span>
<?php } ?>
</h3>
<form name="connect_share_form" id="connect_share_form" method="post" action="<?php echo $_GET['share_url'];?>" onsubmit="connect_share_submit();return false;">
<input type="hidden" name="formhash" value="<?php echo FORMHASH;?>" />
<?php if($_G['inajax']) { ?>
<input type="hidden" name="handlekey" value="<?php echo $_GET['handlekey'];?>" />
<?php } ?>

<div class="c" style="padding: 5px 15px;">
<p class="cl" style="padding-bottom: 5px;">
分享标题:
</p>
<input type="text" name="share_subject" id="share_subject" class="txt" style="padding:0;margin:0;width:450px;height:20px;font-size:12px;border-width:1px;border-style:solid;border-color:#707070 #CECECE #CECECE #707070;" />
</div>
<div class="ec cl">
<div id="connect_subject_tip" style="padding-left:15px;display:none;"></div>
</div>

<div class="c" style="padding: 5px 15px;">
<p class="cl">
分享理由:
</p>
<textarea name="reason" id="reason" class="pt mtn" style="padding:0;margin:0;width:450px;height:80px;font-size:12px;"></textarea>
</div>
<div class="ec cl">
<div id="connect_reason_tip" style="padding-left:15px;display:none;"></div>
</div>

<?php if($_GET['share_images']) { ?>
<div class="c" style="padding: 5px 15px;">
<p class="cl">
分享图片:
</p>
<div id="share_picture_list"><?php if(is_array($_GET['share_images'])) foreach($_GET['share_images'] as $image) { ?><div id="box_<?php echo $image['aid'];?>" class="share_picture_box">
<div class="badge" id="<?php echo $image['aid'];?>" onclick="connect_share_picture_select(this.id);"></div>
<a href="javascript:void(0);"><img class="share_picute_img" src="<?php echo $image['thumb'];?>" id="connect_share_picture_<?php echo $image['aid'];?>" rel="<?php echo $image['big'];?>" /></a>
</div>
<?php } ?>
</div>
</div>
<input type="hidden" name="attach_image" id="attach_image" value="" />
<input type="hidden" name="attach_image_id" id="attach_image_id" value="" />
<?php } ?>

<div class="ec cl" style="padding: 5px 15px; text-align: right; clear: both;"></div>

<p class="o pns">
<button type="submit" name="connect_share_button" id="connect_share_button" class="pn pnc" value="true"><strong>分享给好友</strong></button>
</p>
<input type="hidden" name="dialog_id" id="dialog_id" value="<?php echo $_GET['handlekey'];?>" />
<input type="hidden" name="thread_id" id="thread_id" value="" />
<input type="hidden" name="thread_url" id="thread_url" value="" />
<input type="hidden" name="post_id" id="post_id" value="" />
<input type="hidden" name="subject" id="subject" value="" />
<input type="hidden" name="html_content" id="html_content" value="" />
<input type="hidden" name="forum_id" id="forum_id" value="" />
<input type="hidden" name="author_id" id="author_id" value="" />
<input type="hidden" name="author" id="author" value="" />
<input type="hidden" name="sh_type" id="sh_type" value="<?php echo $_GET['sh_type'];?>" />
</form>
<script type="text/javascript" reload="1">
var connect_share_url = '<?php echo $_GET['share_url'];?>';
var threadInfo = connect_get_thread();
var connect_thread_url = threadInfo.thread_url;
var connect_reason_default = '可以在这里输入分享原因或详细内容';
<?php if($_GET['share_images']) { ?>
if (!document.getElementsByClassName) {
var img_list = getElementsByClassName('badge');
} else {
var img_list = document.getElementsByClassName('badge');
}
var sel_img_id = img_list[0].id;
var sel_img_url = $('connect_share_picture_' + img_list[0].id).getAttribute('rel');
addClass($("box_" + sel_img_id), 'select');
$('attach_image').value = sel_img_url;
$('attach_image_id').value = sel_img_id;
<?php } ?>

function connect_share_init() {
$('share_subject').value = threadInfo.subject.substr(0, 45);
$('subject').value = threadInfo.subject;
$('thread_url').value = connect_thread_url;

var connect_text_color = $('reason').style.color;
$('reason').value = connect_reason_default;
$('reason').style.color = '#999';
$('reason').onfocus = function () {
if (this.value == connect_reason_default) {
this.value = '';
this.style.color = connect_text_color;
}
}

$('reason').onblur = function () {
if (this.value == '') {
this.value = connect_reason_default;
this.style.color = '#999';
}
connect_check_reason();
}

$('reason').onkeyup = function () {
connect_check_reason();
}

$('share_subject').onfocus = function() {
$('share_subject').style.border = "1px solid #6FB1DF";
$('share_subject').style.MozBoxShadow = "0 0 5px #6FB1DF";
}

$('share_subject').focus();

$('share_subject').onblur = function () {
$('share_subject').style.borderColor = "#707070 #CECECE #CECECE #707070";
$('share_subject').style.borderWidth = "1px";
$('share_subject').style.borderStyle = "solid";
$('share_subject').style.MozBoxShadow = "";
connect_check_subject();
}

$('share_subject').onkeyup = function () {
connect_check_subject();
}
}

function connect_share_picture_select(sel_id) {
            for (var i = 0; i < img_list.length; i++) {
                $('box_' + img_list[i].id).className = "share_picture_box";
                if (img_list[i].id == sel_id) {
                    if ($('attach_image_id').value == sel_id) {
                        $('attach_image').value = '';
                        removeClass($('box_' + sel_id), 'select');
                        $('attach_image_id').value = '';
                    } else {
                        $('attach_image').value = $('connect_share_picture_' + img_list[i].id).getAttribute('rel');
                        $('attach_image_id').value = sel_id;
                        addClass($('box_' + sel_id), 'select');
                    }
                }
            }
        }

function getElementsByClassName(searchClass, node, tag) {
var classElements = new Array();
if (node == null) {
node = document;
}
if (tag == null) {
tag = '*';
}
var els = node.getElementsByTagName(tag);
var elsLen = els.length;
var pattern = new RegExp("(^|\\s)"+searchClass+"(\\s|$)");
for (i = 0, j = 0; i < elsLen; i++) {
if ( pattern.test(els[i].className) ) {
classElements[j] = els[i];
j++;
}
}
return classElements;
}

function hasClass(ele, cls) {
return ele.className.match(new RegExp('(\\s|^)'+cls+'(\\s|$)'));
}

function addClass(ele, cls) {
if (!this.hasClass(ele, cls)) ele.className += " " + cls;
}

function removeClass(ele, cls) {
if (hasClass(ele, cls)) {
var reg = new RegExp('(\\s|^)' + cls + '(\\s|$)');
ele.className = ele.className.replace(reg, ' ');
}
}

function connect_error_tip(obj, msg) {
$(obj).innerHTML = msg;
$(obj).style.color = "red";
$(obj).style.display = "";
}

function connect_check_subject() {
var share_subject = $('share_subject').value;
share_subject = share_subject.replace(new RegExp("　","gm"),"");

if (share_subject == '') {
connect_error_tip('connect_subject_tip', '请输入分享标题');
return false;
} else {
$('connect_subject_tip').style.display = "none";
}

if (share_subject.length > 45) {
connect_error_tip('connect_subject_tip', '分享标题超过了长度限制');
return false;
} else {
$('connect_subject_tip').style.display = "none";
}
return true;
}

function connect_check_reason() {
var reason= $('reason').value;
if (reason.length > 200) {
connect_error_tip('connect_reason_tip', '分享理由超过了长度限制');
return false;
} else {
$('connect_reason_tip').style.display = "none";
}
return true;
}

function connect_share_submit(){
if (!connect_check_subject()) {
return false;
}
if (!connect_check_reason()) {
return false;
}

var reason= $('reason').value;
if (reason == connect_reason_default) {
$('reason').value = '';
}
<?php if($_GET['share_images']) { ?>
var selected_images = new Array();
for (var i = 0; i < img_list.length; i++) {
if (hasClass($('box_' + img_list[i].id), 'select')) {
selected_images.push($('connect_share_picture_' + img_list[i].id).getAttribute('rel'));
}
}
$('attach_image').value = selected_images.join('|');
<?php } ?>
$('thread_id').value = threadInfo.thread_id;
$('post_id').value = threadInfo.post_id;
$('html_content').value = threadInfo.html_content;
$('forum_id').value = threadInfo.forum_id;
$('author_id').value = threadInfo.author_id;
$('author').value = threadInfo.author;

ajaxpost('connect_share_form', 'return_<?php echo $_GET['handlekey'];?>', null, null, null, null);
return false;
}

safescript('weibosharejs', connect_share_init(), 1000, 5);
</script>

<?php } else { ?>

<h3 class="flb">
<em id="return_<?php echo $_GET['handlekey'];?>">转播到腾讯微博</em>
<?php if($_G['inajax']) { ?>
<span><a href="javascript:void(0);" onclick="hideWindow('<?php echo $_GET['handlekey'];?>');" class="flbc" title="关闭">关闭</a></span>
<?php } ?>
</h3>
<form name="connect_share_form" id="connect_share_form" method="post" action="<?php echo $_GET['share_url'];?>" onsubmit="connect_share_submit();return false;">
<input type="hidden" name="formhash" value="<?php echo FORMHASH;?>" />
<?php if($_G['inajax']) { ?>
<input type="hidden" name="handlekey" value="<?php echo $_GET['handlekey'];?>" />
<?php } ?>

<div class="c" style="padding: 5px 15px;">
<p class="cl">
<span class="y xg1" id="txtlength">还能输入<strong id="checklen"></strong>字</span>
微博内容:
</p>
<textarea name="reason" id="reason" class="pt mtn" style="padding:0;margin:0;width:450px;height:80px;font-size:12px;"></textarea>
</div>
<div class="ec cl">
<div id="connect_reason_tip" style="padding-left:15px;color:red;display:none;"></div>
</div>

<?php if($_GET['share_images']) { ?>
<div class="ec cl" id="share_picture_title">
分享图片:
</div>
<div class="c" style="padding: 0 15px;display: block;" id="share_picture_content">
<div id="share_picture_list"><?php if(is_array($_GET['share_images'])) foreach($_GET['share_images'] as $image) { ?><div id="box_<?php echo $image['aid'];?>" class="share_picture_box">
<div class="badge" id="<?php echo $image['aid'];?>" onclick="connect_share_picture_select(this.id);"></div>
<a href="javascript:void(0);"><img class="share_picute_img" src="<?php echo $image['thumb'];?>" id="connect_share_picture_<?php echo $image['aid'];?>" rel="<?php echo $image['big'];?>" /></a>
</div>
<?php } ?>
</div>
</div>
<input type="hidden" name="attach_image" id="attach_image" value="" />
<input type="hidden" name="attach_image_id" id="attach_image_id" value="" />
<?php } ?>

<div class="ec cl" style="padding: 5px 15px; text-align: right; clear: both;"></div>

<p class="o pns">
<button type="submit" name="connect_share_button" id="connect_share_button" class="pn pnc" value="true"><strong>转播</strong></button>
</p>
<input type="hidden" name="dialog_id" id="dialog_id" value="<?php echo $_GET['handlekey'];?>" />
<input type="hidden" name="thread_id" id="thread_id" value="" />
<input type="hidden" name="post_id" id="post_id" value="" />
<input type="hidden" name="share_subject" id="share_subject" value="" />
<input type="hidden" name="subject" id="subject" value="" />
<input type="hidden" name="html_content" id="html_content" value="" />
<input type="hidden" name="forum_id" id="forum_id" value="" />
<input type="hidden" name="author_id" id="author_id" value="" />
<input type="hidden" name="author" id="author" value="" />
<input type="hidden" name="sh_type" id="sh_type" value="2" />
</form>
<script type="text/javascript" reload="1">
var connect_share_url = '<?php echo $_GET['share_url'];?>';
var threadInfo = connect_get_thread();
var connect_thread_url = threadInfo.thread_url;

<?php if($_GET['share_images']) { ?>
if (!document.getElementsByClassName) {
var img_list = getElementsByClassName('badge');
} else {
var img_list = document.getElementsByClassName('badge');
}
<?php } ?>

function connect_share_init() {
$('subject').value = threadInfo.subject;
$('share_subject').value = threadInfo.subject.substr(0, 45);

                        var connect_reason_default = '<?php echo $share_message;?>' + '\n' + connect_thread_url;

$('reason').value = connect_reason_default;
var urllength = connect_url_filter(connect_reason_default);
var textlength = 140 - connect_reason_default.length;
textlength = textlength + urllength;
$('checklen').innerHTML = textlength;

$('reason').onblur = function () {
connect_check_reason();
}

$('reason').onkeyup = function() {
var str = $('reason').value;
var sl = connect_smart_length(str);
var l = 140 - sl;
if(l < 0) {
$('txtlength').innerHTML = '超出<strong id="checklen" style="color:#E56C0A;"></strong>字';
$('checklen').innerHTML = sl-140;
//$("connect_share_button").setAttribute('disabled','true');
} else {
$('txtlength').innerHTML = '还能输入<strong id="checklen"></strong>字';
$('checklen').innerHTML = l;
//$('connect_share_button').setAttribute('disabled','true');
}
connect_check_reason();
}

<?php if($_GET['share_images']) { ?>
var sel_img_id = img_list[0].id;
var sel_img_url = $('connect_share_picture_' + img_list[0].id).getAttribute('rel');
addClass($("box_" + sel_img_id), 'select');
$('attach_image').value = sel_img_url;
$('attach_image_id').value = sel_img_id;
<?php } ?>
}

function connect_share_picture_select(sel_id) {
for (var i = 0; i < img_list.length; i++) {
$('box_' + img_list[i].id).className = "share_picture_box";
if (img_list[i].id == sel_id) {
if ($('attach_image_id').value == sel_id) {
$('attach_image').value = '';
removeClass($('box_' + sel_id), 'select');
$('attach_image_id').value = '';
} else {
$('attach_image').value = $('connect_share_picture_' + img_list[i].id).getAttribute('rel');
$('attach_image_id').value = sel_id;
addClass($('box_' + sel_id), 'select');
}
}
}
}

function getElementsByClassName(searchClass, node, tag) {
var classElements = new Array();
if (node == null) {
node = document;
}
if (tag == null) {
tag = '*';
}
var els = node.getElementsByTagName(tag);
var elsLen = els.length;
var pattern = new RegExp("(^|\\s)"+searchClass+"(\\s|$)");
for (i = 0, j = 0; i < elsLen; i++) {
if ( pattern.test(els[i].className) ) {
classElements[j] = els[i];
j++;
}
}
return classElements;
}

function hasClass(ele, cls) {
return ele.className.match(new RegExp('(\\s|^)' + cls + '(\\s|$)'));
}

function addClass(ele, cls) {
if (!this.hasClass(ele, cls)) ele.className += " " + cls;
}

function removeClass(ele, cls) {
if (hasClass(ele, cls)) {
var reg = new RegExp('(\\s|^)' + cls + '(\\s|$)');
ele.className = ele.className.replace(reg, ' ');
}
}

function connect_trim(str) {
str = str.replace(/^\s+/, '');
for (var i = str.length - 1; i >= 0; i--) {
if (/\S/.test(str.charAt(i))) {
str = str.substring(0, i + 1);
break;
}
}
return str;
}

function connect_url_filter(objTxt) {
var urlpatt = new RegExp("((news|telnet|nttp|file|http|ftp|https)://){1}(([-A-Za-z0-9]+(\\.[-A-Za-z0-9]+)*(\\.[-A-Za-z]{2,5}))|([0-9]{1,3}(\\.[0-9]{1,3}){3}))(:[0-9]*)?(/[-A-Za-z0-9_\\$\\.\\+\\!\\*\\(\\),;:@&=\\?/~\\#\\%]*)*","gi");
var objArray = objTxt.split(/\s/g);
var urllength = 0;
for (var i = 0; i < objArray.length; i++) {
var result = objArray[i].match(urlpatt);
if (result !== null) {
urllength += result.toString().length - 11;
}
}
return urllength;
}

function connect_smart_length(str) {
str = str.replace(new RegExp("((news|telnet|nttp|file|http|ftp|https)://){1}(([-A-Za-z0-9]+(\\.[-A-Za-z0-9]+)*(\\.[-A-Za-z]{2,5}))|([0-9]{1,3}(\\.[0-9]{1,3}){3}))(:[0-9]*)?(/[-A-Za-z0-9_\\$\\.\\+\\!\\*\\(\\),;:@&=\\?/~\\#\\%]*)*","gi"), 'AAAAAAAAAAAAAAAAAAAAAA');
return Math.ceil((connect_trim(str.replace(/[^\u0000-\u00ff]/g,"aa")).length)/2);
};

function connect_smart_cut(str, maxlen) {
if (connect_smart_length(str) <= maxlen) {
return str;
} else {
for (var i = 0, l = str.length; i < l; i++) {
var temp = str.substr(0, i);
    if (connect_smart_length(temp) >= maxlen) {
    return temp;
}
}
return str;
}
}

function connect_error_tip(obj, msg) {
$(obj).innerHTML = msg;
$(obj).style.display = "";
}

function connect_check_reason() {
var str = $('reason').value;
var sl = connect_smart_length(str);
var l = 140 - sl;
if (l < 0) {
connect_error_tip('connect_reason_tip', '微博内容超过了长度限制');
return false;
} else if (l >= 140) {
connect_error_tip('connect_reason_tip', '请输入您要转播的微博内容');
return false;
} else {
$('connect_reason_tip').style.display = "none";
}
return true;
}

function connect_share_submit(form_id) {
if (!connect_check_reason()) {
return false;
}
var reason= $('reason').value;

$('thread_id').value = threadInfo.thread_id;
$('post_id').value = threadInfo.post_id;
$('html_content').value = threadInfo.html_content;
$('forum_id').value = threadInfo.forum_id;
$('author_id').value = threadInfo.author_id;
$('author').value = threadInfo.author;

ajaxpost('connect_share_form', 'return_<?php echo $_GET['handlekey'];?>', null, null, null, null);
return false;
}

safescript('weibosharejs', connect_share_init(), 1000, 5);
</script>
<?php } include template('common/footer'); } elseif($_GET['pluginop'] == 'new') { include template('common/header'); ?><script type="text/javascript" reload="1">

var code = "<?php echo $code;?>";
var message = "<?php echo $message;?>";
var dialog_id = "<?php echo $dialog_id;?>";
hideWindow(dialog_id);

if (code > 0) {
showDialog(message, 'notice', null, null, 0);
} else {
showDialog(message, 'right', null, null, 0);
}
</script><?php include template('common/footer'); } else { if($_G['member']['conisbind']) { ?>
<p class="pbm bbda xi1">您已将本站帐号 <strong><?php echo $_G['member']['username'];?></strong> 与QQ号码绑定</p>
<?php if($_G['member']['is_feed']) { ?>
<form action="connect.php?mod=config" method="post" autocomplete="off" class="mbw bbda">
<input type="hidden" name="formhash" value="<?php echo FORMHASH;?>">
<table cellspacing="0" cellpadding="0" class="tfm">
<tr>
<th>绑定设置</th>
<td>
<label for="ispublisht"><input type="checkbox" id="ispublisht" name="ispublisht" class="pc" value="1" <?php if($_G['member']['conispublisht']) { ?>checked="checked"<?php } ?> /> 发表主题和回复时默认转播到腾讯微博</label>
</td>
</tr>
<tr>
<th></th>
<td>
<input type="hidden" name="op" value="config"/>
<button type="submit" name="connectsubmit" value="yes" class="pn pnc"><strong>保存设置</strong></button>
</td>
</tr>
</table>
</form>
<?php } else { ?>
<br />
<?php } if($_G['member']['conisregister']) { ?>
<h2>
<a href="home.php?mod=spacecp&amp;ac=profile&amp;op=password" class="xi2">为单独登录站点创建登录密码</a>
</h2>
<br />
<?php } ?>

<h2>
<a href="javascript:;" onclick="display('unbind');<?php if($_G['member']['conisregister']) { ?>$('newpassword1').focus();<?php } ?>" class="xi2">解除已绑定帐号？</a>
</h2>

<?php if($_G['member']['conisregister']) { ?>
<div id="unbind" style="display:none;">
<form action="connect.php?mod=config" method="post" autocomplete="off">
<input type="hidden" name="formhash" value="<?php echo FORMHASH;?>">
<div class="ptm pbm">
<p>您当前是使用QQ帐号绑定到 <?php echo $_G['setting']['bbname'];?></p>
<p>为了您可以继续使用本站帐号 <strong><?php echo $_G['member']['username'];?></strong>，请设置本站登录密码</p>
</div>
<div class="password">
<table cellspacing="0" cellpadding="0" class="tfm">
<tr>
<th>新密码</th>
<td><input type="password" size="25" name="newpassword1" id="newpassword1" class="px" value="" /><em class="d">登录本站使用</em></td>
</tr>
<tr>
<th>确认密码</th>
<td><input type="password" size="25" name="newpassword2" id="newpassword2" class="px" value="" /></td>
</tr>
<tr>
<th></th>
<td>
<input type="hidden" name="op" value="unbind"/>
<button type="submit" name="connectsubmit" value="yes" class="pn pnc"><strong>确认解除</strong></button>
</td>
</tr>
</table>
</div>
</form>
</div>
<?php } else { ?>
<div id="unbind" style="display:none;">
<form action="connect.php?mod=config" method="post" autocomplete="off">
<input type="hidden" name="formhash" value="<?php echo FORMHASH;?>">
<p class="mtm mbm">
您确认要解除与本站帐号的绑定关系？
</p>
<div>
<input type="hidden" name="op" value="unbind"/>
<button type="submit" name="connectsubmit" value="yes" class="pn pnc"><strong>确认解除</strong></button>
</div>
</form>
</div>
<?php } } else { ?>
<div class="mtw bm2 cl">
<div class="bm2_b bw0 hm" style="padding-top: 70px;">
<a href="<?php echo $_G['connect']['loginbind_url'];?>"><img src="<?php echo IMGDIR;?>/qq_bind.gif" /></a>
<p class="mtn xg1">点击按钮，立刻绑定QQ帐号</p>
</div>
<div class="bm2_b bm2_b_y bw0">
<dl class="xld">
<h2 class="xi1 xs2">使用QQ帐号绑定本站，您可以...</h2>
<dt>用QQ帐号轻松登录</dt>
<dd class="xg1">无需记住本站的帐号和密码，随时使用QQ帐号密码轻松登录</dd>
<dt>发表主题同时推送到腾讯微博</dt>
<dd class="xg1">将您的论坛主题推送到腾讯微博，让好友全方面了解您</dd>
<dt>把本站精彩内容分享到QQ空间、腾讯微博和腾讯朋友</dt>
<dd class="xg1">每一个精彩内容绝不放过，简单快捷的将论坛主题分享给好友和听众</dd>
</dl>
</div>
</div>
<?php } } ?>
