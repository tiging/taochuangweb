<?php
/**
 *	[最新100主题(kx_new100.{modulename})] (C)2013-2099 Powered by .
 *	Version: 1.0
 *	Date: 2013-11-5 19:44
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$config = $_G['cache']['plugin']['kx_new100'];
$navtitle = $config['title'];
$metakeywords = $config['keywords'];
$metadescription = $config['description'];
$nobk = $config['nobk'];
$query = DB::query("SELECT pt.tid,pt.fid,pt.author,pt.authorid,pt.subject,pt.views,pt.replies,pt.dateline,pf.name FROM ".DB::table('forum_thread')." pt LEFT JOIN ".DB::table('forum_forum')." pf USING(fid) WHERE fid not in($nobk) and pt.displayorder>=0 ORDER BY dateline DESC limit 0,100");
	while ($value = DB::fetch($query)) {
        $value['dateline'] = date('Y-m-d H:i',$value['dateline']);
		$devdb[] = $value;
	}
	include template('kx_new100:index');
//TODO - Insert your code here
?>