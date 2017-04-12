<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); hookscriptoutput('viewthread');
0
|| checktplrefresh('./template/elecnation_x3touch_pro/touch/forum/viewthread.htm', './template/elecnation_x3touch_pro/touch/forum/forumdisplay_fastpost.htm', 1491791038, 'diy', './data/template/3_diy_touch_forum_viewthread.tpl.php', './template/elecnation_x3touch_pro', 'touch/forum/viewthread')
|| checktplrefresh('./template/elecnation_x3touch_pro/touch/forum/viewthread.htm', './template/elecnation_x3touch_pro/touch/common/seccheck.htm', 1491791038, 'diy', './data/template/3_diy_touch_forum_viewthread.tpl.php', './template/elecnation_x3touch_pro', 'touch/forum/viewthread')
;?>
<?php $_G[forum_thread][special] = 0;?><?php $threadsort = $threadsorts = null;?><?php include template('common/header'); ?><!-- header start -->
<header class="header">

<div class="nav">
    <div class="category">
    <?php if(!IS_ROBOT && !$_GET['authorid'] && !$_G['forum_thread']['archiveid']) { ?>
    <div id="elecnation_fav_menu">
    	<div id="elecnation_fav_float" class="y">
<a href="forum.php?mod=viewthread&amp;tid=<?php echo $_G['tid'];?>&amp;page=<?php echo $page;?>&amp;authorid=<?php echo $_G['forum_thread']['authorid'];?>" rel="nofollow" style="padding:6px 11px;">只看楼主</a>
</div>
                
<div id="elecnation_fav_float" class="z">
<div id="elecnation_fav_float_bg">看全部</div>
</div>
<div id="elecnation_clear"></div>
</div>
<?php } else { ?>
<div id="elecnation_fav_menu">
<div id="elecnation_fav_float" class="y">
<div id="elecnation_fav_float_bg">只看楼主</div>
</div>
<div id="elecnation_fav_float" class="z">
<a href="forum.php?mod=viewthread&amp;tid=<?php echo $_G['tid'];?>&amp;page=<?php echo $page;?>" rel="nofollow" style="padding:6px 11px;">看全部</a>
        </div>
<div id="elecnation_clear"></div>
</div>
    <?php } ?>
    
    <div id="elecnation_nav_left">
    <?php if($_GET['fromguid'] == 'hot') { ?>
        <?php if($_G['setting']['mobile']['mobilehotthread']) { ?>
    	<a href="forum.php?mod=guide&amp;view=hot&amp;page=<?php echo $_GET['page'];?>" class="z">
        <?php } else { ?>
        <a href="forum.php?mod=guide&amp;view=newthread&amp;page=<?php echo $_GET['page'];?>" class="z">
<?php } ?>
    <?php } else { ?>
    	<a href="forum.php?mod=forumdisplay&amp;fid=<?php echo $_G['fid'];?>&amp;<?php echo rawurldecode($_GET[extra]);?>" class="z">
    <?php } ?>
    	<img src="<?php echo $_G['style']['styleimgdir'];?>/touch/common/images/icon_back.png" width="41" height="30" /></a>
    </div> 
    
        <div id="elecnation_nav_right" class="icon_edit">
        <span class="y">
<button class="btn_pn btn_pn_elecnation" id="replyid"><span>回复</span></button>
</span>
        </div>
    </div>
</div>

</header>
<!-- header end -->

