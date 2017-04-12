<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); hookscriptoutput('discuz');?>
<?php $space['isfriend'] = $space['self'];
if(in_array($_G['uid'], (array)$space['friends'])) $space['isfriend'] = 1;
space_merge($space, 'count');?><?php if($_G['setting']['mobile']['mobilehotthread'] && $_GET['forumlist'] != 1) { dheader('Location:forum.php?mod=guide&view=hot');exit;?><?php } include template('common/header'); ?><!-- header start -->
<header class="header">
<?php if($_G['setting']['domain']['app']['mobile']) { $nav = 'http://'.$_G['setting']['domain']['app']['mobile'];?><?php } else { $nav = "forum.php";?><?php } ?>
<div id="elecnation_bbname">
        <a title="<?php echo $_G['setting']['bbname'];?>" href="<?php echo $nav;?>" class="title"><?php if($_G['setting']['mobile']['mobilesimpletype'] == 1) { if(empty($nobbname)) { ?> <?php echo $_G['setting']['bbname'];?> - <?php } ?> 手机版<?php } else { ?><?php echo $_G['setting']['bbname'];?><?php } ?></a>
</div>

<?php if($_G['uid']) { ?>
<div id="elecnation_header">
<div id="elecnation_header_float">
发布<br />
<?php echo $space['threads'];?>
</div>
<div id="elecnation_header_float">
帖子<br />
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
记录<br />
<?php echo $space['doings'];?>
</div>
<div id="elecnation_header_float">
积分<br />
<?php echo $_G['member']['credits'];?>
</div>
        <div id="elecnation_clear"></div>
        <div id="elecnation_header_username">
        	<span class="elecnation_header_plus"><a href="home.php?mod=space&amp;uid=<?php echo $_G['uid'];?>&amp;do=friend&amp;mobile=2">在线好友</a></span>
            <span style="margin:0 18px; font-size:16px;"><?php echo $_G['username'];?></span>
            <?php if($_G['cache']['plugin']['dsu_paulsign']) { ?>
            <span class="elecnation_header_plus"><a href="plugin.php?id=dsu_paulsign:sign&amp;mobile=2">!name!</a></span>
            <?php } else { ?>
            <span class="elecnation_header_plus"><a href="home.php?mod=space&amp;do=pm&amp;mobile=2">我的消息</a></span>
            <?php } ?>
        </div>
</div>
        
<?php } else { ?>
<div id="elecnation_header_guest">
<div id="elecnation_header_guest_float">
<a href="<?php if($_G['setting']['regstatus']) { ?>member.php?mod=<?php echo $_G['setting']['regname'];?><?php } else { if($_G['setting']['connect']['allow'] && !$_G['setting']['bbclosed']) { ?><?php echo $_G['connect']['login_url'];?>&statfrom=login_simple<?php } else { ?>javascript:;<?php } ?>" style="color:#4C4C4C;<?php } ?>" title="<?php echo $_G['setting']['reglinkname'];?>">注册</a>
</div>
<div id="elecnation_header_guest_avatar60">
        	<div id="elecnation_header_guest_avatar">
<a href="member.php?mod=logging&amp;action=login" style="border:none;"><img src="<?php echo avatar($_G[uid], middle, true);?>" width="60" height="60" alt="游客" style="border:none;" /></a>
</div>
        </div>
<div id="elecnation_header_guest_float">
<a href="member.php?mod=logging&amp;action=login">登录</a>
</div>
        <div id="elecnation_clear"></div>
        <div id="elecnation_header_guest_hello">游客</div>
</div>    
<?php } ?>
</header>
<!-- header end -->

