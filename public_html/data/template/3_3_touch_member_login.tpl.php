<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); hookscriptoutput('login');
0
|| checktplrefresh('./template/elecnation_x3touch_pro/touch/member/login.htm', './template/elecnation_x3touch_pro/touch/common/seccheck.htm', 1491790655, '3', './data/template/3_3_touch_member_login.tpl.php', './template/elecnation_x3touch_pro', 'touch/member/login')
;?><?php include template('common/header'); if(!$_GET['infloat']) { ?>

<!-- header start -->
<header class="header">
    <div class="nav">
        <div class="category">
        	��¼
        	<div id="elecnation_nav_left">
                <a href="javascript:;" onclick="history.go(-1)"><img src="<?php echo $_G['style']['styleimgdir'];?>/touch/common/images/icon_back.png" width="41" height="30" /></a>
        	</div>
        </div>
</div>
</header>
<!-- header end -->

<?php } $loginhash = 'L'.random(4);?><div class="wp">
<!-- userinfo start -->
<div class="loginbox <?php if($_GET['infloat']) { ?>login_pop<?php } ?>">
<?php if($_GET['infloat']) { ?>
<h2 class="log_tit"><a href="javascript:;" onclick="popup.close();"><span class="icon_close y">&nbsp;</span></a>��¼</h2>
<?php } ?>
<form id="loginform" method="post" action="member.php?mod=logging&amp;action=login&amp;loginsubmit=yes&amp;loginhash=<?php echo $loginhash;?>&amp;mobile=2" onsubmit="<?php if($_G['setting']['pwdsafety']) { ?>pwmd5('password3_<?php echo $loginhash;?>');<?php } ?>" >
<input type="hidden" name="formhash" id="formhash" value='<?php echo FORMHASH;?>' />
<input type="hidden" name="referer" id="referer" value="<?php if(dreferer()) { echo dreferer(); } else { ?>forum.php?mobile=2<?php } ?>" />
<input type="hidden" name="fastloginfield" value="username">
<input type="hidden" name="cookietime" value="2592000">
<div class="login_from">
<ul>
<li><input type="text" value="" tabindex="1" class="px p_fre" size="30" autocomplete="off" value="" name="username" placeholder="�������û���" fwin="login"></li>
<li><input type="password" tabindex="2" class="px p_fre" size="30" value="" name="password" placeholder="����" fwin="login"></li>
<li class="bl_none questionli">
<div class="login_select">
<span class="login-btn-inner">
<span class="login-btn-text">
<span class="span_question">��ȫ����(δ���������)</span>
</span>
<span class="icon-arrow">&nbsp;</span>
</span>
<select id="questionid_<?php echo $loginhash;?>" name="questionid" class="sel_list">
<option value="0" selected="selected">��ȫ����(δ���������)</option>
<option value="1">ĸ�׵�����</option>
<option value="2">үү������</option>
<option value="3">���׳����ĳ���</option>
<option value="4">������һλ��ʦ������</option>
<option value="5">�����˼�������ͺ�</option>
<option value="6">����ϲ���Ĳ͹�����</option>
<option value="7">��ʻִ�������λ����</option>
</select>
</div>
</li>
<li class="bl_none answerli" style="display:none;"><input type="text" name="answer" id="answer_<?php echo $loginhash;?>" class="px p_fre" size="30" placeholder="��"></li>
</ul>
<?php if($secqaacheck || $seccodecheck) { $sechash = 'S'.random(4);
$sectpl = !empty($sectpl) ? explode("<sec>", $sectpl) : array('<br />',': ','<br />','');
$secshow = !isset($secshow) ? 1 : $secshow;
$sectabindex = !isset($sectabindex) ? 1 : $sectabindex;
    $ran = random(5, 1);?><?php if($secqaacheck) { include libfile('function/seccode');
    $message = '';
$question = make_secqaa($sechash);
$secqaa = lang('core', 'secqaa_tips').$question;?><?php } ?><?php
$seccheckhtml = <<<EOF

<input name="sechash" type="hidden" value="{$sechash}" class="sechash" />

EOF;
 if($sectpl) { if($secqaacheck) { 
$seccheckhtml .= <<<EOF

<p>
        ��֤�ʴ�: 
        <span class="xg2">{$secqaa}</span>
        <input name="secanswer" id="secqaaverify_{$sechash}" type="text" class="txt" />
        </p>

EOF;
 } if($seccodecheck) { 
$seccheckhtml .= <<<EOF

<div class="sec_code vm">
<input type="text" class="txt px vm" style="ime-mode:disabled;width:60px;background:white;" autocomplete="off" value="" id="seccodeverify_{$sechash}" name="seccodeverify" placeholder="��֤��" fwin="seccode">
        <img src="misc.php?mod=seccode&amp;update={$ran}&amp;idhash={$sechash}&amp;mobile=2" class="seccodeimg"/>
</div>

EOF;
 } } 
$seccheckhtml .= <<<EOF


EOF;
?><?php unset($secshow);?><?php if(empty($secreturn)) { ?><?php echo $seccheckhtml;?><?php } ?>

<script type="text/javascript">
(function() {
$('.seccodeimg').on('click', function() {
$('#seccodeverify_<?php echo $sechash;?>').attr('value', '');
var tmprandom = 'S' + Math.floor(Math.random() * 1000);
$('.sechash').attr('value', tmprandom);
$(this).attr('src', 'misc.php?mod=seccode&update=<?php echo $ran;?>&idhash='+ tmprandom +'&mobile=2');
});
})();
</script>
<?php } ?>
</div>
<div class="btn_login"><button tabindex="3" value="true" name="submit" type="submit" class="formdialog pn pnc"><span>��¼</span></button></div>
</form>
<?php if($_G['setting']['connect']['allow'] && !$_G['setting']['bbclosed']) { ?>
<p>��ʹ��QQ��¼</p>
<div class="btn_qqlogin"><a href="<?php echo $_G['connect']['login_url'];?>&statfrom=login_simple">ʹ��QQ�ʺŵ�¼</a></div>
<?php } if($_G['setting']['regstatus']) { ?>
<p class="reg_link"><a href="member.php?mod=<?php echo $_G['setting']['regname'];?>">��û��ע�᣿</a></p>
<?php } ?>
</div>
<!-- userinfo end -->

<?php if($_G['setting']['pwdsafety']) { ?>
<script src="<?php echo $_G['setting']['jspath'];?>md5.js?<?php echo VERHASH;?>" type="text/javascript" reload="1"></script>
<?php } updatesession();?><script type="text/javascript">
(function() {
$(document).on('change', '.sel_list', function() {
var obj = $(this);
$('.span_question').text(obj.find('option:selected').text());
if(obj.val() == 0) {
$('.answerli').css('display', 'none');
$('.questionli').addClass('bl_none');
} else {
$('.answerli').css('display', 'block');
$('.questionli').removeClass('bl_none');
}
});
formdialog.init();
 })();
</script>
</div><?php include template('common/footer'); ?>