<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); function tpl_hide_credits_hidden($creditsrequire) {
global $_G;?><?php
$return = <<<EOF
<div class="locked">
EOF;
 if($_G['uid']) { 
$return .= <<<EOF
{$_G['username']}
EOF;
 } else { 
$return .= <<<EOF
�ο�
EOF;
 } 
$return .= <<<EOF
���������ص�������Ҫ���ָ��� {$creditsrequire} �ſ����������ǰ����Ϊ {$_G['member']['credits']}</div>
EOF;
?><?php return $return;?><?php }

function tpl_hide_credits($creditsrequire, $message) {?><?php
$return = <<<EOF
<div class="locked">����������Ҫ���ָ��� {$creditsrequire} �ſ����</div>
{$message}<br /><br />

EOF;
?><?php return $return;?><?php }

function tpl_codedisp($code) {?><?php
$return = <<<EOF
<div class="blockcode"><div><ol><li>{$code}</ol></div></div>
EOF;
?><?php return $return;?><?php }

function tpl_quote() {?><?php
$return = <<<EOF
<div class="grey quote"><blockquote>����: \\1</blockquote></div>
EOF;
?><?php return $return;?><?php }

function tpl_free() {?><?php
$return = <<<EOF
<div class="grey quote"><blockquote>\\1</blockquote></div>
EOF;
?><?php return $return;?><?php }

function tpl_hide_reply() {
global $_G;?><?php
$return = <<<EOF
<div class="showhide"><h4>�������ص�����</h4>\\1</div>

EOF;
?><?php return $return;?><?php }

function tpl_hide_reply_hidden() {
global $_G;?><?php
$return = <<<EOF
<div class="locked">
EOF;
 if($_G['uid']) { 
$return .= <<<EOF
{$_G['username']}
EOF;
 } else { 
$return .= <<<EOF
�ο�
EOF;
 } 
$return .= <<<EOF
�������Ҫ�鿴��������������<a href="forum.php?mod=post&amp;action=reply&amp;fid={$_G['fid']}&amp;tid={$_G['tid']}" onclick="showWindow('reply', this.href)">�ظ�</a></div>
EOF;
?><?php return $return;?><?php }

function attachlist($attach) {
global $_G;
$attach['refcheck'] = (!$attach['remote'] && $_G['setting']['attachrefcheck']) || ($attach['remote'] && ($_G['setting']['ftp']['hideurl'] || ($attach['isimage'] && $_G['setting']['attachimgpost'] && strtolower(substr($_G['setting']['ftp']['attachurl'], 0, 3)) == 'ftp')));
$aidencode = packaids($attach);
$is_archive = $_G['forum_thread']['is_archived'] ? "&fid=".$_G['fid']."&archiveid=".$_G[forum_thread][archiveid] : '';?><?php
$return = <<<EOF


EOF;
 if(!$attach['price'] || $attach['payed']) { 
$return .= <<<EOF

<div class="box box_ex2 attach">
<dd>
<p class="attnm">

EOF;
 if($_G['setting']['mobile']['mobilesimpletype'] == 0) { 
$return .= <<<EOF

{$attach['attachicon']}

EOF;
 } if(!$attach['price'] || $attach['payed']) { 
$return .= <<<EOF

<a href="forum.php?mod=attachment{$is_archive}&amp;aid={$aidencode}" id="aid{$attach['aid']}" target="_blank">{$attach['filename']}</a>

EOF;
 } else { 
$return .= <<<EOF

<a href="forum.php?mod=misc&amp;action=attachpay&amp;aid={$attach['aid']}&amp;tid={$attach['tid']}">{$attach['filename']}</a>

EOF;
 } 
$return .= <<<EOF

<span class="xg1">({$attach['dateline']} �ϴ�)</span>
</p>
<p class="xg1">{$attach['attachsize']}
EOF;
 if($attach['readperm']) { 
$return .= <<<EOF
, �Ķ�Ȩ��: <strong>{$attach['readperm']}</strong>
EOF;
 } 
$return .= <<<EOF
, ���ش���: {$attach['downloads']}
EOF;
 if(!$attach['attachimg'] && $_G['getattachcredits']) { 
$return .= <<<EOF
, ���ػ���: {$_G['getattachcredits']}
EOF;
 } 
$return .= <<<EOF
</p>

EOF;
 if($attach['description']) { 
$return .= <<<EOF
<p class="xg2">{$attach['description']}</p>
EOF;
 } 
$return .= <<<EOF

</dd>
</div>

EOF;
 } 
$return .= <<<EOF


EOF;
?><?php return $return;?><?php }

