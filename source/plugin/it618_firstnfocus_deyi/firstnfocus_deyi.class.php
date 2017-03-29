<?php
/**
 *	开发团队：IT618资讯网
 *	it618_copyright 插件设计：<a href="http://www.cnit618.com" target="_blank" title="为站长解决问题的网站">IT618资讯网</a>
 */


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class plugin_it618_firstnfocus_deyi_forum {
	function it618_hook(){
		global $_G;
		$it618_firstnfocus_deyi = $_G['cache']['plugin']['it618_firstnfocus_deyi'];
		require_once libfile('function/cache');
		require_once DISCUZ_ROOT.'./source/plugin/it618_firstnfocus_deyi/firstnfocus_deyi.func.php';
		require_once DISCUZ_ROOT.'./config/config_ucenter.php';
		
		$blocknames=explode(",",lang('plugin/it618_firstnfocus_deyi', 'it618_string'));
		$cache_file = DISCUZ_ROOT.'./data/sysdata/cache_it618_firstnfocus_deyi.php';

		if(($_G['timestamp'] - @filemtime($cache_file)) > $it618_firstnfocus_deyi['cachetime']*60) {
include_once libfile('function/block');
loadcache('blockclass');
$subnames=explode(",",$it618_firstnfocus_deyi['subnames']);

$left_subnames_arr=explode("|",str_replace(array("\r\n", "\r", "\n"), '|', $it618_firstnfocus_deyi['left_subnames']));
foreach($left_subnames_arr as $key => $left_subname){
	if($left_subname!=""){
		$tmparr=explode("==",$left_subname);
		$left_subnames_names[]=$tmparr[0];
		$left_subnames_urls[]=$tmparr[1];
	}
}
$right_subnames_arr=explode("|",str_replace(array("\r\n", "\r", "\n"), '|', $it618_firstnfocus_deyi['right_subnames']));
foreach($right_subnames_arr as $key => $right_subname){
	if($right_subname!=""){
		$tmparr=explode("==",$right_subname);
		$right_subnames_names[]=$tmparr[0];
		$right_subnames_urls[]=$tmparr[1];
	}
}
$right_lx_arr=explode("|",str_replace(array("\r\n", "\r", "\n"), '|', $it618_firstnfocus_deyi['right_lx']));
foreach($right_lx_arr as $key => $right_lx){
	if($right_lx!=""){
		$tmparr=explode("==",$right_lx);
		$right_lx_names[]=$tmparr[0];
		$right_lx_urls[]=$tmparr[1];
	}
}
$csscolor=$it618_firstnfocus_deyi['csscolor'];

if($it618_firstnfocus_deyi['rigth_lxstyle']==1){
	$rigth_lx='<P><A class=cl_phone href="'.$right_lx_urls[0].'" 
target=_blank>'.$right_lx_names[0].'</A> <A href="'.$right_lx_urls[1].'" 
target=_blank>'.$right_lx_names[1].'</A> <A href="'.$right_lx_urls[2].'" 
target=_blank>'.$right_lx_names[2].'</A></P>';
}else{
	$rigth_lx='<P><A href="'.$right_lx_urls[0].'" 
target=_blank>'.$right_lx_names[0].'</A> <A href="'.$right_lx_urls[1].'" 
target=_blank>'.$right_lx_names[1].'</A> <A href="'.$right_lx_urls[2].'" 
target=_blank>'.$right_lx_names[2].'</A> <A href="'.$right_lx_urls[3].'" 
target=_blank>'.$right_lx_names[3].'</A></P>';
}

$left_imgheight=$it618_firstnfocus_deyi['left_imgheight'];
$left_imgheightUL=$left_imgheight-57;
$left_imgheighttop=632-$left_imgheight;

if($it618_firstnfocus_deyi['rigth_lxstyle']==1){
$styletmp='#focus_right P {
	PADDING-RIGHT: 0px; PADDING-LEFT: 0px; BACKGROUND: url(source/plugin/it618_firstnfocus_deyi/css/pic/s.png) no-repeat 0px -300px; PADDING-BOTTOM: 0px; MARGIN: 0px; PADDING-TOP: 0px; HEIGHT: 88px
}
#focus_right P A {
	DISPLAY: inline-block; FONT-SIZE: 14px; FLOAT: left; MARGIN: 7px; WIDTH: 66px; COLOR:#000; HEIGHT: 30px; TEXT-ALIGN: left; TEXT-DECORATION: none; padding-left:29px; line-height:28px
}';}else{
$styletmp='#focus_right P {
	PADDING-RIGHT: 0px; PADDING-LEFT: 0px; BACKGROUND: url(source/plugin/it618_firstnfocus_deyi/css/pic/s1.png) no-repeat 0px -300px; PADDING-BOTTOM: 0px; MARGIN: 0px; PADDING-TOP: 0px; HEIGHT: 88px
}
#focus_right P A {
	DISPLAY: inline-block; FONT-SIZE: 13px; FLOAT: left; WIDTH: 97px; COLOR:#FFF; HEIGHT: 30px; TEXT-ALIGN:center; TEXT-DECORATION: none; line-height:30px; margin-top:9px; margin-left:9px; font-weight:bold
}';}

