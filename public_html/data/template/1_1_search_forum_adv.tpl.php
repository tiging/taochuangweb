<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); hookscriptoutput('forum_adv');
0
|| checktplrefresh('./template/default/search/forum_adv.htm', './template/default/search/pubsearch.htm', 1491752904, '1', './data/template/1_1_search_forum_adv.tpl.php', './template/default', 'search/forum_adv')
;?><?php include template('search/header'); ?><div id="ct" class="cl w">
<div class="mw">
<form class="searchform" method="post" autocomplete="off" action="search.php?mod=forum" onsubmit="if($('scform_srchtxt')) searchFocus($('scform_srchtxt'));">
<input type="hidden" name="formhash" value="<?php echo FORMHASH;?>" /><?php $keywordenc = $keyword ? rawurlencode($keyword) : '';?><?php if($searchid || ($_GET['adv'] && CURMODULE == 'forum')) { ?>
<table id="scform" class="mbm" cellspacing="0" cellpadding="0">
<tr>
<td><h1><a href="search.php" title="<?php echo $_G['setting']['bbname'];?>"><img src="<?php echo IMGDIR;?>/logo_sc_s.png" alt="<?php echo $_G['setting']['bbname'];?>" /></a></h1></td>
<td>
<div id="scform_tb" class="cl">
<?php if(CURMODULE == 'forum') { ?>
<span class="y">
<a href="javascript:;" id="quick_sch" class="showmenu" onmouseover="delayShow(this);">����</a>
<?php if(CURMODULE == 'forum') { ?>
<a href="search.php?mod=forum&amp;adv=yes<?php if($keyword) { ?>&amp;srchtxt=<?php echo $keywordenc;?><?php } ?>">�߼�</a>
<?php } ?>
</span>
<?php } if($_G['setting']['portalstatus'] && $_G['setting']['search']['portal']['status'] && ($_G['group']['allowsearch'] & 1 || $_G['adminid'] == 1)) { ?><?php
$slist[portal] = <<<EOF
<a href="search.php?mod=portal
EOF;
 if($keyword) { 
$slist[portal] .= <<<EOF
&amp;srchtxt={$keywordenc}&amp;searchsubmit=yes
EOF;
 } 
$slist[portal] .= <<<EOF
"
EOF;
 if(CURMODULE == 'portal') { 
$slist[portal] .= <<<EOF
 class="a"
EOF;
 } 
$slist[portal] .= <<<EOF
>����</a>
EOF;
?><?php } if($_G['setting']['search']['forum']['status'] && ($_G['group']['allowsearch'] & 2 || $_G['adminid'] == 1)) { ?><?php
$slist[forum] = <<<EOF
<a href="search.php?mod=forum
EOF;
 if($keyword) { 
$slist[forum] .= <<<EOF
&amp;srchtxt={$keywordenc}&amp;searchsubmit=yes
EOF;
 } 
$slist[forum] .= <<<EOF
"
EOF;
 if(CURMODULE == 'forum') { 
$slist[forum] .= <<<EOF
 class="a"
EOF;
 } 
$slist[forum] .= <<<EOF
>����</a>
EOF;
?><?php } if(helper_access::check_module('blog') && $_G['setting']['search']['blog']['status'] && ($_G['group']['allowsearch'] & 4 || $_G['adminid'] == 1)) { ?><?php
$slist[blog] = <<<EOF
<a href="search.php?mod=blog
EOF;
 if($keyword) { 
$slist[blog] .= <<<EOF
&amp;srchtxt={$keywordenc}&amp;searchsubmit=yes
EOF;
 } 
$slist[blog] .= <<<EOF
"
EOF;
 if(CURMODULE == 'blog') { 
$slist[blog] .= <<<EOF
 class="a"
EOF;
 } 
$slist[blog] .= <<<EOF
>��־</a>
EOF;
?><?php } if(helper_access::check_module('album') && $_G['setting']['search']['album']['status'] && ($_G['group']['allowsearch'] & 8 || $_G['adminid'] == 1)) { ?><?php
$slist[album] = <<<EOF
<a href="search.php?mod=album
EOF;
 if($keyword) { 
$slist[album] .= <<<EOF
&amp;srchtxt={$keywordenc}&amp;searchsubmit=yes
EOF;
 } 
$slist[album] .= <<<EOF
"
EOF;
 if(CURMODULE == 'album') { 
$slist[album] .= <<<EOF
 class="a"
EOF;
 } 
$slist[album] .= <<<EOF
>���</a>
EOF;
?><?php } if($_G['setting']['groupstatus'] && $_G['setting']['search']['group']['status'] && ($_G['group']['allowsearch'] & 16 || $_G['adminid'] == 1)) { ?><?php
$slist[group] = <<<EOF
<a href="search.php?mod=group
EOF;
 if($keyword) { 
$slist[group] .= <<<EOF
&amp;srchtxt={$keywordenc}&amp;searchsubmit=yes
EOF;
 } 
$slist[group] .= <<<EOF
"
EOF;
 if(CURMODULE == 'group') { 
$slist[group] .= <<<EOF
 class="a"
EOF;
 } 
$slist[group] .= <<<EOF
>{$_G['setting']['navs']['3']['navname']}</a>
EOF;
?><?php } if(helper_access::check_module('collection') && $_G['setting']['search']['collection']['status'] && ($_G['group']['allowsearch'] & 64 || $_G['adminid'] == 1)) { ?><?php
$slist[collection] = <<<EOF
<a href="search.php?mod=collection
EOF;
 if($keyword) { 
$slist[collection] .= <<<EOF
&amp;srchtxt={$keywordenc}&amp;searchsubmit=yes
EOF;
 } 
$slist[collection] .= <<<EOF
"
EOF;
 if(CURMODULE == 'collection') { 
$slist[collection] .= <<<EOF
 class="a"
EOF;
 } 
$slist[collection] .= <<<EOF
>����</a>
EOF;
?><?php } ?><?php
$slist[user] = <<<EOF
<a href="search.php?mod=user
EOF;
 if($keyword) { 
$slist[user] .= <<<EOF
&amp;srchtxt={$keywordenc}&amp;searchsubmit=yes
EOF;
 } 
$slist[user] .= <<<EOF
"
EOF;
 if(CURMODULE == 'user') { 
$slist[user] .= <<<EOF
 class="a"
EOF;
 } 
$slist[user] .= <<<EOF
>�û�</a>
EOF;
?><?php echo implode("", $slist);; ?></div>
<table id="scform_form" cellspacing="0" cellpadding="0">
<tr>
<td class="td_srchtxt"><input type="text" id="scform_srchtxt" name="srchtxt" size="45" maxlength="40" value="<?php echo $keyword;?>" tabindex="1" x-webkit-speech speech /><script type="text/javascript">initSearchmenu('scform_srchtxt');$('scform_srchtxt').focus();</script></td>
<td class="td_srchbtn"><input type="hidden" name="searchsubmit" value="yes" /><button type="submit" id="scform_submit" class="schbtn"><strong>����</strong></button></td>
</tr>
</table>
</td>
</tr>
</table>
<?php } else { if(!empty($srchtype)) { ?><input type="hidden" name="srchtype" value="<?php echo $srchtype;?>" /><?php } if($srchtype != 'threadsort') { ?>
<div class="hm mtw ptw pbw"><h1 class="mtw ptw"><a href="./" title="<?php echo $_G['setting']['bbname'];?>"><img src="<?php echo IMGDIR;?>/logo_sc.png" alt="<?php echo $_G['setting']['bbname'];?>" /></a></a></h1></div>
<table id="scform" cellspacing="0" cellpadding="0" style="margin: 0 auto;">
<tr>
<td id="scform_tb" class="xs2">
<?php if(CURMODULE == 'forum') { ?>
<span class="y xs1">
<a href="javascript:;" id="quick_sch" class="showmenu" onmouseover="delayShow(this);">����</a>
<?php if(CURMODULE == 'forum') { ?>
<a href="search.php?mod=forum&amp;adv=yes">�߼�</a>
<?php } ?>
</span>
<?php } if(helper_access::check_module('portal') && $_G['setting']['search']['portal']['status'] && ($_G['group']['allowsearch'] & 1 || $_G['adminid'] == 1)) { ?><?php
$slist[portal] = <<<EOF
<a href="search.php?mod=portal
EOF;
 if($keyword) { 
$slist[portal] .= <<<EOF
&amp;srchtxt={$keywordenc}&amp;searchsubmit=yes
EOF;
 } 
$slist[portal] .= <<<EOF
"
EOF;
 if(CURMODULE == 'portal') { 
$slist[portal] .= <<<EOF
 class="a"
EOF;
 } 
$slist[portal] .= <<<EOF
>����</a>
EOF;
?><?php } if($_G['setting']['search']['forum']['status'] && ($_G['group']['allowsearch'] & 2 || $_G['adminid'] == 1)) { ?><?php
$slist[forum] = <<<EOF
<a href="search.php?mod=forum
EOF;
 if($keyword) { 
$slist[forum] .= <<<EOF
&amp;srchtxt={$keywordenc}&amp;searchsubmit=yes
EOF;
 } 
$slist[forum] .= <<<EOF
"
EOF;
 if(CURMODULE == 'forum') { 
$slist[forum] .= <<<EOF
 class="a"
EOF;
 } 
$slist[forum] .= <<<EOF
>����</a>
EOF;
?><?php } if(helper_access::check_module('blog') && $_G['setting']['search']['blog']['status'] && ($_G['group']['allowsearch'] & 4 || $_G['adminid'] == 1) && helper_access::check_module('blog')) { ?><?php
$slist[blog] = <<<EOF
<a href="search.php?mod=blog
EOF;
 if($keyword) { 
$slist[blog] .= <<<EOF
&amp;srchtxt={$keywordenc}&amp;searchsubmit=yes
EOF;
 } 
$slist[blog] .= <<<EOF
"
EOF;
 if(CURMODULE == 'blog') { 
$slist[blog] .= <<<EOF
 class="a"
EOF;
 } 
$slist[blog] .= <<<EOF
>��־</a>
EOF;
?><?php } if(helper_access::check_module('album') && $_G['setting']['search']['album']['status'] && ($_G['group']['allowsearch'] & 8 || $_G['adminid'] == 1) && helper_access::check_module('album')) { ?><?php
$slist[album] = <<<EOF
<a href="search.php?mod=album
EOF;
 if($keyword) { 
$slist[album] .= <<<EOF
&amp;srchtxt={$keywordenc}&amp;searchsubmit=yes
EOF;
 } 
$slist[album] .= <<<EOF
"
EOF;
 if(CURMODULE == 'album') { 
$slist[album] .= <<<EOF
 class="a"
EOF;
 } 
$slist[album] .= <<<EOF
>���</a>
EOF;
?><?php } if(helper_access::check_module('group') && $_G['setting']['search']['group']['status'] && ($_G['group']['allowsearch'] & 16 || $_G['adminid'] == 1)) { ?><?php
$slist[group] = <<<EOF
<a href="search.php?mod=group
EOF;
 if($keyword) { 
$slist[group] .= <<<EOF
&amp;srchtxt={$keywordenc}&amp;searchsubmit=yes
EOF;
 } 
$slist[group] .= <<<EOF
"
EOF;
 if(CURMODULE == 'group') { 
$slist[group] .= <<<EOF
 class="a"
EOF;
 } 
$slist[group] .= <<<EOF
>{$_G['setting']['navs']['3']['navname']}</a>
EOF;
?><?php } if(helper_access::check_module('collection') && $_G['setting']['search']['collection']['status'] && ($_G['group']['allowsearch'] & 64 || $_G['adminid'] == 1)) { ?><?php
$slist[collection] = <<<EOF
<a href="search.php?mod=collection
EOF;
 if($keyword) { 
$slist[collection] .= <<<EOF
&amp;srchtxt={$keywordenc}&amp;searchsubmit=yes
EOF;
 } 
$slist[collection] .= <<<EOF
"
EOF;
 if(CURMODULE == 'collection') { 
$slist[collection] .= <<<EOF
 class="a"
EOF;
 } 
$slist[collection] .= <<<EOF
>����</a>
EOF;
?><?php } echo implode("", $slist);; ?><a href="search.php?mod=user<?php if($keyword) { ?>&amp;srchtxt=<?php echo $keywordenc;?>&amp;searchsubmit=yes<?php } ?>"<?php if(CURMODULE == 'user') { ?> class="a"<?php } ?>>�û�</a>
</td>
</tr>
<tr>
<td>
<table cellspacing="0" cellpadding="0" id="scform_form">
<tr>
<td class="td_srchtxt"><input type="text" id="scform_srchtxt" name="srchtxt" size="65" maxlength="40" value="<?php echo $keyword;?>" tabindex="1" /><script type="text/javascript">initSearchmenu('scform_srchtxt');$('scform_srchtxt').focus();</script></td>
<td class="td_srchbtn"><input type="hidden" name="searchsubmit" value="yes" /><button type="submit" id="scform_submit" value="true"><strong>����</strong></button></td>
</tr>
</table>
</td>
</tr>
</table>
<?php } } if(CURMODULE == 'forum') { ?>
<ul id="quick_sch_menu" class="p_pop" style="display: none;">
<li><a href="search.php?mod=forum&amp;srchfrom=3600&amp;searchsubmit=yes">1 Сʱ���ڵ�����</a></li>
<li><a href="search.php?mod=forum&amp;srchfrom=14400&amp;searchsubmit=yes">4 Сʱ���ڵ�����</a></li>
<li><a href="search.php?mod=forum&amp;srchfrom=28800&amp;searchsubmit=yes">8 Сʱ���ڵ�����</a></li>
<li><a href="search.php?mod=forum&amp;srchfrom=86400&amp;searchsubmit=yes">24 Сʱ���ڵ�����</a></li>
<li><a href="search.php?mod=forum&amp;srchfrom=604800&amp;searchsubmit=yes">1 ��������</a></li>
<li><a href="search.php?mod=forum&amp;srchfrom=2592000&amp;searchsubmit=yes">1 ��������</a></li>
<li><a href="search.php?mod=forum&amp;srchfrom=15552000&amp;searchsubmit=yes">6 ��������</a></li>
<li><a href="search.php?mod=forum&amp;srchfrom=31536000&amp;searchsubmit=yes">1 ��������</a></li>
</ul>
<?php } $policymsgs = $p = '';?><?php if(is_array($_G['setting']['creditspolicy']['search'])) foreach($_G['setting']['creditspolicy']['search'] as $id => $policy) { ?><?php
$policymsg = <<<EOF

EOF;
 if($_G['setting']['extcredits'][$id]['img']) { 
$policymsg .= <<<EOF
{$_G['setting']['extcredits'][$id]['img']} 
EOF;
 } 
$policymsg .= <<<EOF
{$_G['setting']['extcredits'][$id]['title']} {$policy} {$_G['setting']['extcredits'][$id]['unit']}
EOF;
?><?php $policymsgs .= $p.$policymsg;$p = ', ';?><?php } if($policymsgs) { ?><p>ÿ����һ���������۳� <?php echo $policymsgs;?></p><?php } ?>
</form>

