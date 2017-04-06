<?php
/**
 *	[【星辰】转载地址(crx349_copyrighted.{modulename})] (C)2013-2099 Powered by 无限星辰|www.xmspace.net.
 *	Version: V1.0
 *	Date: 2013-6-26 00:38
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class plugin_crx349_copyrighted {
	//TODO - Insert your code here

}

class plugin_crx349_copyrighted_forum extends plugin_crx349_copyrighted{
    function viewthread_postbottom_output($template = array()){
global $_G,$postlist;

$siteurl=$_G['siteurl'];
$crx_on=$_G ['cache'] ['plugin'] ['crx349_copyrighted'] ['crx349_on'];
$crx_re=$_G ['cache'] ['plugin'] ['crx349_copyrighted'] ['crx349_re'];
if($crx_on==1){
foreach ($postlist as $key => $value){
if ($value['first']==1){
if($crx_re==1){
		return array('<br/><div style=margin: 20px 0px;><font size="3px"  color="red">转载请说明出处，本文地址：</font><a href='.$siteurl.'thread-'.(isset($_GET['tid'])?intval($_GET['tid']):0).'-1-1.html onclick=return copyThreadUrl(this) title='.$_G[forum_thread][subject].'>'.$siteurl.'thread-'.(isset($_GET['tid'])?intval($_GET['tid']):0).'-1-1.html</a></div>');
		       }else{
return array('<br/><div style=margin: 20px 0px;><font size="3px"  color="red">转载请说明出处，本文地址：</font><a href='.$siteurl.'forum.php?mod=viewthread&tid='.(isset($_GET['tid'])?intval($_GET['tid']):0).' onclick=return copyThreadUrl(this) title='.$_G[forum_thread][subject].'>'.$siteurl.'forum.php?mod=viewthread&tid='.(isset($_GET['tid'])?intval($_GET['tid']):0).'</a></div>');
                 }
				 }
				 }
				 
               }
    }

}

?>