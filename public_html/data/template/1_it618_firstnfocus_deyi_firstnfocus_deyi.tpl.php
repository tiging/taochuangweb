<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?><?php
$it618_firstnfocus_deyi_block = <<<EOF


<script src="source/plugin/it618_firstnfocus_deyi/js/mo.js" type="text/javascript" type=text/javascript></script>
<script src="source/plugin/it618_firstnfocus_deyi/js/mo.ui.js" type="text/javascript" type=text/javascript></script>
<LINK href="source/plugin/it618_firstnfocus_deyi/css/common.css" type=text/css rel=stylesheet>

{$strall}

EOF;
 if($it618_firstnfocus_deyi['diyad']!='') { 
$it618_firstnfocus_deyi_block .= <<<EOF

<tr>
<td>
<div style="clear:both; margin-top:10px">{$it618_firstnfocus_deyi['diyad']}</div>
</td>
</tr>

EOF;
 } if($it618_firstnfocus_deyi['code']!='http://www.cnit618.com') { 
$it618_firstnfocus_deyi_block .= <<<EOF

<tr>
<td>
插件设计：<a href="http://www.cnit618.com" target="_blank" title="为站长解决问题的网站">IT618资讯网</a>
</td>
</tr>

EOF;
 } 
$it618_firstnfocus_deyi_block .= <<<EOF

</table>
<script src="source/plugin/it618_firstnfocus_deyi/js/function.js" type="text/javascript" stype=text/javascript></script>
<div style="clear:both; margin-bottom:10px"></div>

EOF;
?>