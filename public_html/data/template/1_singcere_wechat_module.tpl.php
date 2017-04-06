<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); function singcere_wechat_tpl_login_bar() {
global $_G;?><?php
$return = <<<EOF

<a href="javascript:;" onclick="hideWindow('login');showWindow('singcere_wechat_bind', 'plugin.php?id=singcere_wechat:bind')"><img src="source/plugin/singcere_wechat/template/static/wechat_login.png" class="vm" /></a>

EOF;
?><?php return $return;?><?php }

function singcere_wechat_tpl_login_extra_bar($text) {
global $_G;?><?php
$return = <<<EOF


EOF;
 if($text) { 
$return .= <<<EOF

<a href="javascript:;" onclick="showWindow('singcere_wechat_bind', 'plugin.php?id=singcere_wechat:bind')"><img src="source/plugin/singcere_wechat/template/static/wechat_login.png" class="vm" /></a>

EOF;
 } else { 
$return .= <<<EOF

<div class="fastlg_fm y" style="margin-right: 10px; padding-right: 10px">
<p><a href="javascript:;" onclick="showWindow('singcere_wechat_bind', 'plugin.php?id=singcere_wechat:bind')"><img src="source/plugin/singcere_wechat/template/static/wechat_login.png" class="vm" /></a></p>
<p class="hm xg1" style="padding-top: 2px;"></p>
</div>

EOF;
 } 
$return .= <<<EOF


EOF;
?><?php return $return;?><?php }

function singcere_wechat_tpl_user_bar() {
global $_G;?><?php
$return = <<<EOF

<span class="pipe">|</span><a href="javascript:;" onclick="showWindow('singcere_wechat_bind', 'plugin.php?id=singcere_wechat:bind')"><img src="source/plugin/singcere_wechat/template/static/wechat_bind.png" class="qq_bind" align="absmiddle" /></a>

EOF;
?><?php return $return;?><?php }

function tpl_post_editorctrl_left() {
global $_G, $editorid;?><?php
$return = <<<EOF

<a id="wechat_auth" href="javascript:;" menupos="00" menuwidth="400" onclick="wechat_auth_show(this.id);"></a>
<script type="text/javascript">
function wechat_auth_show(ctrlid) {
var menu = document.createElement('div');
menu.id = ctrlid + '_menu';
menu.style.display = 'none';
menu.className = 'p_pof upf';
menu.style.width = '270px';
/* if(menupos == '00') {
menu.className = 'fwinmask';
s = '<table width="100%" cellpadding="0" cellspacing="0" class="fwin"><tr><td class="t_l"></td><td class="t_c"></td><td class="t_r"></td></tr><tr><td class="m_l">&nbsp;&nbsp;</td><td class="m_c">'
+ '<h3 class="flb"><em>' + 4444 + '</em><span><a onclick="hideMenu(\'\', \'win\');return false;" class="flbc" href="javascript:;">关闭</a></span></h3><div class="c">' + 123 + '</div>'
+ '<p class="o pns"><button type="submit" id="' + ctrlid + '_submit" class="pn pnc"><strong>提交</strong></button></p>'
+ '</td><td class="m_r"></td></tr><tr><td class="b_l"></td><td class="b_c"></td><td class="b_r"></td></tr></table>'; */
//} else {
s = '<div class="p_opt cl"><span class="y" style="margin:-10px -10px 0 0"><a onclick="hideMenu();return false;" class="flbc" href="javascript:;">关闭</a></span><div>全部  部分'  + '</div><div class="pns mtn"><button type="submit" id="' + ctrlid + '_submit" class="pn pnc"><strong>提交</strong></button></div></div>';
//}
menu.innerHTML = s;
$('{$editorid}_editortoolbar').appendChild(menu);
showMenu({'ctrlid':ctrlid,'mtype':'menu','evt':'click','duration':3,'cache':0,'drag':1,'pos':'43'});
}

function xxx() {
if(!str) {
str = '';
var first = $(ctrlid + '_param_1').value;
if($(ctrlid + '_param_2')) var second = $(ctrlid + '_param_2').value;
if($(ctrlid + '_param_3')) var third = $(ctrlid + '_param_3').value;
if((params == 1 && first) || (params == 2 && first && (haveSel || second)) || (params == 3 && first && second && (haveSel || third))) {
if(params == 1) {
str = first;
} else if(params == 2) {
str = haveSel ? selection : second;
opentag = '[' + tag + '=' + first + ']';
} else {
str = haveSel ? selection : third;
opentag = '[' + tag + '=' + first + ',' + second + ']';
}
insertText((opentag + str + closetag), strlen(opentag), strlen(closetag), true, sel);
}
}
}

</script>

EOF;
?><?php return $return;?><?php }

function tpl_reward_pannel(){
global $_G, $tips, $rewardlist;?><?php
$return = <<<EOF

<div class="rewardPanel" id="rewardPanel" style="border-left: 4px solid {$_G['singcere_wechat']['setting']['reward_color']};">
<p>{$tips}</p>
<a href="plugin.php?id=singcere_wechat:reward&amp;ac=rewardpay&amp;tid={$_G['tid']}" id="rewardpaybtn" onclick="ajaxmenu(this, 0, 0, 3, '00');"  mid="rewardprice"  class="btn btn-pay" style="border-color: {$_G['singcere_wechat']['setting']['reward_color']};background: {$_G['singcere_wechat']['setting']['reward_color']};">&#65509; 打赏支持 <span id="reward_wait"></span></a>
<div id="avatar-list" class="avatar-list"></div>
<div id="rewardqr_menu" class="p_pop" style="display: none"></div>
</div>


<script type="text/javascript">
function show_reward_user(id, page) {
ajaxget('plugin.php?id=singcere_wechat:reward&ac=rewarduser&tid={$_G['tid']}&page=' + page, id, id, '', '', function() {

});
}

function show_reward_tips(ctrlobj) {
if(!ctrlobj.id) {
ctrlobj.id = 'tip_' + Math.random();
}
menuid = ctrlobj.id + '_menu';
if(!$(menuid)) {
var div = document.createElement('div');
div.id = ctrlobj.id + '_menu';
div.className = 'rewardtip';
div.style.display = 'none';
div.innerHTML = '<div class="tip_horn arrow"></div><div class="tip_c">' + ctrlobj.getAttribute('tip') + '</div>';
$('append_parent').appendChild(div);
}
$(ctrlobj.id).onmouseout = function () { hideMenu('', 'prompt'); };
showMenu({'mtype':'prompt','ctrlid':ctrlobj.id,'pos':'12','duration':2,'zindex':JSMENU['zIndex']['prompt']});
}

show_reward_user('avatar-list', 1);
</script>

EOF;
?><?php return $return;?><?php }?>