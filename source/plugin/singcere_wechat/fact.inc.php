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
loadcache(array('groupreadaccess', 'stamps'));

if($_GET['action'] == 'admincp') {
    if(!$_G['uid']) {
        showmessage('to_login', '', array(), array('login' => 1));
    }
    if(!in_array($_G['uid'], explode(',', $_G['singcere_wechat']['setting']['fact_moderators']))) {
        showmessage('quickclear_noperm');
    }
    
    if($_GET['op'] == 'edit') {
        $fact = C::t('#singcere_wechat#singcere_wechat_fact')->fetch($_GET['factid']);
        $fact['attachment'] = unserialize($fact['attachment']);
        if(empty($fact) || !$fact['status']) {
            showmessage('undefined_action', dreferer(), array(), array('alert'=> 'right', 'closetime' => true, 'locationtime' => true, 'showdialog' => 1));
        }
        
        if(submitcheck('editsubmit')) {
            C::t('#singcere_wechat#singcere_wechat_fact')->update($fact['factid'], array(
                'subject' => dhtmlspecialchars($_GET['subject']),
                'message' => dhtmlspecialchars($_GET['message']),
                'anonymous' => $_GET['anonymous'] ? 1 : 0,
                'mobile' => dhtmlspecialchars($_GET['mobile']),
            ));
            
            showmessage('singcere_wechat:fact_edit_success', dreferer());
        }
        
        
    } else if($_GET['op'] == 'audit') {
        if(empty($_G['singcere_wechat']['setting']['fact_postfid'])) {
            showmessage('singcere_wechat:admincp_fact_postfid_error');
        }
        $fact = C::t('#singcere_wechat#singcere_wechat_fact')->fetch($_GET['factid']);
        if(empty($fact) || $fact['status'] != 1) {
            showmessage('undefined_action');
        }
        if(empty($fact['subject'])) {
            showmessage('singcere_wechat:admincp_fact_subject_empty');
        }
        loadcache(array('groupreadaccess', 'stamps'));
        
        $postuser = C::t('common_member')->fetch_all(explode(',', $_G['singcere_wechat']['setting']['fact_anonymous']));
        if(submitcheck('auditsubmit')) {
            $message = dhtmlspecialchars(trim($_GET['return']));
            if(dstrlen($message) > 255) {
                showmessage('singcere_wechat:admincp_fact_return_too_long');
            }
            
            if($_GET['status'] == 0) {
                if(empty($message)) {
                    showmessage('singcere_wechat:admincp_fact_return_empty');
                }
                C::t('#singcere_wechat#singcere_wechat_fact')->update($fact['factid'], array('return' => $message, 'status' => -1));
            } else {
                
                // 发布帖子
                include_once libfile('function/forum');
                include_once libfile('function/post');
                include_once libfile('function/stat');
                $fact['attachment'] = unserialize($fact['attachment']);
                
            
                $anonymous_arr = explode(',', $_G['singcere_wechat']['setting']['fact_anonymous']);
                $anonymous_uid = $anonymous_arr[rand(0, count($anonymous_arr)-1)];
            
                if($fact['anonymous']) {
                    $uid =  intval($_GET['uid']);
                    if($uid) {
                        $member = getuserbyuid($uid, 1);
                        if(empty($member)) {
                            showmessage(lang('plugin/singcere_wechat', 'admincp_fact_user_nofound')."(uid:$uid)");
                        }
                    }
                } else {
                    $uid = $fact['uid'];
                    $member = getuserbyuid($uid, 1);
                }
            
            
                $newthread = array(
                    'fid' => $_G['singcere_wechat']['setting']['fact_postfid'],
                    'typeid' => $_G['singcere_wechat']['setting']['fact_typeid'],
                    'author' => $member['username'],
                    'authorid' => $uid,
                    'subject' => $fact['subject'],
                    'attachment' => 0,
                    'readperm' => $_GET['readaccess'],
                    'stamp' => $_GET['stamp'],
                    'dateline' => TIMESTAMP,
                    'lastpost' => TIMESTAMP,
                    'lastposter' => $member['username'],
                    'status' => 192,
                    'closed' => 0
                );
                if($_G['cache']['stamps'][$_GET['stamp']]['icon']) {
                    $newthread['icon'] = $_G['cache']['stamps'][$_GET['stamp']]['icon'];
                }
                
                $tid = C::t('forum_thread')->insert($newthread, true);
                $pid = insertpost(array(
                    'fid' => $_G['singcere_wechat']['setting']['fact_postfid'],
                    'tid' => $tid,
                    'first' => 1,
                    'author' => $member['username'],
                    'authorid' => $uid,
                    'subject' => $fact['subject'],
                    'dateline' => TIMESTAMP,
                    'message' => $fact['message'],
                    'attachment' => 0,
                    'usesig' => $_G['group']['maxsigsize'] ? 1 : 0,
                    'useip' => $fact['clientip'],
                    'status' => 8
                ));
            
                if(!isset($_G['cache']['forums'])) {
                    loadcache('forums');
                }
                useractionlog($uid, 'tid');
                C::t('common_member_field_home')->update($uid, array('recentnote' => $fact['subject']));
                $lastpost = "$tid\t" . $fact['subject'] . "\t".TIMESTAMP."\t$member[username]";
                C::t('forum_forum')->update($_G['singcere_wechat']['setting']['fact_postfid'], array('lastpost' => $lastpost));
                C::t('forum_forum')->update_forum_counter($_G['singcere_wechat']['setting']['fact_postfid'], 1, 1, 1);
                if ($_G['cache']['forums'][$_G['singcere_wechat']['setting']['fact_postfid']]['type'] == 'sub') {
                    C::t('forum_forum')->update($_G['cache']['forums'][$_G['singcere_wechat']['setting']['fact_postfid']]['fup'], array('lastpost' => $lastpost));
                }
            
                // 处理微信远程附件
                $subdir = date('Ym').'/'.date('d').'/';
                $basedir = 'forum/'.$subdir;
                !is_dir($_G['setting']['attachdir'].$basedir) && dmkdir($_G['setting']['attachdir'].$basedir);
            
                $attachubb = '';
                $attachment = 0;
                foreach($fact['attachment'] as $mediaid => $url) {
                    $content = dfsockopen($url);
                    if(!$content) {
                        continue;
                    }
            
                    $filename = date('His').strtolower(random(16)).'.jpg';
                    $rtn = file_put_contents($_G['setting']['attachdir'].$basedir.$filename, $content);
                    if(!$rtn) {
                        continue;
                    }
            
                    $aid = C::t('forum_attachment')->insert(array('tid' => $tid, 'pid' => $pid, 'uid' => $uid, 'tableid' => getattachtableid($tid)), true);
                    $filesize = filesize($_G['setting']['attachdir'].$basedir.$filename);
                    $thumb = $width = 0;
                    if($_G['setting']['thumbsource'] && $_G['setting']['sourcewidth'] && $_G['setting']['sourceheight']) {
                        $image = new image();
                        $thumb = $image->Thumb($_G['setting']['attachdir'].$basedir.$filename, '', $_G['setting']['sourcewidth'], $_G['setting']['sourceheight'], 1, 1) ? 1 : 0;
                        $width = $image->imginfo['width'];
                        $filesize = $image->imginfo['size'];
                    }
                    if($_G['setting']['thumbstatus']) {
                        $image = new image();
                        $thumb = $image->Thumb($_G['setting']['attachdir'].$basedir.$filename, '', $_G['setting']['thumbwidth'], $_G['setting']['thumbheight'], $_G['setting']['thumbstatus'], 0) ? 1 : 0;
                        $width = $image->imginfo['width'];
                    }
                    if($_G['setting']['thumbsource'] || !$_G['setting']['thumbstatus']) {
                        list($width) = @getimagesize($_G['setting']['attachdir'].$basedir.$filename);
                    }
                    if($_G['setting']['watermarkstatus'] && empty($_G['forum']['disablewatermark'])) {
                        $image = new image();
                        $image->Watermark($_G['setting']['attachdir'].$basedir.$filename, '', 'forum');
                        $filesize = $image->imginfo['size'];
                    }
            
                    $insert = array(
                        'aid' => $aid,
                        'tid' => $tid,
                        'pid' => $pid,
                        'dateline' => $_G['timestamp'],
                        'filename' => $filename,
                        'filesize' => $filesize,
                        'attachment' => $subdir.$filename,
                        'isimage' => 1,
                        'uid' => $uid,
                        'thumb' => $thumb,
                        'remote' => 0,
                        'width' => $width,
                    );
            
                    C::t('forum_attachment_n')->insert('tid:'.$tid, $insert, false, true);
                    $attachubb .= "\n[attach]".$aid.'[/attach]';
                    $attachment = 2;
                }
                
                C::t('forum_post')->update(0, $pid, array('message' => $fact['message'].$attachubb, 'attachment' => $attachment));
                C::t('forum_thread')->update($tid, array('attachment' => $attachment));
                C::t('#singcere_wechat#singcere_wechat_fact')->update($fact['factid'], array('return' => $message, 'status' => 2));
            }
            
            if($fact['uid']) {
                notification_add($fact['uid'], 'system', lang('plugin/singcere_wechat', 'fact_notice_success'), array(), 1);
                $reward = max(0, intval($_GET['reward']));
                $reward && updatemembercount($fact['uid'], array($_G['singcere_wechat']['setting']['fact_credit'] => $reward));
            }
            
            include_once libfile('function/common', 'plugin/singcere_wechat');
            sc_wechat_notification('fact', array(
                'to' => 'openid:'.$fact['openid'],
                //'url' => $_G['siteurl'].'home.php?mod=space&do=pm&authorization=1',
                'color' => '#55990b',
                'data' => array(
                    'first' => array('value' => ($_GET['status'] ? lang('plugin/singcere_wechat', 'fact_wxnotice_title_success') : lang('plugin/singcere_wechat', 'fact_wxnotice_title_failed').":\n".$message), 'color' => '#4998e7'),
                    'keyword1' => array('value' => $fact['subject'], 'color' => '#000000'),
                    'keyword2' => array('value' => lang('plugin/singcere_wechat', 'fact_wxnotice_k2'), 'color' => '#000000'),
                ))
            );
             
            showmessage('singcere_wechat:fact_audit_finished', dreferer());
        }
    } else if($_GET['op'] == 'delete' && $_GET['formhash'] == FORMHASH) {
        C::t('#singcere_wechat#singcere_wechat_fact')->delete($_GET['factid']);
        showmessage('singcere_wechat:fact_delete_success', dreferer(), array(), array('showdialog' => true, 'locationtime' => 3));
    } else {
        $filtersql = 'status <> 0';
        if(in_array($_GET['status'], array(-1, 1, 2))) {
            $addurl = '&status='.intval($_GET['status']);
            $filtersql = 'status = '.intval($_GET['status']);
        }

        $page = max(1, intval($_GET['page']));
        $perpage = 10;
        
        
        $counts = DB::result_first("SELECT count(*) FROM %t WHERE %i", array('singcere_wechat_fact', $filtersql));
        if($counts) {
            $factlist = DB::fetch_all("SELECT * FROM %t WHERE %i ORDER BY dateline DESC ".DB::limit(($page-1)*$perpage, $perpage), array('singcere_wechat_fact', $filtersql), 'factid');
            $multipage = multi($counts, $perpage, $page, 'plugin.php?id=singcere_wechat:fact&action=admincp'.$addurl);
        }
    }
    
    include template('singcere_wechat:fact_admincp');
} else {
    sc_wechat_auth(true);
    
    list($openid, $unionid, $uid, $time) = getcookie('wxoauth') ? explode("\t", authcode(getcookie('wxoauth'), 'DECODE')) : array();
    
    if(empty($openid)) {
        showmessage('quickclear_noperm');
    }
    
    $factid = intval($_GET['factid']);
    $fact = C::t('#singcere_wechat#singcere_wechat_fact')->fetch($factid);
    if(empty($fact) || $fact['openid'] != $openid) {
        showmessage('quickclear_noperm');
    }
    
    
    
    if($fact['status'] != 0) {
        $op = 'post_success';
    } else if(submitcheck('factsubmit')) {
        C::t('#singcere_wechat#singcere_wechat_fact')->update_unused_by_openid($openid, array(
            'subject' => dhtmlspecialchars($_GET['subject']),
            'message' => dhtmlspecialchars($_GET['message']),
            'uid' => $_G['uid'],
            'username' => $_G['username'],
            'anonymous' => $_GET['anonymous'] ? 1 : 0,
            'mobile' => dhtmlspecialchars($_GET['mobile']),
            'status' => 1,
        ));
    
        $op = 'post_success';
        
        foreach(explode(',', $_G['singcere_wechat']['setting']['fact_moderators']) as $muid) {
            notification_add($muid, 'system', lang('plugin/singcere_wechat', 'fact_audit_notice', array('username' => $_G['username'])), array(), 1);
        }
    } 
   
    
    $fact['attachment'] = unserialize($fact['attachment']);
    include template('singcere_wechat:fact');
}