<div class="wp">
<?php if(!empty($_G['setting']['pluginhooks']['viewthread_top_mobile'])) echo $_G['setting']['pluginhooks']['viewthread_top_mobile'];?>
<!-- main postlist start -->
<div class="postlist">
<div id="elecnation_post_title">
        	<?php if($_G['forum_thread']['typeid'] && $_G['forum']['threadtypes']['types'][$_G['forum_thread']['typeid']]) { ?>
[<?php echo $_G['forum']['threadtypes']['types'][$_G['forum_thread']['typeid']];?>]
            <?php } ?>
            <?php if($threadsorts && $_G['forum_thread']['sortid']) { ?>
                [<?php echo $_G['forum']['threadsorts']['types'][$_G['forum_thread']['sortid']];?>]
<?php } ?>
<?php echo $_G['forum_thread']['subject'];?>
            <?php if($_G['forum_thread']['displayorder'] == -2) { ?> <span>(审核中)</span>
            <?php } elseif($_G['forum_thread']['displayorder'] == -3) { ?> <span>(已忽略)</span>
            <?php } elseif($_G['forum_thread']['displayorder'] == -4) { ?> <span>(草稿)</span>
            <?php } ?>
    </div>
    <?php if(is_array($postlist)) foreach($postlist as $post) { $needhiddenreply = ($hiddenreplies && $_G['uid'] != $post['authorid'] && $_G['uid'] != $_G['forum_thread']['authorid'] && !$post['first'] && !$_G['forum']['ismoderator']);?><?php if(!empty($_G['setting']['pluginhooks']['viewthread_posttop_mobile'][$postcount])) echo $_G['setting']['pluginhooks']['viewthread_posttop_mobile'][$postcount];?>
<div class="plc cl">
    <div id="elecnation_post_message">
<div id="elecnation_post_avatar">
        	<div id="elecnation_post_avatar_img">
        	<?php if($post['authorid'] && $post['username'] && !$post['anonymous']) { ?>
            	<a href="home.php?mod=space&amp;uid=<?php echo $post['authorid'];?>&amp;do=profile&amp;mobile=2" style="border:none;"><img src="<?php echo avatar($post[authorid], small, true);?>" width="32" height="32" style="border:none;" /></a>
            <?php } else { if(!$post['authorid']) { ?>
                <img src="uc_server/images/noavatar_small.gif" width="32" height="32"/>
<?php } elseif($post['authorid'] && $post['username'] && $post['anonymous']) { if($_G['forum']['ismoderator']) { ?>
                	<a href="home.php?mod=space&amp;uid=<?php echo $post['authorid'];?>&amp;do=profile&amp;mobile=2" style="border:none;" target="_blank"><img src="uc_server/images/noavatar_small.gif" width="32" height="32" style="border:none;"/></a>
               	 	<?php } else { ?>
                	<img src="uc_server/images/noavatar_small.gif" width="32" height="32"/>
                	<?php } } else { ?>
<img src="<?php echo avatar($post[authorid], small, true);?>" width="32" height="32"/>
<?php } } ?>
        	</div>
        </div>
       
       <div class="display pi" href="#replybtn_<?php echo $post['pid'];?>">
   <div style="margin:0 10px 0 55px;">
           <ul class="authi">
<li class="elecnation_dy">
<em>
<?php if(isset($post['isstick'])) { ?>
<img src ="<?php echo IMGDIR;?>/settop.png" title="置顶回复" class="vm" /> 来自 <?php echo $post['number'];?><?php echo $postnostick;?>
<?php } elseif($post['number'] == -1) { ?>
推荐
<?php } else { if(!empty($postno[$post['number']])) { ?><?php echo $postno[$post['number']];?><?php } else { ?><?php echo $post['number'];?><?php echo $postno['0'];?><?php } } ?>
</em>
                    <b><?php if($post['authorid'] && $post['username'] && !$post['anonymous']) { ?><a href="home.php?mod=space&amp;uid=<?php echo $post['authorid'];?>&amp;do=profile&amp;mobile=2" class="elecnation_dr"><?php echo $post['author'];?></a></b>

<?php } else { if(!$post['authorid']) { ?>
<a href="javascript:;">游客 <em><?php echo $post['useip'];?></em></a>
<?php } elseif($post['authorid'] && $post['username'] && $post['anonymous']) { if($_G['forum']['ismoderator']) { ?><a href="home.php?mod=space&amp;uid=<?php echo $post['authorid'];?>&amp;do=profile&amp;mobile=2">匿名</a><?php } else { ?>匿名<?php } } else { ?>
<?php echo $post['author'];?> <em>该用户已被删除</em>
<?php } } ?>
</li>
<li class="elecnation_dy">
<?php if($_G['forum']['ismoderator']) { ?>
<!-- manage start -->
<?php if($post['first']) { ?>
<em><a href="#moption_<?php echo $post['pid'];?>" class="popup elecnation_dr">管理</a></em>
<div id="moption_<?php echo $post['pid'];?>" popup="true" class="manage" style="display:none;">
<?php if(!$_G['forum_thread']['special']) { ?>
<input type="button" value="编辑" class="redirect button" href="forum.php?mod=post&amp;action=edit&amp;fid=<?php echo $_G['fid'];?>&amp;tid=<?php echo $_G['tid'];?>&amp;pid=<?php echo $post['pid'];?><?php if($_G['forum_thread']['sortid']) { if($post['first']) { ?>&amp;sortid=<?php echo $_G['forum_thread']['sortid'];?><?php } } if(!empty($_GET['modthreadkey'])) { ?>&amp;modthreadkey=<?php echo $_GET['modthreadkey'];?><?php } ?>&amp;page=<?php echo $page;?>">
<?php } ?>
<input type="button" value="删除" class="dialog button" href="forum.php?mod=topicadmin&amp;action=moderate&amp;fid=<?php echo $_G['fid'];?>&amp;moderate[]=<?php echo $_G['tid'];?>&amp;operation=delete&amp;optgroup=3&amp;from=<?php echo $_G['tid'];?>">
<input type="button" value="关闭" class="dialog button" href="forum.php?mod=topicadmin&amp;action=moderate&amp;fid=<?php echo $_G['fid'];?>&amp;moderate[]=<?php echo $_G['tid'];?>&amp;from=<?php echo $_G['tid'];?>&amp;optgroup=4">
<input type="button" value="屏蔽" class="dialog button" href="forum.php?mod=topicadmin&amp;action=banpost&amp;fid=<?php echo $_G['fid'];?>&amp;tid=<?php echo $_G['tid'];?>&amp;topiclist[]=<?php echo $_G['forum_firstpid'];?>">
<input type="button" value="警告" class="dialog button" href="forum.php?mod=topicadmin&amp;action=warn&amp;fid=<?php echo $_G['fid'];?>&amp;tid=<?php echo $_G['tid'];?>&amp;topiclist[]=<?php echo $_G['forum_firstpid'];?>">
</div>
<?php } else { ?>
<em><a href="#moption_<?php echo $post['pid'];?>" class="popup elecnation_dr">管理</a></em>
<div id="moption_<?php echo $post['pid'];?>" popup="true" class="manage" style="display:none;">
<input type="button" value="编辑" class="redirect button" href="forum.php?mod=post&amp;action=edit&amp;fid=<?php echo $_G['fid'];?>&amp;tid=<?php echo $_G['tid'];?>&amp;pid=<?php echo $post['pid'];?><?php if(!empty($_GET['modthreadkey'])) { ?>&amp;modthreadkey=<?php echo $_GET['modthreadkey'];?><?php } ?>&amp;page=<?php echo $page;?>">
<?php if($_G['group']['allowdelpost']) { ?><input type="button" value="删除" class="dialog button" href="forum.php?mod=topicadmin&amp;action=delpost&amp;fid=<?php echo $_G['fid'];?>&amp;tid=<?php echo $_G['tid'];?>&amp;operation=&amp;optgroup=&amp;page=&amp;topiclist[]=<?php echo $post['pid'];?>"><?php } if($_G['group']['allowbanpost']) { ?><input type="button" value="屏蔽" class="dialog button" href="forum.php?mod=topicadmin&amp;action=banpost&amp;fid=<?php echo $_G['fid'];?>&amp;tid=<?php echo $_G['tid'];?>&amp;operation=&amp;optgroup=&amp;page=&amp;topiclist[]=<?php echo $post['pid'];?>"><?php } if($_G['group']['allowwarnpost']) { ?><input type="button" value="警告" class="dialog button" href="forum.php?mod=topicadmin&amp;action=warn&amp;fid=<?php echo $_G['fid'];?>&amp;tid=<?php echo $_G['tid'];?>&amp;operation=&amp;optgroup=&amp;page=&amp;topiclist[]=<?php echo $post['pid'];?>"><?php } ?>
</div>
<?php } ?>
<!-- manage end -->
<?php } if($post['first']) { ?>
<em><a href="home.php?mod=spacecp&amp;ac=favorite&amp;type=thread&amp;id=<?php echo $_G['tid'];?>" class="favbtn elecnation_dr" <?php if($_G['forum']['ismoderator']) { ?>style="margin-right:10px;"<?php } ?>>收藏</a></em>
<?php } ?>
<?php echo $post['dateline'];?>
</li>
            </ul>
            </div>
            
            <div style="border-top:1px solid #DDDDDD; margin-top:8px; padding:10px;">
<div class="message">       
                	<?php if($post['warned']) { ?>
                        <span class="grey quote">受到警告</span>
                    <?php } ?>
                    <?php if(!$post['first'] && !empty($post['subject'])) { ?>
                        <h2><strong><?php echo $post['subject'];?></strong></h2>        	
                    <?php } ?>
                    <?php if($_G['adminid'] != 1 && $_G['setting']['bannedmessages'] & 1 && (($post['authorid'] && !$post['username']) || ($post['groupid'] == 4 || $post['groupid'] == 5) || $post['status'] == -1 || $post['memberstatus'])) { ?>
                        <div class="grey quote">提示: <em>作者被禁止或删除 内容自动屏蔽</em></div>
                    <?php } elseif($_G['adminid'] != 1 && $post['status'] & 1) { ?>
                        <div class="grey quote">提示: <em>该帖被管理员或版主屏蔽</em></div>
                    <?php } elseif($needhiddenreply) { ?>
                        <div class="grey quote">此帖仅作者可见</div>
                    <?php } elseif($post['first'] && $_G['forum_threadpay']) { include template('forum/viewthread_pay'); } else { ?>

                    	<?php if($_G['setting']['bannedmessages'] & 1 && (($post['authorid'] && !$post['username']) || ($post['groupid'] == 4 || $post['groupid'] == 5))) { ?>
                            <div class="grey quote">提示: <em>作者被禁止或删除 内容自动屏蔽，只有管理员或有管理权限的成员可见</em></div>
                        <?php } elseif($post['status'] & 1) { ?>
                            <div class="grey quote">提示: <em>该帖被管理员或版主屏蔽，只有管理员或有管理权限的成员可见</em></div>
                        <?php } ?>
                        <?php if($_G['forum_thread']['price'] > 0 && $_G['forum_thread']['special'] == 0) { ?>
                            付费主题, 价格: <strong><?php echo $_G['forum_thread']['price'];?> <?php echo $_G['setting']['extcredits'][$_G['setting']['creditstransextra']['1']]['unit'];?><?php echo $_G['setting']['extcredits'][$_G['setting']['creditstransextra']['1']]['title'];?> </strong> <a href="forum.php?mod=misc&amp;action=viewpayments&amp;tid=<?php echo $_G['tid'];?>" >记录</a>
                        <?php } ?>

                        <?php if($post['first'] && $threadsort && $threadsortshow) { ?>
                        	<?php if($threadsortshow['optionlist'] && !($post['status'] & 1) && !$_G['forum_threadpay']) { ?>
                                <?php if($threadsortshow['optionlist'] == 'expire') { ?>
                                    该信息已经过期
                                <?php } else { ?>
                                    <div class="box_ex2 viewsort">
                                        <h4><?php echo $_G['forum']['threadsorts']['types'][$_G['forum_thread']['sortid']];?></h4>
                                    <?php if(is_array($threadsortshow['optionlist'])) foreach($threadsortshow['optionlist'] as $option) { ?>                                        <?php if($option['type'] != 'info') { ?>
                                            <?php echo $option['title'];?>: <?php if($option['value']) { ?><?php echo $option['value'];?> <?php echo $option['unit'];?><?php } else { ?><span class="elecnation_dy">--</span><?php } ?><br />
                                        <?php } ?>
                                    <?php } ?>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                        <?php if($post['first']) { ?>
                            <?php if(!$_G['forum_thread']['special']) { ?>
                                <?php echo $post['message'];?>
                            <?php } elseif($_G['forum_thread']['special'] == 1) { ?>
                                <?php include template('forum/viewthread_poll'); ?>                            <?php } elseif($_G['forum_thread']['special'] == 2) { ?>
                                <?php include template('forum/viewthread_trade'); ?>                            <?php } elseif($_G['forum_thread']['special'] == 3) { ?>
                                <?php include template('forum/viewthread_reward'); ?>                            <?php } elseif($_G['forum_thread']['special'] == 4) { ?>
                                <?php include template('forum/viewthread_activity'); ?>                            <?php } elseif($_G['forum_thread']['special'] == 5) { ?>
                                <?php include template('forum/viewthread_debate'); ?>                            <?php } elseif($threadplughtml) { ?>
                                <?php echo $threadplughtml;?>
                                <?php echo $post['message'];?>
                            <?php } else { ?>
                            	<?php echo $post['message'];?>
                            <?php } ?>
                        <?php } else { ?>
                            <?php echo $post['message'];?>
                        <?php } } ?>
            </div>
<?php if($_G['setting']['mobile']['mobilesimpletype'] == 0) { if($post['attachment']) { ?>
               <div class="elecnation_dy quote">
               附件: <em><?php if($_G['uid']) { ?>您所在的用户组无法下载或查看附件<?php } else { ?>您需要<a href="member.php?mod=logging&amp;action=login">登录</a>才可以下载或查看附件。没有帐号？<a href="member.php?mod=<?php echo $_G['setting']['regname'];?>" title="注册帐号"><?php echo $_G['setting']['reglinkname'];?></a><?php } ?></em>
               </div>
            <?php } elseif($post['imagelist'] || $post['attachlist']) { ?>
               <?php if($post['imagelist']) { if(count($post['imagelist']) == 1) { ?>
<ul class="img_one"><?php echo showattach($post, 1); ?></ul>
<?php } else { ?>
<ul class="img_list cl vm"><?php echo showattach($post, 1); ?></ul>
<?php } } ?>
                <?php if($post['attachlist']) { ?>
<ul><?php echo showattach($post); ?></ul>
<?php } } } ?>
            </div>
       </div>
<?php if($_G['uid'] && $allowpostreply && !$post['first']) { ?>
<div id="replybtn_<?php echo $post['pid'];?>" display="true" class="elecnation_post_reply">
<a href="forum.php?mod=post&amp;action=reply&amp;fid=<?php echo $_G['fid'];?>&amp;tid=<?php echo $_G['tid'];?>&amp;repquote=<?php echo $post['pid'];?>&amp;extra=<?php echo $_GET['extra'];?>&amp;page=<?php echo $page;?>" style="padding:8px 6px;">回复</a>
</div>
<?php } ?>
   </div>
   </div>
   <?php if(!empty($_G['setting']['pluginhooks']['viewthread_postbottom_mobile'][$postcount])) echo $_G['setting']['pluginhooks']['viewthread_postbottom_mobile'][$postcount];?>
   <?php $postcount++;?>   <?php } ?>
   
<div id="post_new"></div>
<div class="elecnation_fastpost cl">
<div style="padding:18px 0;">
<form method="post" autocomplete="off" id="fastpostform" action="forum.php?mod=post&amp;action=reply&amp;fid=<?php echo $_G['fid'];?>&amp;tid=<?php echo $_G['tid'];?>&amp;extra=<?php echo $_GET['extra'];?>&amp;replysubmit=yes&amp;mobile=2">
<input type="hidden" name="formhash" value="<?php echo FORMHASH;?>" />
<div class="avatar"><img style="height:32px;width:32px;" src="<?php echo avatar($_G[uid], small, true);?>" /></div>  
<div class="pi">
<ul class="fastpost">
<?php if($_G['forum_thread']['special'] == 5 && empty($firststand)) { ?>
<li>
<select id="stand" name="stand" >
<option value="">选择观点</option>
<option value="0">中立</option>
<option value="1">正方</option>
<option value="2">反方</option>
</select>
</li>
<?php } ?>
<li><input type="text" value="我也说一句" class="input grey" color="gray" name="message" id="fastpostmessage"></li>    
<li id="fastpostsubmitline" style="display:none;"><?php if(checkperm('seccode') && ($secqaacheck || $seccodecheck)) { $sechash = 'S'.random(4);
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
        验证问答: 
        <span class="xg2">{$secqaa}</span>
        <input name="secanswer" id="secqaaverify_{$sechash}" type="text" class="txt" />
        </p>

EOF;
 } if($seccodecheck) { 
$seccheckhtml .= <<<EOF

<div class="sec_code vm">
<input type="text" class="txt px vm" style="ime-mode:disabled;width:60px;background:white;" autocomplete="off" value="" id="seccodeverify_{$sechash}" name="seccodeverify" placeholder="验证码" fwin="seccode">
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
<?php } ?><input type="button" value="回复" class="button2" name="replysubmit" id="fastpostsubmit"><a href="forum.php?mod=post&amp;action=reply&amp;fid=<?php echo $_G['fid'];?>&amp;tid=<?php echo $_G['tid'];?>&amp;reppost=<?php echo $_G['forum_firstpid'];?>&amp;page=<?php echo $page;?>" class="y" style="height:30px;width:30px;margin-top:7px;background:url(<?php echo $_G['style']['styleimgdir'];?>/touch/common/images/icon_photo.png) no-repeat 50% 50%;"><span class="none">回复</span></a></li>
</ul>
</div>
    </form>
</div>
</div>
<script type="text/javascript">
(function() {
var form = $('#fastpostform');
<?php if(!$_G['uid'] || $_G['uid'] && !$allowpostreply) { ?>
$('#fastpostmessage').on('focus', function() {
<?php if(!$_G['uid']) { ?>
popup.open('您还未登录，立即登录?', 'confirm', 'member.php?mod=logging&action=login');
<?php } else { ?>
popup.open('您暂时没有权限发表', 'alert');
<?php } ?>
this.blur();
});
<?php } else { ?>
$('#fastpostmessage').on('focus', function() {
var obj = $(this);
if(obj.attr('color') == 'gray') {
obj.attr('value', '');
obj.removeClass('grey');
obj.attr('color', 'black');
$('#fastpostsubmitline').css('display', 'block');
}
})
.on('blur', function() {
var obj = $(this);
if(obj.attr('value') == '') {
obj.addClass('grey');
obj.attr('value', '我也说一句');
obj.attr('color', 'gray');
}
});
<?php } ?>
$('#fastpostsubmit').on('click', function() {
var msgobj = $('#fastpostmessage');
if(msgobj.val() == '我也说一句') {
msgobj.attr('value', '');
}
$.ajax({
type:'POST',
url:form.attr('action') + '&handlekey=fastpost&loc=1&inajax=1',
data:form.serialize(),
dataType:'xml'
})
.success(function(s) {
evalscript(s.lastChild.firstChild.nodeValue);
})
.error(function() {
window.location.href = obj.attr('href');
popup.close();
});
return false;
});

$('#replyid').on('click', function() {
$(document).scrollTop($(document).height());
$('#fastpostmessage')[0].focus();
});

})();

function succeedhandle_fastpost(locationhref, message, param) {
var pid = param['pid'];
var tid = param['tid'];
if(pid) {
$.ajax({
type:'POST',
url:'forum.php?mod=viewthread&tid=' + tid + '&viewpid=' + pid + '&mobile=2',
dataType:'xml'
})
.success(function(s) {
$('#post_new').append(s.lastChild.firstChild.nodeValue);
})
.error(function() {
window.location.href = 'forum.php?mod=viewthread&tid=' + tid;
popup.close();
});
} else {
if(!message) {
message = '本版回帖需要审核，您的帖子将在通过审核后显示';
}
popup.open(message, 'alert');
}
$('#fastpostmessage').attr('value', '');
if(param['sechash']) {
$('.seccodeimg').click();
}
}

function errorhandle_fastpost(message, param) {
popup.open(message, 'alert');
}
</script>    
</div>

<?php echo $multipage;?>
<div id="elecnation_multi_footer"></div>
<!-- main postlist end -->

</div>

<?php if(!empty($_G['setting']['pluginhooks']['viewthread_bottom_mobile'])) echo $_G['setting']['pluginhooks']['viewthread_bottom_mobile'];?>

<script type="text/javascript">
$('.favbtn').on('click', function() {
var obj = $(this);
$.ajax({
type:'POST',
url:obj.attr('href') + '&handlekey=favbtn&inajax=1',
data:{'favoritesubmit':'true', 'formhash':'<?php echo FORMHASH;?>'},
dataType:'xml',
})
.success(function(s) {
popup.open(s.lastChild.firstChild.nodeValue);
evalscript(s.lastChild.firstChild.nodeValue);
})
.error(function() {
window.location.href = obj.attr('href');
popup.close();
});
return false;
});
</script>

<a href="javascript:;" title="返回顶部" class="scrolltop bottom"></a><?php include template('common/footer'); ?>