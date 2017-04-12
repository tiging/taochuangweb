<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); hookscriptoutput('space_profile');?>
<?php if($_GET['mycenter'] && !$_G['uid']) { dheader('Location:member.php?mod=logging&action=login');exit;?><?php } include template('common/header'); if(!$_GET['mycenter']) { ?>

<!-- header start -->
<header class="header">
    <div class="nav">
        <div class="category">
            <?php if($_G['uid'] == $space['uid']) { ?>
            我的资料
            <?php } else { ?>
            <?php echo $space['username'];?> 的资料
            <?php } ?>
        
        <div id="elecnation_nav_left">
            <?php if($_G['uid'] == $space['uid']) { ?>
            <a href="home.php?mod=space&amp;uid=<?php echo $_G['uid'];?>&amp;do=profile&amp;mycenter=1"><img src="<?php echo $_G['style']['styleimgdir'];?>/touch/common/images/icon_center.png" width="41" height="30" /></a>
            <?php } else { ?>
            <a href="javascript:;" onclick="history.go(-1)"><img src="<?php echo $_G['style']['styleimgdir'];?>/touch/common/images/icon_back.png" width="41" height="30"/></a>
            <?php } ?>
        </div>
        </div>
    </div>
</header>
<!-- header end -->
<!-- userinfo start -->
<div class="userinfo wp">
<div class="elecnation_userinfo">
    	<div id="elecnation_userinfo_avatar">
<div id="elecnation_userinfo_avatar_img">
        	<img src="<?php echo avatar($space[uid], big, true);?>" width="100" height="100" alt="<?php echo $discuz_userss;?>" style="border:none;" />
        </div>
        </div>
<div id="elecnation_userinfo_name"><?php echo $space['username'];?></div>
</div>
<div class="user_box">
<ul>
<li><span><?php echo $space['credits'];?></span>积分</li><?php if(is_array($_G['setting']['extcredits'])) foreach($_G['setting']['extcredits'] as $key => $value) { if($value['title']) { ?>
<li><span><?php echo $space["extcredits$key"];?> <?php echo $value['unit'];?></span><?php echo $value['title'];?></li>
<?php } } ?>
            <li><span><?php echo $space['friends'];?></span>好友</li>
</ul>
</div>
<?php if($space['uid'] == $_G['uid']) { ?>
<div class="btn_exit"><a href="member.php?mod=logging&amp;action=logout&amp;formhash=<?php echo FORMHASH;?>" style="color:#FFFFFF;">退出登录</a></div>
    <?php } else { ?>
    <div class="btn_friend">
    <?php require_once libfile('function/friend');$isfriend=friend_check($space[uid]);?><?php if(!$isfriend) { ?>
    		<a href="home.php?mod=spacecp&amp;ac=friend&amp;op=add&amp;uid=<?php echo $space['uid'];?>&amp;handlekey=addfriendhk_<?php echo $space['uid'];?>" style="color:#FFFFFF;">加为好友</a>
            <?php } else { ?>
            <a href="home.php?mod=spacecp&amp;ac=friend&amp;op=ignore&amp;uid=<?php echo $uid;?>&amp;confirm=1" style="color:#FFFFFF;">忽略好友</a>
            <?php } ?>
    </div>
    <div class="btn_exit"><a href="home.php?mod=space&amp;do=pm&amp;subop=view&amp;touid=<?php echo $space['uid'];?>" style="color:#FFFFFF;">发消息</a></div>
<?php } ?>
</div>
<!-- userinfo end -->

<?php } else { ?>

<!-- header start -->
<header class="header">
    <div class="nav">
        <div class="category" style="width:100%; text-align:center;">
            我的空间
        </div>
    </div>
</header>
<!-- header end -->
<!-- userinfo start -->
<div class="userinfo wp">
<div id="elecnation_userinfo">
    	<div id="elecnation_userinfo_avatar">
<div id="elecnation_userinfo_avatar_img">
<img src="<?php echo avatar($_G[uid], big, true);?>" width="100" height="100" alt="<?php echo $discuz_userss;?>" style="border:none;" />
        </div>
        </div>
        <div id="elecnation_userinfo_name"><?php echo $_G['username'];?></div>
</div>
<div class="myinfo_list cl">
<ul>
<li><div id="elecnation_userinfo_word"><a href="home.php?mod=space&amp;uid=<?php echo $_G['uid'];?>&amp;do=favorite&amp;view=me&amp;type=thread">我的收藏</a></div></li>
<li><div id="elecnation_userinfo_word"><a href="home.php?mod=space&amp;uid=<?php echo $_G['uid'];?>&amp;do=thread&amp;view=me">我的主题</a></div></li>
<li class="tit_msg">
            <div id="elecnation_userinfo_word">
            <a href="home.php?mod=space&amp;do=pm">我的消息
            <?php if($_G['member']['newpm']) { ?>
            	<img src="<?php echo $_G['style']['styleimgdir'];?>/touch/common/images/icon_msg.png" />
            <?php } ?>
            </a>
            </div>
            </li>
<li><div id="elecnation_userinfo_word"><a href="home.php?mod=space&amp;uid=<?php echo $_G['uid'];?>&amp;do=profile&amp;mobile=2">我的资料</a></div></li>
            <li><div id="elecnation_userinfo_word"><a href="home.php?mod=space&amp;uid=<?php echo $_G['uid'];?>&amp;do=friend&amp;mobile=2">我的好友</a></div></li>
            <li><div id="elecnation_userinfo_word"><a href="home.php?mod=spacecp&amp;ac=friend&amp;op=request&amp;mobile=2">好友请求</a></div></li>
</ul>
</div>
</div>
<!-- userinfo end -->

<?php } include template('common/footer'); ?>