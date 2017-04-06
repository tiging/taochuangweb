<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); function wechat_tpl_login_bar() {
global $_G;?><?php
$return = <<<EOF

<a 
EOF;
 if($_G['wechat']['setting']['wechat_qrtype']) { 
$return .= <<<EOF
href="plugin.php?id=wechat:login"
EOF;
 } else { 
$return .= <<<EOF
href="javascript:;" onclick="showWindow('wechat_bind', 'plugin.php?id=wechat:bind')"
EOF;
 } 
$return .= <<<EOF
><img src="source/plugin/wechat/image/wechat_login.png" class="vm" /></a>

EOF;
?><?php return $return;?><?php }

function wechat_tpl_login_extra_bar() {
global $_G;?><?php
$return = <<<EOF

<div class="fastlg_fm y" style="margin-right: 10px; padding-right: 10px">
<p><a 
EOF;
 if($_G['wechat']['setting']['wechat_qrtype']) { 
$return .= <<<EOF
href="plugin.php?id=wechat:login"
EOF;
 } else { 
$return .= <<<EOF
href="javascript:;" onclick="showWindow('wechat_bind', 'plugin.php?id=wechat:bind')"
EOF;
 } 
$return .= <<<EOF
><img src="source/plugin/wechat/image/wechat_login.png" class="vm" /></a></p>
<p class="hm xg1" style="padding-top: 2px;">{$_G['Plang']['lang_wechat_logintip']}</p>
</div>

EOF;
?><?php return $return;?><?php }

function wechat_tpl_user_bar() {
global $_G;?><?php
$return = <<<EOF

<span class="pipe">|</span><a 
EOF;
 if($_G['wechat']['setting']['wechat_qrtype'] && $_G['cookie']['qrauth']) { 
$return .= <<<EOF
href="plugin.php?id=wechat:login"
EOF;
 } else { 
$return .= <<<EOF
href="javascript:;" onclick="showWindow('wechat_bind', 'plugin.php?id=wechat:bind')"
EOF;
 } 
$return .= <<<EOF
><img src="source/plugin/wechat/image/wechat_bind.png" class="qq_bind" align="absmiddle" /></a>

EOF;
?><?php return $return;?><?php }

function wechat_tpl_float_qrcode($idstr = '') {
global $_G;?><?php
$__STATICURL = STATICURL;$return = <<<EOF


EOF;
 if($_G['basescript'] != 'userapp' && empty($_G['cookie']['wechatfqrc'])) { 
$return .= <<<EOF

<div id="wechat_float_qrcode" class="p_pop xg1" style="display:none;text-align:center;float:left;position:fixed;top:220px;z-index:100;margin-left: 2px;width:110px">
<p class="cl"><img class="y" style="cursor:pointer" onclick="display('wechat_float_qrcode');setcookie('wechatfqrc', 1, 86400)" src="{$__STATICURL}image/common/ad_close.gif"></p>
<img src="plugin.php?id=wechat:qrcode{$idstr}&amp;access=yes" width="98" />
<p>
EOF;
 if($_G['wechat']['setting']['wechat_float_text']) { 
$return .= <<<EOF
{$_G['wechat']['setting']['wechat_float_text']}
EOF;
 } else { 
$return .= <<<EOF
��΢��ɨһɨ<br />����Ӯ����
EOF;
 } 
$return .= <<<EOF
</p>
</div>
<script>
function wechat_qrcode(type) {
if(type && $('wechat_float_qrcode').style.display == 'none') {
return;
}
var qrleft = parseInt($('ft').clientWidth + parseInt(fetchOffset($('ft'))['left']));
$('wechat_float_qrcode').style.display = '';
if(qrleft + $('wechat_float_qrcode').clientWidth > document.documentElement.clientWidth) {
$('wechat_float_qrcode').style.cssFloat = 'right';
$('wechat_float_qrcode').style.left = 'auto';
$('wechat_float_qrcode').style.right = 0;
} else {
$('wechat_float_qrcode').style.cssFloat = 'left';
$('wechat_float_qrcode').style.left = (qrleft) + 'px';
$('wechat_float_qrcode').style.right = 'auto';
}
}
_attachEvent(window, 'scroll', function () { wechat_qrcode(1); })
_attachEvent(window, 'load', function() { wechat_qrcode(0); }, document);
</script>

EOF;
 } 
$return .= <<<EOF


EOF;
?><?php return $return;?><?php }

function wechat_tpl_share($isshow) {
global $_G;?><?php
$return = <<<EOF

<a href="javascript:;" onclick="showWindow('wechat_share', 'plugin.php?id=wechat:qrcode&threadqr={$_G['tid']}&access=yes&share=yes')"><i><img src="source/plugin/wechat/image/share.png" alt="΢��" />΢��</i></a>

EOF;
 if($isshow) { 
$return .= <<<EOF

<script>
if(document.getElementsByName('ijoin').length) {
if($('post_reply')) {
document.getElementsByName('ijoin')[0].onclick = function() {
$('post_reply').click();
}
} else {
document.getElementsByName('ijoin')[0].style.display = 'none';
}
}
if($('vfastpost')) {
$('vfastpost').style.display = 'none';
}
</script>

EOF;
 } 
$return .= <<<EOF




EOF;
?><?php return $return;?><?php }

function wechat_tpl_register() {
global $_G;?><?php
$return = <<<EOF

<div class="rfm pbm">��д������Ϣ���΢���ʺ�ע��</div>

EOF;
?><?php return $return;?><?php }

function wechatshowactivity_tpl_voters($post) {
global $_G;?><?php
$__STATICURL = STATICURL;$return = <<<EOF

<span class="pipe">|</span> <img src="{$__STATICURL}image/common/agree.gif" class="vm"> ���� <strong class="xi1">{$post['voters']}</strong> �˵���

EOF;
?><?php return $return;?><?php }

function wechatshowactivity_tpl_share($post) {
global $_G;?><?php
$return = <<<EOF

<p class="xs3 quote"><img src="source/plugin/wechat/image/share.png"> <a href="javascript:;" onclick="showWindow('wechat_share', 'plugin.php?id=wechat:qrcode&threadqr={$post['tid']}&pid={$post['pid']}&showactivity=yes&access=yes')">ͨ��΢��ɨ���ά��Ϊ����Ʊ</a></p>
<br /><br />

EOF;
?><?php return $return;?><?php }

function wechat_tpl_resourcepush() {
global $_G;?><?php
$return = <<<EOF

<a href="javascript:;" onclick="showWindow('wechat_resourcepush', 'plugin.php?id=wechat:resourcepush&tid={$_G['tid']}')">���͵�΢���ز�</a>

EOF;
?><?php return $return;?><?php }?>