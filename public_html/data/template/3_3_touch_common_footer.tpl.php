<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?>
<?php if(!empty($_G['setting']['pluginhooks']['global_footer_mobile'])) echo $_G['setting']['pluginhooks']['global_footer_mobile'];?><?php $useragent = strtolower($_SERVER['HTTP_USER_AGENT']);$clienturl = 'http://www.discuz.net/mobile.php?platform=android';?><?php if(strpos($useragent, 'iphone') !== false || strpos($useragent, 'ios') !== false) { $clienturl = 'http://www.discuz.net/mobile.php?platform=ios';?><?php } elseif(strpos($useragent, 'android') !== false) { $clienturl = 'http://www.discuz.net/mobile.php?platform=android';?><?php } elseif(strpos($useragent, 'windows phone') !== false) { $clienturl = 'http://www.discuz.net/mobile.php?platform=windowsphone';?><?php } ?>

<div id="mask" style="display:none;"></div>

<?php if(!$nofooter) { ?>
<div class="footer">
    <div id="elecnation_footer">
        <div id="elecnation_footer_float" style="border:none;">
            <a href="forum.php?forumlist=1&amp;mobile=2">首页</a>
        </div>
        
        <div id="elecnation_footer_float">
            <?php if(!$_G['uid']) { ?>
                <a href="<?php if($_G['setting']['regstatus']) { ?>member.php?mod=<?php echo $_G['setting']['regname'];?><?php } else { if($_G['setting']['connect']['allow'] && !$_G['setting']['bbclosed']) { ?><?php echo $_G['connect']['login_url'];?>&statfrom=login_simple<?php } else { ?>javascript:;<?php } ?>" style="color:#AAAAAA;<?php } ?>" title="<?php echo $_G['setting']['reglinkname'];?>">注册</a>
            <?php } else { ?>
                <div class="elecnation_newmsg">
                <a href="home.php?mod=space&amp;do=pm">消息</a>
                <?php if($_G['member']['newpm']) { ?>
                <img src="<?php echo $_G['style']['styleimgdir'];?>/touch/common/images/icon_msg.png" />
                <?php } ?>
                </div>
            <?php } ?>
        </div>
        
        <div id="elecnation_footer_float">
            <a href="search.php?mod=forum">搜索</a>
        </div>
        
        <div id="elecnation_footer_float2">
            <?php if(!$_G['uid']) { ?><a href="member.php?mod=logging&amp;action=login" title="登录">登录</a><?php } else { ?><a href="member.php?mod=logging&amp;action=logout&amp;formhash=<?php echo FORMHASH;?>" title="退出">退出</a><?php } ?>
        </div>
    <div id="elecnation_clear"></div>
    </div>
    
    <div id="elecnation_footer_links">
    	<a href="forum.php?mobile=yes" target="_blank">标准版</a> &copy; <?php echo $_G['setting']['sitename'];?> & TaoChuangWeb. <a href="<?php echo $_G['setting']['mobile']['nomobileurl'];?>" target="_blank">电脑版</a>
    </div>

</div>
<?php } ?>
</body>
</html><?php updatesession();?><?php if(defined('IN_MOBILE')) { output();?><?php } else { output_preview();?><?php } ?>