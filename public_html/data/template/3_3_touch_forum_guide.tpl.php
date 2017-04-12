<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); hookscriptoutput('guide');
0
|| checktplrefresh('./template/elecnation_x3touch_pro/touch/forum/guide.htm', './template/elecnation_x3touch_pro/touch/forum/guide_list_row.htm', 1491925394, '3', './data/template/3_3_touch_forum_guide.tpl.php', './template/elecnation_x3touch_pro', 'touch/forum/guide')
;?>
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
    
    <div id="elecnation_gds_red">
    	<a href="forum.php?mod=guide&amp;view=hot" style="padding:6px; color:#D80000;">热帖</a>
    </div>
    
    <div id="elecnation_gds_float">
    	<a href="forum.php?forumlist=1&amp;mobile=2" style="padding:6px;"><?php echo $_G['setting']['navs']['2']['navname'];?></a>
    </div>
    
    <div id="elecnation_gds_5float" style="border-right:none;">
    	<a href="home.php?mod=space&amp;do=doing&amp;view=all&amp;mobile=2" style="padding:6px;">记录</a>
    </div>
    <div id="elecnation_clear"></div>
</div>
<?php } else { ?>
<div id="elecnation_gds">
    
    <div id="elecnation_gds_red">
    	<a href="forum.php?mod=guide&amp;view=newthread" style="padding:6px; color:#D80000;">帖子</a>
    </div>
    
    <div id="elecnation_gds_float">
    	<a href="forum.php?forumlist=1&amp;mobile=2" style="padding:6px;"><?php echo $_G['setting']['navs']['2']['navname'];?></a>
    </div>
    
    <div id="elecnation_gds_float" style="border-right:none;">
    	<a href="home.php?mod=space&amp;do=doing&amp;view=all&amp;mobile=2" style="padding:6px;">记录</a>
    </div>
    <div id="elecnation_clear"></div>
</div>
<?php } ?>


<!-- main threadlist start -->
<div class="threadlist"><?php if(is_array($data)) foreach($data as $key => $list) { if($list['threadcount']) { ?>
<div class="threadlist"><?php if(is_array($list['threadlist'])) foreach($list['threadlist'] as $key => $thread) { ?>                                <a href="forum.php?mod=viewthread&amp;tid=<?php echo $thread['tid'];?>&amp;fromguid=hot&amp;<?php if($_GET['archiveid']) { ?>archiveid=<?php echo $_GET['archiveid'];?>&amp;<?php } ?>extra=<?php echo $extra;?>">
                                <div id="threadlist_li">
<?php if(!$thread['forumstick'] && $thread['closed'] > 1 && ($thread['isgroup'] == 1 || $thread['fid'] != $_G['fid'])) { $thread[tid]=$thread[closed];?><?php } ?>
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                          <tr>
                                            <td width="18" valign="top">
                                            <?php if(in_array($thread['displayorder'], array(1, 2, 3, 4))) { ?>
                                                <span><img src="<?php echo $_G['style']['styleimgdir'];?>/touch/common/images/icon_top.gif" width="13" height="12"></span>
                                            <?php } elseif($thread['attachment'] == 2 && $_G['setting']['mobile']['mobilesimpletype'] == 0) { ?>
                                                <span><img src="<?php echo $_G['style']['styleimgdir'];?>/touch/common/images/icon_img.gif" width="13" height="12"></span>
                                            <?php } elseif($thread['digest'] > 0) { ?>
                                                <span><img src="<?php echo $_G['style']['styleimgdir'];?>/touch/common/images/icon_dig.gif" width="13" height="12"></span>
                                            <?php } else { ?>
                                                <span><img src="<?php echo $_G['style']['styleimgdir'];?>/touch/common/images/icon_tid.gif" width="13" height="12"></span>
                                            <?php } ?>
                                          </td>
                                            <td valign="top"><?php echo $thread['typehtml'];?> <?php echo $thread['sorthtml'];?> <?php echo $thread['subject'];?></td>
                                          </tr>
                                        </table>

                                        <div style="margin-left:18px; font-size:11px; color:#AAAAAA; line-height:16px;">
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                          <?php if($thread['replies'] > 0) { ?>
                                          <tr>
                                            <td width="80%">
                                                作者 : 
                                                <?php if($thread['author'] && !$thread['anonymous']) { ?>
                                                <?php echo $thread['author'];?>
                                                <?php } else { ?>
                                                匿名
                                                <?php } ?>
                                                 @ <?php echo $thread['dateline'];?>
                                                <br />
                                                回复 : 
                                                <?php if($thread['lastposter'] && !$thread['anonymous']) { ?>
                                                <?php echo $thread['lastposter'];?> @ <?php echo $thread['lastpost'];?>
                                                <?php } else { ?>
                                                    匿名 @ <?php echo $thread['lastpost'];?>
                                                <?php } ?>
                                                
                                            </td>
                                            <td align="right">
                                                <?php if($thread['isgroup'] != 1) { ?><?php echo $thread['replies'];?><?php } else { ?><?php echo $groupnames[$thread['tid']]['replies'];?><?php } ?>
                                                回复<br />
                                                <?php if($thread['isgroup'] != 1) { ?><?php echo $thread['views'];?><?php } else { ?><?php echo $groupnames[$thread['tid']]['views'];?><?php } ?>
                                                查看
                                            </td>
                                          </tr>
                                          <?php } else { ?>
                                          <tr>
                                            <td width="80%">
                                                作者 :
                                                <?php if($thread['author'] && !$thread['anonymous']) { ?>
                                                <?php echo $thread['author'];?>
                                                <?php } else { ?>
                                                匿名
                                                <?php } ?>
                                                 @ <?php echo $thread['dateline'];?>
                                            </td>
                                            <td align="right">
                                                <?php if($thread['isgroup'] != 1) { ?><?php echo $thread['replies'];?><?php } else { ?><?php echo $groupnames[$thread['tid']]['replies'];?><?php } ?>
                                                回复<br />
                                                <?php if($thread['isgroup'] != 1) { ?><?php echo $thread['views'];?><?php } else { ?><?php echo $groupnames[$thread['tid']]['views'];?><?php } ?>
                                                查看
                                            </td>
                                          </tr>
                                            <?php } ?>
                                        </table>
                                        </div>
                                </div>
</a>
<?php } ?>
</div>
<?php } else { ?>
<div id="elecnation_noinfo">暂时还没有帖子</div>
<?php } } ?>

</div>
<!-- main threadlist end -->

<?php echo $multipage;?>
<div id="elecnation_multi_footer"></div>
<div id="elecnation_footer_box">
<?php if($_G['uid']) { ?>
您好, <?php echo $_G['username'];?>, 欢迎光临触屏版
<?php } else { ?>
您好, 游客, 您需要登录后才可以发帖
<?php } ?>
</div>

<div class="pullrefresh" style="display:none;"></div><?php include template('common/footer'); ?>