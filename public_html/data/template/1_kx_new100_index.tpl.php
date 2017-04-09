<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); hookscriptoutput('index');?><?php include template('common/header'); ?><style type="text/css">
.t {border: 1px solid #bdcfdd;padding: 1px;_display: inline-block;margin-bottom: 10px;background: #ffffff;}
.tr2 td{padding:5px 5px 3px;color:#999999;background:#FFFFFF;border-bottom:1px solid #c5d8e8;}
.tr3 td{padding:5px 10px;border-bottom:1px dotted #ddd;border-right:1px solid #D5E5E8;font-size:1.2em;}
.tac {text-align: center;padding: 10px;}
.yl_jz{text-align:center;}
.tal{text-align:left}
</style>
            <div class="t">
<table width="100%" align="center" cellspacing="0" cellpadding="0">
<tr class="tr2 tac">
<td width="10%">所在版块</td>
<td width="40%">标题</td>
<td width="14%">作者</td>
<td width="10%">人气</td>
<td width="10%">回复</td>
<td width="16%">发表时间</td>
</tr>
<?php if(!empty($devdb)) { ?>  <?php if(is_array($devdb)) foreach($devdb as $value) { ?><tr class="tr3 tac">
<td class="yl_jz"><font color=#A6A6A6><?php echo $value['name'];?></font></td>
<td class="yl_jz tal"><a href="forum.php?mod=viewthread&amp;tid=<?php echo $value['tid'];?>" target='_blank'><?php echo $value['subject'];?></a></td>
<td class="yl_jz"><a href='home.php?mod=space&uid=<?php echo $value['authorid'];?>' target='_blank'><font color=#8B5742><?php echo $value['author'];?></font></a></td>
<td class="yl_jz"><font color="#FF6633" size="+2"><?php echo $value['views'];?></font></td>
<td class="yl_jz"><font color="#FF6633" size="+1"><?php echo $value['replies'];?></font></td>
<td class="yl_jz"><?php echo $value['dateline'];?></td>
</tr>
<?php } ?>		<?php } ?>
</table></div><?php include template('common/footer'); ?>