<div class="bm bw0">
<div class="sttl mbn"><h2>���Ӹ߼�����</h2></div>
<div class="bm_c">
<form method="post" autocomplete="off" action="search.php?mod=forum" onsubmit="if($('srchtxt_1')) searchFocus($('srchtxt_1'));">
<input type="hidden" name="formhash" value="<?php echo FORMHASH;?>" />

<table summary="����" cellspacing="0" cellpadding="0" class="tfm">
<tr>
<th>�ؼ���</th>
<td>
<input type="text" name="srchtxt" id="srchtxt_1" class="px" size="25" maxlength="40" value="<?php echo $keyword;?>" />
<?php if(($_G['group']['allowsearch'] & 32)) { ?><label><input type="checkbox" name="srchtype" class="pc" value="fulltext" <?php echo $fulltextchecked;?> onclick="if(this.checked){$('seltableid').style.display='';}else{$('seltableid').style.display='none';}"/>ȫ��</label><?php } if($posttableselect) { ?>&nbsp; <?php echo $posttableselect;?><?php } ?>
<script type="text/javascript">initSearchmenu('srchtxt_1');$('srchtxt_1').focus();</script>
</td>
</tr>

<tr>
<th>����</th>
<td><input type="text" name="srchuname" id="srchname" class="px" size="25" maxlength="40" value="<?php echo $srchuname;?>" /></td>
</tr>

