<?php

/**
 *      (C)2007-2012 singcere.net
 *      This is a freeware, But you have no right to modify or distribute
 *
 *      $Id$
 */

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}


if(submitcheck('userinfo', true)) {
    $bindmember = C::t('#singcere_wechat#singcere_wechat_bind')->fetch_by_openid($_GET['openid']);
    if($bindmember) {
    	require_once libfile('function/misc');
    	require_once libfile('function/forum');
    	require_once libfile('function/post');
        $threadlist = C::t('forum_thread')->fetch_all_by_authorid_displayorder($bindmember['uid'], null, null, '=', null, 0, 5);
        foreach($threadlist as &$t) {
        	$t = procthread($t);
        }


		$posts = C::t('forum_post')->fetch_all_by_authorid(0, $bindmember['uid'], true, 'DESC', 0, 5, 0);
		foreach($posts as $pid => $post) {
			$delrow = false;
			if($post['anonymous'] && $post['authorid'] != $_G['uid']) {
				$delrow = true;
			} elseif($viewuserthread && $post['authorid'] != $_G['uid']) {
				if(($_G['adminid'] != 1 && !empty($viewfids) && !in_array($post['fid'], $viewfids))) {
					$delrow = true;
				}
			}
			if($delrow) {
				unset($posts[$pid]);
				$hiddennum++;
				continue;
			} else {
				$tids[$post['tid']][] = $pid;
				$post['message'] = !getstatus($post['status'], 2) || $post['authorid'] == $_G['uid'] ? messagecutstr($post['message'], 100) : '';
				$posts[$pid] = $post;
			}
		}

		if(!empty($tids)) {
			$threads = C::t('forum_thread')->fetch_all_by_tid_displayorder(array_keys($tids), $displayorder, $dglue, array(), $closed);

		}
		
        $relist = '';
    }
} 

include template('singcere_wechat:dkf');