<?php if($_G['setting']['mobile']['mobilehotthread']) { ?>
<div id="elecnation_gds">
       
    <div id="elecnation_gds_float">
    	<a href="forum.php?mod=guide&amp;view=hot" style="padding:6px;">热帖</a>
    </div>
    
    <div id="elecnation_gds_red">
    	<a href="forum.php?forumlist=1&amp;mobile=2" style="padding:6px; color:#D80000;"><?php echo $_G['setting']['navs']['2']['navname'];?></a>
    </div>
    
    <div id="elecnation_gds_float" style="border-right:none;">
    	<a href="home.php?mod=space&amp;do=doing&amp;view=all&amp;mobile=2" style="padding:6px;">记录</a>
    </div>
    <div id="elecnation_clear"></div>
</div>
<?php } else { ?>
<div id="elecnation_gds">
    
    <div id="elecnation_gds_float">
    	<a href="forum.php?mod=guide&amp;view=newthread" style="padding:6px;">帖子</a>
    </div>
    
    <div id="elecnation_gds_red">
    	<a href="forum.php?forumlist=1&amp;mobile=2" style="padding:6px; color:#D80000;"><?php echo $_G['setting']['navs']['2']['navname'];?></a>
    </div>
    
    <div id="elecnation_gds_float" style="border-right:none;">
    	<a href="home.php?mod=space&amp;do=doing&amp;view=all&amp;mobile=2" style="padding:6px;">记录</a>
    </div>
    <div id="elecnation_clear"></div>
</div>
<?php } ?>

<!-- main forumlist start -->
<div class="wp"><?php if(is_array($catlist)) foreach($catlist as $key => $cat) { ?><div class="elecnation_discuz_cn">
    	<font style="font-weight:bold;color:red"><?php echo $cat['name'];?></font>
    </div>
<div style="width:100%; overflow:hidden;"><?php if(is_array($cat['forums'])) foreach($cat['forums'] as $forumid) { $forum=$forumlist[$forumid];?><div id="elecnation_<?php echo $forum['fid'];?>" class="elecnation_discuz_fn">
<div class="elecnation_discuz_fn_float_z ">
<a href="forum.php?mod=forumdisplay&amp;fid=<?php echo $forum['fid'];?>" class="forum_title"><?php echo $forum['name'];?> <img src="<?php echo $_G['style']['styleimgdir'];?>/touch/common/images/arrow_green.png" width="6" height="11" style="border:none;" />
                </a>
</div>
                
<div class="elecnation_discuz_fn_float_y">
<div id="elecnation_discuz_fn_post">
<div id="elecnation_discuz_fn_fatie"><a href="
forum.php?mod=post&action=newthread&fid=<?php echo $forum['fid'];?>" style="color:#FFFFFF;">发帖</a></div>
<div id="elecnation_discuz_fn_num"><?php echo $forum['todayposts'];?></div>
                    <div id="elecnation_clear"></div>
</div>
</div>
<div id="elecnation_clear"></div>
            
            <div id="elecnation_discuz_fn_subject">
<?php if($forum['lastpost']) { ?>
                <a href="forum.php?mod=viewthread&amp;tid=<?php echo $forum['lastpost']['tid'];?>&amp;extra=page%3D1"><?php echo $forum['lastpost']['subject'];?></a>
                <?php } else { ?>
                本版块或指定的范围内尚无主题
                <?php } ?>
</div>
</div>
<?php } ?>
</div>
<?php } ?>
</div>
<!-- main forumlist end -->
<?php if(!empty($_G['setting']['pluginhooks']['index_middle_mobile'])) echo $_G['setting']['pluginhooks']['index_middle_mobile'];?>
<div id="elecnation_footer_box">
今日 <?php echo $todayposts;?> <span class="pipe"> , </span> 总计 <?php echo $posts;?> <span class="pipe"> , </span> 会员 <?php echo $_G['cache']['userstats']['totalmembers'];?> <?php if(empty($gid) && $_G['setting']['whosonlinestatus'] && $onlinenum) { ?> <span class="pipe"> , </span> 在线 <?php echo $onlinenum;?><?php } ?>
</div>
<script type="text/javascript">
(function() {
<?php if(!$_G['setting']['mobile']['mobileforumview']) { ?>
$('.sub_forum').css('display', 'block');
<?php } else { ?>
$('.sub_forum').css('display', 'none');
<?php } ?>
$('.subforumshow').on('click', function() {
var obj = $(this);
var subobj = $(obj.attr('href'));
if(subobj.css('display') == 'none') {
subobj.css('display', 'block');
obj.find('img').attr('src', '<?php echo $_G['style']['styleimgdir'];?>/touch/common/images/collapsed_yes.png');
} else {
subobj.css('display', 'none');
obj.find('img').attr('src', '<?php echo $_G['style']['styleimgdir'];?>/touch/common/images/collapsed_no.png');
}
});
 })();
</script><?php include template('common/footer'); ?>