<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); hookscriptoutput('showmessage');?>
<?php $space['isfriend'] = $space['self'];
if(in_array($_G['uid'], (array)$space['friends'])) $space['isfriend'] = 1;
space_merge($space, 'count');?><?php if($param['login']) { if($_G['inajax']) { dheader('Location:member.php?mod=logging&action=login&inajax=1&infloat=1');exit;?><?php } else { dheader('Location:member.php?mod=logging&action=login');exit;?><?php } } include template('common/header'); if($_G['inajax']) { ?>
<div class="tip">
<dt id="messagetext">
<p><?php echo $show_message;?></p>
        <?php if($_G['forcemobilemessage']) { ?>
        	<p>
            	<a href="<?php echo $_G['setting']['mobile']['pageurl'];?>" class="mtn">��������</a><br />
                <a href="javascript:history.back();">������һҳ</a>
            </p>
        <?php } if($url_forward && !$_GET['loc']) { ?>
<!--<p><a class="grey" href="<?php echo $url_forward;?>">��������ӽ�����ת</a></p>-->
<script type="text/javascript">
setTimeout(function() {
window.location.href = '<?php echo $url_forward;?>';
}, '3000');
</script>
<?php } elseif($allowreturn) { ?>
<p><input type="button" class="button" onclick="popup.close();" value="�ر�"></p>
<?php } ?>
</dt>
</div>
<?php } else { ?>

<!-- header start -->
<header class="header">
<?php if($_G['setting']['domain']['app']['mobile']) { $nav = 'http://'.$_G['setting']['domain']['app']['mobile'];?><?php } else { $nav = "forum.php";?><?php } ?>
<div id="elecnation_bbname">
        <a title="<?php echo $_G['setting']['bbname'];?>" href="<?php echo $nav;?>" class="title"><?php if($_G['setting']['mobile']['mobilesimpletype'] == 1) { if(empty($nobbname)) { ?> <?php echo $_G['setting']['bbname'];?> - <?php } ?> �ֻ���<?php } else { ?><?php echo $_G['setting']['bbname'];?><?php } ?></a>
</div>

<?php if($_G['uid']) { ?>
<div id="elecnation_header">
<div id="elecnation_header_float">
����<br />
<?php echo $space['threads'];?>
</div>
<div id="elecnation_header_float">
����<br />
<?php echo $space['posts'];?>
</div>
            
<div class="elecnation_header_avatar">
        	<div id="elecnation_header_avatar_rad60">
            	<div id="elecnation_header_avatar_rad">
<a href="home.php?mod=space&amp;uid=<?php echo $_G['uid'];?>&amp;do=profile&amp;mycenter=1" style="border:none;"><img src="<?php echo avatar($_G[uid], middle, true);?>" width="60" height="60" alt="<?php echo $discuz_userss;?>" style="border:none;" /></a>
</div>
            </div>
            <?php if($_G['member']['newpm']) { ?>
            <div class="elecnation_header_newmsg"><img src="<?php echo $_G['style']['styleimgdir'];?>/touch/common/images/icon_msg.png" /></div>
            <?php } ?>
        </div>
            
<div id="elecnation_header_float">
��¼<br />
<?php echo $space['doings'];?>
</div>
<div id="elecnation_header_float">
����<br />
<?php echo $_G['member']['credits'];?>
</div>
        <div id="elecnation_clear"></div>
        <div id="elecnation_header_username"><?php echo $_G['username'];?></div>
</div>
        
<?php } else { ?>
<div id="elecnation_header_guest">
<div id="elecnation_header_guest_float">
<a href="<?php if($_G['setting']['regstatus']) { ?>member.php?mod=<?php echo $_G['setting']['regname'];?><?php } else { if($_G['setting']['connect']['allow'] && !$_G['setting']['bbclosed']) { ?><?php echo $_G['connect']['login_url'];?>&statfrom=login_simple<?php } else { ?>javascript:;<?php } ?>" style="color:#4C4C4C;<?php } ?>" title="<?php echo $_G['setting']['reglinkname'];?>">ע��</a>
</div>
<div id="elecnation_header_guest_avatar60">
        	<div id="elecnation_header_guest_avatar">
<a href="member.php?mod=logging&amp;action=login" style="border:none;"><img src="<?php echo avatar($_G[uid], middle, true);?>" width="60" height="60" alt="�ο�" style="border:none;" /></a>
</div>
        </div>
<div id="elecnation_header_guest_float">
<a href="member.php?mod=logging&amp;action=login">��¼</a>
</div>
        <div id="elecnation_clear"></div>
        <div id="elecnation_header_guest_hello">�ο�</div>
</div>    
<?php } ?>
</header>
<!-- header end -->

<!-- main jump start -->
<div class="jump_c">
<p><?php echo $show_message;?></p>
    <?php if($_G['forcemobilemessage']) { ?>
<p>
            <a href="<?php echo $_G['setting']['mobile']['pageurl'];?>" class="mtn">��������</a><br />
            <a href="javascript:history.back();">������һҳ</a>
        </p>
    <?php } if($url_forward) { ?>
<p><a class="grey" href="<?php echo $url_forward;?>">��������ӽ�����ת</a></p>
<?php } elseif($allowreturn) { ?>
<p><a class="grey" href="javascript:history.back();">[ ������ﷵ����һҳ ]</a></p>
<?php } ?>
</div>
<!-- main jump end -->

<?php } include template('common/footer'); ?>