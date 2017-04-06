<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?><?php
$return = <<<EOF


EOF;
 if($config['isyoubian']) { if($config['huise']) { 
$return .= <<<EOF

<link rel=stylesheet type="text/css" href="source/plugin/ljqq/css/css_huise_right.css">

EOF;
 } else { 
$return .= <<<EOF

<link rel=stylesheet type="text/css" href="source/plugin/ljqq/css/css_right.css">

EOF;
 } } else { if($config['huise']) { 
$return .= <<<EOF

<link rel=stylesheet type="text/css" href="source/plugin/ljqq/css/css_huise.css">

EOF;
 } else { 
$return .= <<<EOF

<link rel=stylesheet type="text/css" href="source/plugin/ljqq/css/css.css">

EOF;
 } } 
$return .= <<<EOF


<div id="xixi" onmouseover="toBig()" onmouseout="toSmall()" style=" margin-top:{$config['ztpx']}px;z-index:88;">
<table 
EOF;
 if($config['isyoubian']) { 
$return .= <<<EOF
style="FLOAT: right" border="0" cellspacing="0" cellpadding="0" width="157"
EOF;
 } else { 
$return .= <<<EOF
style="FLOAT: left" border="0" cellspacing="0" cellpadding="0" width="157"
EOF;
 } 
$return .= <<<EOF
>
  <tbody>
  <tr>
    <td class="main_head" height="39" valign="top">&nbsp;</td>
  </tr>
  <tr>
    <td class="info1" valign="top">
      <table class="qqtable" border="0" cellspacing="0" cellpadding="0" width="120" align="center">
        <tbody>
<tr>
          <td align="middle"> &nbsp;</td>
        </tr>

EOF;
 if($config['logo']) { 
$return .= <<<EOF

        <tr>
          <td align="middle"><a href="{$_G['siteurl']}" target="_blank"><img border="0" src="{$config['logo']}"></a> </td>
        </tr>

EOF;
 } 
$return .= <<<EOF

        <tr>
          <td height="5"></td>
  </tr>
EOF;
 if(is_array($qqgroup)) foreach($qqgroup as $k=>$v) { 
$return .= <<<EOF
<tr>
          <td height="30" align="middle"><span>{$v}</span></td>
  </tr>
  
EOF;
 } 
$return .= <<<EOF

        <tr>
          <td height="5"></td></tr>
  
EOF;
 if(is_array($wwgroup)) foreach($wwgroup as $k=>$v) { 
$return .= <<<EOF
  <tr>
          <td height="30" align="middle"><span>{$v}</span></td></tr>
       
EOF;
 } 
$return .= <<<EOF

        <tr>
          <td height="5"></td>
</tr> 
EOF;
 if(is_array($newgroup)) foreach($newgroup as $k=>$v) { 
$return .= <<<EOF
        <tr>
          <td align="middle">
            <div class="qun">
EOF;
 if($config['huise']) { 
$return .= <<<EOF
{$v['0']}
EOF;
 } else { 
$return .= <<<EOF
<font color="#FF6600">{$v['0']}</font>
EOF;
 } 
$return .= <<<EOF
<br><span>{$v['1']}</span></div></td>
</tr>
<tr>
          <td height="5"><img border="0" src="source/plugin/ljqq/images/img3-5_3.png"></td>
</tr>
          
EOF;
 } 
$return .= <<<EOF

 <tr>
 
          <td align="middle">
  
EOF;
 if(is_array($works)) foreach($works as $k=>$v) { 
$return .= <<<EOF
            <div class="qun">
EOF;
 if($config['huise']) { 
$return .= <<<EOF
{$v['0']}<br><span>{$v['1']}</span>
EOF;
 } else { 
$return .= <<<EOF
<font color="#FF6600">{$v['0']}</font><br><span>{$v['1']}</span>
EOF;
 } 
$return .= <<<EOF
</div>

EOF;
 } 
$return .= <<<EOF

</td>

</tr>

EOF;
 if($config['weixin']) { 
$return .= <<<EOF

<tr>
          <td align="middle">
EOF;
 if($config['huise']) { 
$return .= <<<EOF
{$config['tsy']}
EOF;
 } else { 
$return .= <<<EOF
<font color="#FF6600">{$config['tsy']}</font>
EOF;
 } 
$return .= <<<EOF
</td>
  </tr>
<tr>
          <td align="middle"><img border="0" src="{$config['weixin']}"></td>
        </tr>

EOF;
 } 