function imagelist($attach) {
global $_G, $post;
$fix = count($post[imagelist]) == 1 ? 560 : 88;
$fixtype = count($post[imagelist]) == 1 ? 'fixnone' : 'fixwr';
$attach['refcheck'] = (!$attach['remote'] && $_G['setting']['attachrefcheck']) || ($attach['remote'] && ($_G['setting']['ftp']['hideurl'] || ($attach['isimage'] && $_G['setting']['attachimgpost'] && strtolower(substr($_G['setting']['ftp']['attachurl'], 0, 3)) == 'ftp')));
$mobilethumburl = $attach['attachimg'] && $_G['setting']['showimages'] && (!$attach['price'] || $attach['payed']) && ($_G['group']['allowgetimage'] || $_G['uid'] == $attach['uid']) ? getforumimg($attach['aid'], 0, $fix, $fix, $fixtype) : '' ;
$aidencode = packaids($attach);
$is_archive = $_G['forum_thread']['is_archived'] ? "&fid=".$_G['fid']."&archiveid=".$_G[forum_thread][archiveid] : '';?><?php
$return = <<<EOF


EOF;
 if($attach['attachimg'] && $_G['setting']['showimages'] && ($_G['group']['allowgetimage'] || $_G['uid'] == $attach['uid']) && (!$attach['price'] || $attach['payed'])) { if(!$attach['price'] || $attach['payed']) { if($_G['setting']['mobile']['mobilesimpletype'] == 0) { 
$return .= <<<EOF

<li><a href="forum.php?mod=viewthread&amp;tid={$attach['tid']}&amp;aid={$attach['aid']}&amp;from=album&amp;page={$_G['page']}" class="orange"><img id="aimg_{$attach['aid']}" src="{$mobilethumburl}" style="width:50%" /></a>
</li>

EOF;
 } } } 
$return .= <<<EOF


EOF;
?><?php return $return;?><?php }

function attachinpost($attach) {
global $_G;
$attach['refcheck'] = (!$attach['remote'] && $_G['setting']['attachrefcheck']) || ($attach['remote'] && ($_G['setting']['ftp']['hideurl'] || ($attach['isimage'] && $_G['setting']['attachimgpost'] && strtolower(substr($_G['setting']['ftp']['attachurl'], 0, 3)) == 'ftp')));
$mobilethumburl = $attach['attachimg'] && $_G['setting']['showimages'] && (!$attach['price'] || $attach['payed']) && ($_G['group']['allowgetimage'] || $_G['uid'] == $attach['uid']) ? getforumimg($attach['aid'], 0, 1120, 1120, 'fixnone') : '' ;
$aidencode = packaids($attach);
$is_archive = $_G['forum_thread']['is_archived'] ? '&fid='.$_G['fid'].'&archiveid='.$_G[forum_thread][archiveid] : '';?><?php
$return = <<<EOF


EOF;
 if($attach['attachimg'] && $_G['setting']['showimages'] && (!$attach['price'] || $attach['payed']) && ($_G['group']['allowgetimage'] || $_G['uid'] == $attach['uid'])) { if($_G['setting']['mobile']['mobilesimpletype'] == 0) { 
$return .= <<<EOF

<a href="forum.php?mod=viewthread&amp;tid={$attach['tid']}&amp;aid={$attach['aid']}&amp;from=album&amp;page={$_G['page']}" class="orange"><img id="aimg_{$attach['aid']}" src="{$mobilethumburl}" /></a>

EOF;
 } } else { if(!$attach['price'] || $attach['payed']) { 
$return .= <<<EOF

<div id="attach_{$attach['aid']}" class="box attach mbn" >

EOF;
 if($_G['setting']['mobile']['mobilesimpletype'] == 0) { 
$return .= <<<EOF

{$attach['attachicon']}

EOF;
 } if(!$attach['price'] || $attach['payed']) { 
$return .= <<<EOF

<a href="forum.php?mod=attachment{$is_archive}&amp;aid={$aidencode}" target="_blank">{$attach['filename']}</a>

EOF;
 } else { 
$return .= <<<EOF

<a href="forum.php?mod=misc&amp;action=attachpay&amp;aid={$attach['aid']}&amp;tid={$attach['tid']}" target="_blank">{$attach['filename']}</a>

EOF;
 } 
$return .= <<<EOF

<em class="xg1">({$attach['attachsize']})</em>
<em class="xg1"><br />(���ش���: {$attach['downloads']}, {$attach['dateline']} �ϴ�)
</em>

EOF;
 if(!$attach['attachimg'] && $_G['getattachcredits']) { 
$return .= <<<EOF
<p>���ػ���: {$_G['getattachcredits']}</p>
EOF;
 } 
$return .= <<<EOF

</div>

EOF;
 } } 
$return .= <<<EOF


EOF;
?><?php return $return;?><?php }?>