<tr>
<th>���ⷶΧ</th>
<td>
<label class="lb"><input type="radio" class="pr" name="srchfilter" value="all" checked="checked" />ȫ������</label>
<label class="lb"><input type="radio" class="pr" name="srchfilter" value="digest" />��������</label>
<label class="lb"><input type="radio" class="pr" name="srchfilter" value="top" />�ö�����</label>
</td>
</tr>

<tr>
<th>��������</th>
<td>
<label class="lb"><input type="checkbox" class="pc" name="special[]" value="1" />ͶƱ����</label>
<label class="lb"><input type="checkbox" class="pc" name="special[]" value="2" />��Ʒ����</label>
<label class="lb"><input type="checkbox" class="pc" name="special[]" value="3" />��������</label>
<label class="lb"><input type="checkbox" class="pc" name="special[]" value="4" />�����</label>
<label class="lb"><input type="checkbox" class="pc" name="special[]" value="5" />��������</label>
</td>
</tr>

<tr>
<th><label for="srchfrom">����ʱ��</label></th>
<td>
<select id="srchfrom" name="srchfrom">
<option value="0">ȫ��ʱ��</option>
<option value="86400">1 ��</option>
<option value="172800">2 ��</option>
<option value="604800">1 ��</option>
<option value="2592000">1 ����</option>
<option value="7776000">3 ����</option>
<option value="15552000">6 ����</option>
<option value="31536000">1 ��</option>
</select>
<label class="lb"><input type="radio" class="pr" name="before" value="" checked="checked" />����</label>
<label class="lb"><input type="radio" class="pr" name="before" value="1" />��ǰ</label>
</td>
</tr>