$styletmp.='#play {height:'.$left_imgheight.'px}
#play UL {
	Z-INDEX: 100; MARGIN: '.$left_imgheightUL.'px 0px 0px 10px; POSITION: absolute
}
#play IMG {HEIGHT: '.$left_imgheight.'px}
#top {height:'.$left_imgheighttop.'px}';

$strall='
<style>
.cl_green {
	COLOR: '.$csscolor.'
}
.cl_green:hover {
	COLOR: '.$csscolor.'
}
.cl_yellow {
	COLOR: '.$csscolor.'
}
.cl_yellow:hover {
	COLOR: '.$csscolor.'
}
.cl_yellow A {
	COLOR: '.$csscolor.'
}
.cl_people em{color:'.$csscolor.'; padding-left:20px}
#focus_center H3 A {
	COLOR: '.$csscolor.'
}
#focus_center strong{font-size:18px; color:'.$csscolor.'}
#focus_center UL LI A.cl_topic {
	COLOR: '.$csscolor.'
}
'.$styletmp.'
</style>
<DIV id=container>
<DIV id=focus>
<DIV id=focus_left>
<DIV id=play>
<UL>
  <LI>1 </LI>
  <LI>2 </LI>
  <LI class=cl_active>3 </LI>
  <LI>4 </LI>
  <LI>5 </LI></UL>
  '.it618_nfocusdeyi_gettui($blocknames[0]).'
</DIV>
<SCRIPT type=text/javascript>Mo("#play a").hide();Mo( Mo("#play a").item(0) ).show();</SCRIPT>

<DIV id=top>
<OL>
  <LI><A href="'.$left_subnames_urls[0].'" target=_blank>'.$left_subnames_names[0].'</A> </LI>
  <LI id=id_center><A href="'.$left_subnames_urls[1].'" target=_blank>'.$left_subnames_names[1].'</A> </LI>
  <LI><A href="'.$left_subnames_urls[2].'" target=_blank>'.$left_subnames_names[2].'</A> </LI></OL>
<UL style="DISPLAY: none">'.it618_nfocusdeyi_gettui($blocknames[4]).'</UL>
<UL style="DISPLAY: none">'.it618_nfocusdeyi_gettui($blocknames[5]).'</UL>
<UL>'.it618_nfocusdeyi_gettui($blocknames[6]).'</UL></DIV></DIV>
<DIV id=focus_center>
<DL>
  <DT><SPAN>'.$it618_firstnfocus_deyi['mid_weather'].'</SPAN><STRONG>'.$it618_firstnfocus_deyi['mainname'].'</STRONG> 
  <DD>
  '.it618_nfocusdeyi_gettui($blocknames[1]).'
  <DIV class=cl_line></DIV>
  <UL>'.it618_nfocusdeyi_gettui($blocknames[2]).'</UL>
  <DIV class=cl_line></DIV>
  <UL>'.it618_nfocusdeyi_gettui($blocknames[3]).'</UL></DD></DL></DIV>
