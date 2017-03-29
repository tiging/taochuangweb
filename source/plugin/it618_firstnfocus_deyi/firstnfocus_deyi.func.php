<?php
/**
 *	开发团队：IT618资讯网
 *	it618_copyright 插件设计：<a href="http://www.cnit618.com" target="_blank" title="为站长解决问题的网站">IT618资讯网</a>
 */
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function it618_nfocusdeyi_gettui($blockname){
	if(trim($blockname)=="")return "";
	$blockid=DB::result_first("select bid from ".DB::table('common_block')." where name='$blockname'");

	block_get_batch($blockid);
	$fl_html = block_fetch_content($blockid, true);
	//global $_G;
//	
//	$url=$_G['siteurl']."api.php?mod=js&bid=".$blockid;
//	$fl_html = file_get_contents($url);
//	$fl_html = str_replace("document.write('","",$fl_html);
//	$fl_html = str_replace("');","",$fl_html);
//	$fl_html = str_replace(array("\\r\\n", "\\r", "\\n","\\"),"",$fl_html);
	
	return it618_nfocusdeyi_rewriteurl($fl_html);
}

function it618_nfocusdeyi_rewriteurl($fl_html){
	global $_G;
	if($_G['cache']['plugin']['it618_firstnfocus_deyi']['rewriteurl']==1){
		//	forum.php?mod=forumdisplay&fid=2
		//	forum-2-1.html
		$tmparr=explode("forum.php?mod=forumdisplay",$fl_html);
		if(count($tmparr)>1){
			$fl_html="";
			foreach($tmparr as $key => $tmp){
				if(strpos($tmp,"fid=")==1){
					$tmp=str_replace("&fid=","forum-",$tmp);
					$tmparr1=explode('"',$tmp,2);
					$fl_html.=$tmparr1[0].'-1.html"'.$tmparr1[1];
				}else{
					$fl_html.=$tmp;
				}
			}
		}
		//	forum.php?mod=viewthread&tid=43
		//	thread-43-1-1.html
		$tmparr=explode("forum.php?mod=viewthread",$fl_html);
		if(count($tmparr)>1){
			$fl_html="";
			foreach($tmparr as $key => $tmp){
				if(strpos($tmp,"tid=")==1){
					$tmp=str_replace("&tid=","thread-",$tmp);
					$tmparr1=explode('"',$tmp,2);
					$fl_html.=$tmparr1[0].'-1-1.html"'.$tmparr1[1];
				}else{
					$fl_html.=$tmp;
				}
			}
		}
		
		$tmparr=explode("do=album&id=",$fl_html);
		if(count($tmparr)>1){return $fl_html;}
		
		//	home.php?mod=space&uid=5
		//	space-uid-5.html
		$tmparr=explode("home.php?mod=space",$fl_html);
		if(count($tmparr)>1){
			$fl_html="";
			foreach($tmparr as $key => $tmp){
				if(strpos($tmp,"uid=")==1){
					$tmp=str_replace("&uid=","space-uid-",$tmp);
					$tmparr1=explode('"',$tmp,2);
					$fl_html.=$tmparr1[0].'.html"'.$tmparr1[1];
				}else{
					$fl_html.=$tmp;
				}
			}
		}
		//	space-uid-1&do=blog&id=1.html
		//	blog-1-1.html
		$tmparr=explode("&do=blog&id=",$fl_html);
		if(count($tmparr)>1){
			$tmparr=explode('"',$fl_html);
			$fl_html="";
			foreach($tmparr as $key => $tmp){
				if(strpos($tmp,"do=blog&id=")>1){
					$tmp=str_replace("space-uid-","blog-",$tmp);
					$tmp=str_replace("&do=blog&id=","-",$tmp);
					$fl_html.=$tmp;
				}else{
					$fl_html.=$tmp;
				}
			}
		}
	}
	return $fl_html;
}
?>