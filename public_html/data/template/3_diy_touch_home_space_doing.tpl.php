<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); hookscriptoutput('space_doing');?>
<?php $space['isfriend'] = $space['self'];
if(in_array($_G['uid'], (array)$space['friends'])) $space['isfriend'] = 1;
space_merge($space, 'count');?><?php include template('common/header'); ?><!-- header start -->
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
    
    <div id="elecnation_gds_float">
    	<a href="forum.php?forumlist=1&amp;mobile=2" style="padding:6px;"><?php echo $_G['setting']['navs']['2']['navname'];?></a>
    </div>
    
    <div id="elecnation_gds_red" style="border-right:none;">
    	<a href="home.php?mod=space&amp;do=doing&amp;view=all&amp;mobile=2" style="padding:6px; color:#D80000;">记录</a>
    </div>
    <div id="elecnation_clear"></div>
</div>
<?php } else { ?>
<div id="elecnation_gds">
    
    <div id="elecnation_gds_float">
    	<a href="forum.php?mod=guide&amp;view=newthread" style="padding:6px;">帖子</a>
    </div>
    
    <div id="elecnation_gds_float">
    	<a href="forum.php?forumlist=1&amp;mobile=2" style="padding:6px;"><?php echo $_G['setting']['navs']['2']['navname'];?></a>
    </div>
    
    <div id="elecnation_gds_red" style="border-right:none;">
    	<a href="home.php?mod=space&amp;do=doing&amp;view=all&amp;mobile=2" style="padding:6px; color:#D80000; ">记录</a>
    </div>
    <div id="elecnation_clear"></div>
</div>
<?php } ?>

<div id="ct" class="wp">
        <?php if(!empty($_G['setting']['pluginhooks']['space_doing_top'])) echo $_G['setting']['pluginhooks']['space_doing_top'];?>
<?php if(helper_access::check_module('doing')) { include template('home/space_doing_form'); } ?>
<?php if(!empty($_G['setting']['pluginhooks']['space_doing_bottom'])) echo $_G['setting']['pluginhooks']['space_doing_bottom'];?>
            
            <?php if($dolist) { ?>
            <div>
            <?php if(is_array($dolist)) foreach($dolist as $dv) { $doid = $dv[doid];?><?php $_GET[key] = $key = random(8);?>            <div id="elecnation_doing">
                
                <div id="elecnation_doing_relative">
                    <div id="elecnation_doing_avatar">
                        <div id="elecnation_doing_avatar_img"><a href="home.php?mod=space&amp;uid=<?php echo $dv['uid'];?>&amp;do=profile&amp;mobile=2" style="border:none;"><img src="<?php echo avatar($dv[uid], small, true);?>" width="24" height="24" style="border:none;" /></a></div>
                    </div>
                                
                    <div id="elecnation_doing_name">
                        <span><a href="home.php?mod=space&amp;uid=<?php echo $dv['uid'];?>&amp;do=profile&amp;mobile=2"><?php echo $dv['username'];?></a></span>
                        <span class="elecnation_time"> <?php echo dgmdate($dv['dateline'], 'u');?></span>
                    </div>
                </div>
                
                <div id="elecnation_doing_word">
                	<div id="elecnation_doing_message"><?php echo $dv['message'];?></div>
                    <?php if($_G['uid'] && helper_access::check_module('doing')) { ?>
                    <div id="elecnation_doing_reply" class="z">
                    <a href="home.php?mod=spacecp&amp;ac=doing&amp;op=getcomment&amp;handlekey=msg_<?php echo $doid;?>&amp;doid=<?php echo $doid;?>&amp;key=<?php echo $key;?>" style="color:#FFFFFF;">回复</a>
                    </div>
                    <div id="elecnation_doing_replynum" class="z"> <?php echo $dv['replynum'];?> </div>
                    
                    <?php } ?>
                    
                    <?php if($dv['uid']==$_G['uid'] || $_G['uid']==1) { ?>
                    <div id="elecnation_doing_delete" class="y">
                    <a href="home.php?mod=spacecp&amp;ac=doing&amp;op=delete&amp;doid=<?php echo $doid;?>&amp;id=<?php echo $dv['id'];?>&amp;handlekey=doinghk_<?php echo $doid;?>_<?php echo $dv['id'];?>" id="<?php echo $key;?>_doing_delete_<?php echo $doid;?>_<?php echo $dv['id'];?>" style="color:#FFFFFF;">删除</a>
                    </div>
                    <?php } ?>
                    <div id="elecnation_clear"></div>
                </div>
                
                <?php $list = $clist[$doid];?><dd class="cmt brm" id="<?php echo $key;?>_<?php echo $doid;?>"<?php if(empty($list) || !$showdoinglist[$doid]) { ?> style="display:none;"<?php } ?>>
<span id="<?php echo $key;?>_form_<?php echo $doid;?>_0"></span><?php include template('home/space_doing_li'); ?></dd>

            </div>
            <?php } ?>
            
            </div>
            
        <?php } else { ?>
<div id="elecnation_noinfo">现在还没有记录<?php if($space['self']) { ?>您可以用一句话记录下这一刻在做什么<?php } ?></div>
<?php } ?>

<?php echo $multi;?>
<div id="elecnation_multi_footer"></div>

</div>

<?php if(!empty($_G['setting']['pluginhooks']['forumdisplay_bottom_mobile'])) echo $_G['setting']['pluginhooks']['forumdisplay_bottom_mobile'];?>
<div class="pullrefresh" style="display:none;"></div><?php include template('common/footer'); ?><script src="<?php echo $_G['style']['styleimgdir'];?>/touch/common/images/get_face.js" type="text/javascript"></script>