<DIV id=focus_right>
'.$rigth_lx.'
<DL>
  <DD id=bind_star>
  <UL class=cl_option>
    <LI><STRONG><A class=cl_yellow 
    href="'.$right_subnames_urls[0].'" 
    target=_blank>'.$right_subnames_names[0].'</A></STRONG> </LI>
    <LI><STRONG><A class=cl_yellow 
    href="'.$right_subnames_urls[1].'" 
    target=_blank>'.$right_subnames_names[1].'</A></STRONG> </LI></UL>
  <DIV class=ppMagezine rel="box">'.$it618_firstnfocus_deyi['right_ad1'].'</DIV>
  <div rel="box" class="ppMagezine" style="display:none">'.$it618_firstnfocus_deyi['right_ad1_1'].'</div>
  </DD>
  <DD id=bind_naver>
  <UL class=cl_option>
    <LI><STRONG><A class=cl_yellow 
    href="'.$right_subnames_urls[2].'" target=_blank 
    rel=nofollow>'.$right_subnames_names[2].'</A></STRONG> </LI>
    <LI><STRONG><A class=cl_yellow 
    href="'.$right_subnames_urls[3].'" target=_blank 
    rel=nofollow>'.$right_subnames_names[3].'</A></STRONG> </LI></UL>
  <DIV class=ppInfo rel="box">'.$it618_firstnfocus_deyi['right_urls'].'</DIV>
  <div rel="box" class="ppInfo" style="display:none">'.$it618_firstnfocus_deyi['right_urls_1'].'  </DIV>
  </DD>
  <DD class=cl_split></DD>
  <DT><SPAN class="cl_more cl_more_bwhite"><A 
  href="'.$right_subnames_urls[4].'" 
  target=_blank>'.lang('plugin/it618_firstnfocus_deyi', 'it618_more').'</A></SPAN> <STRONG class="cl_people cl_people_topic"><em>'.$right_subnames_names[4].'</em></STRONG> </DT>
  <DD>
  <OL>
    <LI>'.$it618_firstnfocus_deyi['right_ad2'].'</LI>
    <LI>'.$it618_firstnfocus_deyi['right_ad3'].'</LI>
    <LI>'.$it618_firstnfocus_deyi['right_ad4'].'</LI>
	</OL></DD></DL></DIV></DIV>
			</td>
			</tr>';
			
			$contents[]=$strall;
			$cacheArray .= "\$contents=".arrayeval($contents).";\n";
			writetocache('it618_firstnfocus_deyi', $cacheArray);	
	}
	else{
			include_once DISCUZ_ROOT.'./data/sysdata/cache_it618_firstnfocus_deyi.php';
			$strall=$contents[0];
	}
		
		
		$usergroup=(array)unserialize($it618_firstnfocus_deyi['usergroup']);
		if(empty($usergroup[0]) || in_array($_G['groupid'], $usergroup)){
			include template('it618_firstnfocus_deyi:firstnfocus_deyi');
			return $it618_firstnfocus_deyi_block;
		}
		
	}
	
	function index_top(){
		global $_G;
		$it618_firstnfocus_deyi = $_G['cache']['plugin']['it618_firstnfocus_deyi'];
		if($it618_firstnfocus_deyi['indextop']==1)return $this->it618_hook();else return "";
	}
}

class plugin_it618_firstnfocus_deyi extends plugin_it618_firstnfocus_deyi_forum{
	
	function global_header(){
		$blockname="it618_firstnfocus_deyi";
		$blockcount=DB::result_first("select count(1) from ".DB::table('common_block')." where name='".$blockname."' and blockclass=0");
		
		if($blockcount>0){
			$strContent=$this->it618_hook();
			
			$setarr = array(
				'summary' => $strContent,
				'dateline' => $_G['timestamp']
			);
			DB::update('common_block', $setarr, "name='".$blockname."' and blockclass=0");
		}
	}

}
?>