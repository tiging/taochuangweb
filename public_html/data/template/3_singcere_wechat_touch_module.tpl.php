<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); function tpl_post_editorctrl_left() {
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
 + '<h3 class="flb"><em>' + 4444 + '</em><span><a onclick="hideMenu(\'\', \'win\');return false;" class="flbc" href="javascript:;">???</a></span></h3><div class="c">' + 123 + '</div>'
 + '<p class="o pns"><button type="submit" id="' + ctrlid + '_submit" class="pn pnc"><strong>??</strong></button></p>'
 + '</td><td class="m_r"></td></tr><tr><td class="b_l"></td><td class="b_c"></td><td class="b_r"></td></tr></table>'; */
//} else {
s = '<div class="p_opt cl"><span class="y" style="margin:-10px -10px 0 0"><a onclick="hideMenu();return false;" class="flbc" href="javascript:;">???</a></span><div>???  ????'  + '</div><div class="pns mtn"><button type="submit" id="' + ctrlid + '_submit" class="pn pnc"><strong>??</strong></button></div></div>';
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

function tpl_global_footer() {
global $_G, $editorid;?><?php
$return = <<<EOF

<div id="J_bottomSection" class="bottomSection J_bottomSection none" style="position:fixed;">
<div class="bottomSectionWrap bottomView1">
<b id="bottomSectionClose" class="bottomSectionClose J_bottomSectionClose clkstat"></b>
<a class="dl clkstat" id="dl2" href="{$_G['singcere_wechat']['setting']['wechat_subscribe_url']}"> <i class="iopen">立即关注</i> <i class="adLogo"><img src="{$_G['singcere_wechat']['setting']['wechat_shareicon']}">"></i>
                <span class="fnt">
                    <span class="tit">{$_G['setting']['bbname']} 公众号</span>
                    <span class="char">不关注，同学聚会怎么吐槽？</span>
                </span>
</a>
</div>
</div>


EOF;
 if(!$_G['member']['subscribe'] && $_G['basescript'] !='member' && !$_G['cookie']['disable_wechat_focus']) { 
$return .= <<<EOF

<script type="text/javascript">
var hidefocus = false;
$(window).bind('scroll orientationchange', function(e) {
if($(this).scrollTop() > 45) {
if(!hidefocus && $("#J_bottomSection").is(':hidden')) {
$("#J_bottomSection").slideDown(500);
}
} else {
$("#J_bottomSection").hide();
}
});
$("#bottomSectionClose").click(function() {
hidefocus = true;
$("#J_bottomSection").fadeOut(500);
setcookie('disable_wechat_focus', 1, 3600);
});

</script>


EOF;
 } 
$return .= <<<EOF


EOF;
?><?php return $return;?><?php }


function tpl_login_bottom_wechat() {
global $_G, $editorid;?><?php
$return = <<<EOF

<div><a href="{$_G['singcere_wechat']['login_url']}" class="btn_wxlogin">点此使用微信帐号一键登录！</a></div>

EOF;
?><?php return $return;?><?php }

function singcere_wechat_jssdk() {
global $_G, $js_config;?><?php
$__FORMHASH = FORMHASH;$return = <<<EOF

<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript"></script>
<script src="source/plugin/singcere_wechat/template/static/wechat.js" type="text/javascript"></script>
<script type="text/javascript">
$.ajax({
url: "{$_G['siteurl']}plugin.php?id=singcere_wechat&op=js_signature&signaturesubmit=true",
type: 'post',
dataType: 'json',
data: {
url: document.location.href,
formhash: "{$__FORMHASH}",
}
}).success(function(js_config) {
wx.config({
appId: '{$_G['singcere_wechat']['setting']['wechat_appId']}',
timestamp: js_config['timestamp'],
nonceStr: js_config['noncestr'],
signature: js_config['signature'],
jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage', 'onMenuShareQQ', 'onMenuShareWeibo', 'previewImage','openLocation','getLocation'] // ???????????JS????б?
});


EOF;
 if($_G['member']['groupid'] == 1 && $_GET['debug']) { 
$return .= <<<EOF

wx.error(function(res){
alert(res.errMsg);
});

EOF;
 } 
$return .= <<<EOF

});
</script>


EOF;
?><?php return $return;?><?php }

function singcere_wechat_jsfooter() {
global $_G, $wxData;?><?php
$return = <<<EOF

<script type="text/javascript">
wxData = {title: '{$wxData['title']}', link: document.location.href, imgUrl: '{$wxData['imgUrl']}', desc: '{$wxData['desc']}'};
var urls = [];
imglist = document.querySelectorAll('.wxpreview');
for(var i = 0; i < imglist.length; i++) {
urls.push(imglist[i].getAttribute('data-wcp'));
imglist[i].onclick = function() {
wx.previewImage({
current: this.getAttribute('data-wcp'),
urls: urls
});
}
}
wx.ready(function(){
wx.showOptionMenu();
wx.onMenuShareTimeline(wxData);
wx.onMenuShareAppMessage(wxData);
wx.onMenuShareQQ(wxData);
wx.onMenuShareWeibo(wxData);
});
</script>

EOF;
?><?php return $return;?><?php }

function tpl_reward_pannel(){
global $_G, $tips, $rewardlist, $count;?><?php
$return = <<<EOF

<div class="reward_area tc" id="js_reward_area" style="display: block; ">
<p class="tips_global reward_tips">
{$tips}
</p>
<p>
<a class="reward_access" id="js_reward_link" href="plugin.php?id=singcere_wechat:reward&amp;ac=rewardpay&amp;tid={$_G['tid']}" style="background-color:{$_G['singcere_wechat']['setting']['reward_color']};">
赞赏
</a>
</p>
<div id="js_reward_inner" class="reward_area_inner" style="width: 272px; display: block; ">
<p class="tips_global reward_user_tips">
<a href="javascript:;" id="js_reward_total">{$count}</a>人赞赏
</p>
<div id="js_reward_list" class="reward_user_list tl">
EOF;
 if(is_array($rewardlist)) foreach($rewardlist as $reward) { 
$return .= <<<EOF
            <span class="reward_user_avatar z">
<img src="{$_G['setting']['ucenterurl']}/avatar.php?uid={$reward['uid']}&size=small" >
            </span>

EOF;
 } 
$return .= <<<EOF

</div>
</div>
</div>

EOF;
?><?php return $return;?><?php }?>