$return .= <<<EOF

          </tbody></table></td></tr>
<tr>
<td class="down_kefu" valign="top"></td>
</tr></tbody></table>
<div class="Obtn" style="MARGIN-TOP: {$config['tbpx']}px;"></div></div>

EOF;
 if($config['isyoubian']) { 
$return .= <<<EOF

<script type="text/javascript">
function aaa(id,_top,_right){
var me=id.charAt?document.getElementById(id):id, d1=document.body, d2=document.documentElement;
//d1.style.height=d2.style.height='100%';
me.style.top=_top?_top+'px':0;me.style.right=_right+"px";//[(_right>0?'right':'right')]=_right?Math.abs(_right)+'px':0;
me.style.position='absolute';
setInterval(function (){me.style.top=parseInt(me.style.top)+(Math.max(d1.scrollTop,d2.scrollTop)+_top-parseInt(me.style.top))*0.1+'px';},10+parseInt(Math.random()*20));
return arguments.callee;
}



aaa('xixi',100,{$px});
</SCRIPT>

<script type="text/javascript">
lastScrollY=0; 

var InterTime = 1;
var maxWidth=-1;
var minWidth=-152;
var numInter = 8;

var BigInter ;
var SmallInter ;

var aljo =  document.getElementById("xixi");
var i = parseInt(aljo.style.right);
function Big()
{
if(parseInt(aljo.style.right)<maxWidth)
{
i = parseInt(aljo.style.right);
i += numInter;	
aljo.style.right=i+"px";	
if(i==maxWidth)
clearInterval(BigInter);
}
}
function toBig()
{
clearInterval(SmallInter);
clearInterval(BigInter);
BigInter = setInterval(Big,InterTime);
}
function Small()
{
if(parseInt(aljo.style.right)>minWidth)
{
i = parseInt(aljo.style.right);
i -= numInter;
aljo.style.right=i+"px";

if(i==minWidth)
clearInterval(SmallInter);
}
}
function toSmall()
{
clearInterval(SmallInter);
clearInterval(BigInter);
SmallInter = setInterval(Small,InterTime);

}

</SCRIPT>

EOF;
 } else { 
$return .= <<<EOF

<script type="text/javascript">
function aaa(id,_top,_left){
var me=id.charAt?document.getElementById(id):id, d1=document.body, d2=document.documentElement;
//d1.style.height='100%';d2.style.height='100%';
me.style.top=_top?_top+'px':0;me.style.left=_left+"px";//[(_left>0?'left':'left')]=_left?Math.abs(_left)+'px':0;
me.style.position='absolute';
setInterval(function (){me.style.top=parseInt(me.style.top)+(Math.max(d1.scrollTop,d2.scrollTop)+_top-parseInt(me.style.top))*0.1+'px';},10+parseInt(Math.random()*20));
return arguments.callee;
}



aaa('xixi',100,{$px});
</SCRIPT>

<script type="text/javascript">
lastScrollY=0; 

var InterTime = 1;
var maxWidth=-1;
var minWidth=-152;
var numInter = 8;

var BigInter ;
var SmallInter ;

var aljo =  document.getElementById("xixi");
var i = parseInt(aljo.style.left);
function Big()
{
if(parseInt(aljo.style.left)<maxWidth)
{
i = parseInt(aljo.style.left);
i += numInter;	
aljo.style.left=i+"px";	
if(i==maxWidth)
clearInterval(BigInter);
}
}
function toBig()
{
clearInterval(SmallInter);
clearInterval(BigInter);
BigInter = setInterval(Big,InterTime);
}
function Small()
{
if(parseInt(aljo.style.left)>minWidth)
{
i = parseInt(aljo.style.left);
i -= numInter;
aljo.style.left=i+"px";

if(i==minWidth)
clearInterval(SmallInter);
}
}
function toSmall()
{
clearInterval(SmallInter);
clearInterval(BigInter);
SmallInter = setInterval(Small,InterTime);

}

</SCRIPT>

EOF;
 } 
$return .= <<<EOF


EOF;
?>