<tr>
<th><label for="orderby">��������</label></th>
<td>
<select id="orderby1" name="orderby" class="ps">
<option value="lastpost" selected="selected">�ظ�ʱ��</option>
<option value="dateline">����ʱ��</option>
<option value="replies">�ظ�����</option>
<option value="views">�������</option>
</select>
<select id="orderby2" name="orderby" class="ps" style="position: absolute; display: none" disabled="disabled">
<option value="dateline" selected="selected">����ʱ��</option>
<option value="price">��Ʒ�۸�</option>
<option value="expiration">ʣ��ʱ��</option>
</select>
<label class="lb"><input type="radio" class="pr" name="ascdesc" value="asc" />����������</label>
<label class="lb"><input type="radio" class="pr" name="ascdesc" value="desc" checked="checked" />����������</label>
</td>
</tr>

<tr>
<th valign="top"><label for="srchfid">������Χ</label></th>
<td>
<select id="srchfid" name="srchfid[]" multiple="multiple" size="10" style="width: 26em;">
<option value="all"<?php if(!$srchfid) { ?> selected="selected"<?php } ?>>ȫ�����</option>
<?php echo $forumselect;?>
</select>
</td>
</tr>
<tr>
<th></th>
<td>
<input type="hidden" name="searchsubmit" value="yes" />
<button type="submit" class="pn pnc"><strong>����</strong></button>
</td>
</tr>
</table>
</form>
</div>
</div>

</div>
</div><?php include template('search/footer'); ?>