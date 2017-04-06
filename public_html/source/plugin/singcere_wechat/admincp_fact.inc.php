<?php

/**
 *      (C)2007-2012 singcere.net
 *      This is a freeware, But you have no right to modify or distribute
 *
 *      $Id$
 */


if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}

$langs = &$scriptlang['singcere_wechat'];
$setting = (array)unserialize($_G['setting']['singcere_wechat']);


if($_GET['op'] == 'getclass') {
    $fid = intval($_GET['fid']);
    $class = C::t('forum_threadclass')->fetch_all_by_fid($fid);
    
    include template('common/header');
    $optionhtml = '<option>'.$langs['admincp_fact_threadclass'].'</option>';
    foreach($class as $cls) {
    	$optionhtml .= "<option value='$cls[typeid]'>$cls[name]</option>";
    }
    echo $optionhtml;
    include template('common/footer');
    exit;
} else if($_GET['op'] == 'detail') {
    $fact = C::t('#singcere_wechat#singcere_wechat_fact')->fetch($_GET['factid']);
    if(empty($fact) || $fact['status'] == 0) {
        cpmsg($langs['admincp_fact_nofound'], '', 'error');
    }
    if(submitcheck('detailsubmit')) {
        C::t('#singcere_wechat#singcere_wechat_fact')->update($fact['factid'], array(
            'subject' => dhtmlspecialchars($_GET['subject']),
            'message' => dhtmlspecialchars($_GET['message']),
            'anonymous' => $_GET['anonymous'] ? 1 : 0,
            'mobile' => dhtmlspecialchars($_GET['mobile']),
        ));
        
        cpmsg('setting_update_succeed', 'action=plugins&operation=config&do='.$pluginid.'&identifier='.$plugin['identifier'].'&pmod='.$module['name'].'&op=detail&factid='.$fact['factid'], 'succeed');
    } else {
        if($fact['tid']) {
            $thread = C::t('forum_thread')->fetch($fact['tid']);
        }
        
        showformheader('plugins&operation=config&do='.$pluginid.'&identifier='.$plugin[identifier].'&pmod='.$module['name'].'&op=detail');
	    showtableheader();
         
        showtitle(lang('plugin/singcere_wechat', 'admincp_fact_detail_title', array('uid' => $fact['uid'], 'username' => $fact['username'], 'dateline' => dgmdate($fact['dateline'], 'Y-m-d H:i'))));
        
        showsetting($langs['admincp_fact_title'], 'subject', $fact['subject'], 'text');
        showsetting($langs['admincp_fact_message'], 'message', $fact['message'], 'textarea');
        showsetting($langs['admincp_fact_anonymous'], 'anonymous', $fact['anonymous'], 'radio');
        showsetting($langs['admincp_fact_mobile'], 'mobile', $fact['mobile'], 'text');
       
        $fact['attachment'] = unserialize($fact['attachment']);
        echo '<tr ><td colspan="2" class="td27"><span class="">'.$langs['admincp_fact_remoteimg_tips'].':</span></td></tr>';
        if($fact['attachment']) {
            
            echo '<tr class="noborder""><td style="width:80%">';
            foreach($fact['attachment'] as $attach) {
                echo '<p style="margin: 0 12px 12px 0; float:left;"><img width="110" style="cursor:pointer" onclick="zoom(this, \'plugin.php?id=singcere_wechat:misc&ac=img2wx&url='.$attach.'\', 1)" src="plugin.php?id=singcere_wechat:misc&ac=img2wx&url='.$attach.'"></p>'; 
            }
            echo '</td><td class="vtop tips2" s="1"><br><span class="smalltxt" style="color:#F00"></span></td></tr>';
        } else {
            echo '<tr class="noborder""><td style="width:80%">'.cplang('none').'</td></tr>';
        }
        showsubmit('detailsubmit', 'modify', '<input type="hidden" name="factid" value="'.$fact['factid'].'">');
        
        showtablefooter();
        showformfooter();
    }
   
} else if($_GET['op'] == 'audit') {
    $fact = C::t('#singcere_wechat#singcere_wechat_fact')->fetch($_GET['factid']);
    if(empty($fact) || $fact['status'] != 1) {
        cpmsg('undefined_action', '', 'error');
    }
    
    if(empty($setting['fact_postfid'])) {
        cpmsg($langs['admincp_fact_postfid_error'], '', 'error');
    }
    
    if(empty($fact['subject'])) {
        cpmsg($langs['admincp_fact_subject_empty'], '', 'error');
    }

    loadcache(array('groupreadaccess', 'stamps'));
    if(submitcheck('auditsubmit', true)) {
        $message = dhtmlspecialchars(trim($_GET['return']));
        if(dstrlen($message) > 255) {
            cpmsg($langs['admincp_fact_return_too_long'], '', 'error');
        }
        
        if($_GET['status'] == 0) {
            if(empty($message)) {
                cpmsg($langs['admincp_fact_return_empty'], '', 'error');
            }
            C::t('#singcere_wechat#singcere_wechat_fact')->update($fact['factid'], array('return' => $message, 'status' => -1));
        } else {
            if($_GET['processing']) {
                $nextlink = "action=plugins&operation=config&do=$pluginid&identifier=$plugin[identifier]&pmod=$module[name]&op=audit&factid=$fact[factid]&status=1&readaccess=$_GET[readaccess]&stamp=$_GET[stamp]&reward=$_GET[reward]&return=$_GET[return]&auditsubmit=true&formhash=".FORMHASH;
                cpmsg($langs['admincp_fact_create_loading'], $nextlink, 'loading');
            } else {
                // 发布帖子
                include_once libfile('function/forum');
                include_once libfile('function/post');
                include_once libfile('function/stat');

                $anonymous_arr = explode(',', $setting['fact_anonymous']);
                $anonymous_uid = $anonymous_arr[rand(0, count($anonymous_arr)-1)];
                
                if($fact['anonymous']) {
                    $uid =  intval($_GET['uid']);
                    if($uid) {
                        $member = getuserbyuid($uid, 1);
                        if(empty($member)) {
                            cpmsg($langs['admincp_fact_user_nofound']."(uid:$uid)", '', 'error');
                        }
                    }
                } else {
                    $uid = $fact['uid'];
                    $member = getuserbyuid($uid, 1);
                }
                
                
                $newthread = array(
                    'fid' => $setting['fact_postfid'],
                    'typeid' => $setting['fact_typeid'],
                    'author' => $member['username'],
                    'authorid' => $uid,
                    'subject' => $fact['subject'],
                    //'attachment' => $attachment,
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
                    'fid' => $setting['fact_postfid'],
                    'tid' => $tid,
                    'first' => 1,
                    'author' => $member['username'],
                    'authorid' => $uid,
                    'subject' => $fact['subject'],
                    'dateline' => TIMESTAMP,
                    'message' => $fact['message'],
                    //'attachment' => $attachment,
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
                C::t('forum_forum')->update($setting['fact_postfid'], array('lastpost' => $lastpost));
                C::t('forum_forum')->update_forum_counter($setting['fact_postfid'], 1, 1, 1);
                if ($_G['cache']['forums'][$setting['fact_postfid']]['type'] == 'sub') {
                    C::t('forum_forum')->update($_G['cache']['forums'][$setting['fact_postfid']]['fup'], array('lastpost' => $lastpost));
                }
                
                // 处理微信远程附件
                $subdir = date('Ym').'/'.date('d').'/';
                $basedir = 'forum/'.$subdir;
                !is_dir($_G['setting']['attachdir'].$basedir) && dmkdir($_G['setting']['attachdir'].$basedir);
                
                $attachubb = '';
                $fact['attachment'] = unserialize($fact['attachment']);
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
                C::t('forum_thread')->update($tid, array('attachment' => $attachment));
                C::t('forum_post')->update(0, $pid, array('message' => $fact['message'].$attachubb, 'attachment' => $attachment));
                C::t('#singcere_wechat#singcere_wechat_fact')->update($fact['factid'], array('return' => $message, 'status' => 2));
            }
        }
        
        if($fact['uid']) { 
            notification_add($fact['uid'], 'system', lang('plugin/singcere_wechat', 'fact_notice_success', array('tid' => $tid)), array(), 1);
            $reward = max(0, intval($_GET['reward']));
            $reward && updatemembercount($fact['uid'], array($setting['fact_credit'] => $reward));
        }

        include_once libfile('function/common', 'plugin/singcere_wechat');
        sc_wechat_notification('fact', array(
            'to' => 'openid:'.$fact['openid'],
            'url' => ($_GET['status'] ? $_G['siteurl'].'forum.php?mod=viewthread&tid='.$tid.'&authorization=1' : $_G['siteurl'].'home.php?mod=space&do=pm&authorization=1'),
            'color' => '#55990b',
            'data' => array(
                'first' => array('value' => ($_GET['status'] ? $langs['fact_wxnotice_title_success'] : $langs['fact_wxnotice_title_failed']), 'color' => '#4998e7'),
                'keyword1' => array('value' => $fact['subject'], 'color' => '#000000'),
                'keyword2' => array('value' => $langs['fact_wxnotice_k2'], 'color' => '#000000'),
            ))
        );
         
        cpmsg('setting_update_succeed', 'action=plugins&operation=config&do='.$pluginid.'&identifier='.$plugin[identifier].'&pmod='.$module['name'], 'succeed');
        
    } else {

        showtips($langs['admincp_fact_tips']);
        
        showformheader('plugins&operation=config&do='.$pluginid.'&identifier='.$plugin[identifier].'&pmod='.$module['name'].'&op=audit&processing=true');
        showtableheader();
        showtitle('<span class="highlight">'.$langs['admincp_fact_audit'].': '.$fact['subject'].'</a>');
        
        $groupoptions = '<option value="0">'.cplang('none').'</option>';
        foreach($_G['cache']['groupreadaccess'] as $group) {
            $groupoptions .= '<option value="'.$group['readaccess'].'" >'.$group['grouptitle'].'</option>';
        }
        
        $stampoptions = '<option value="0">'.cplang('none').'</option>';
        foreach($_G['cache']['stamps'] as $stampid => $stamp) {
            if($stamp['type'] == 'stamp') {
                $stampoptions .= '<option value="'.$stampid.'" >'.$stamp['text'].'</option>';
            }
        }
        
        
        
        showsetting($langs['admincp_fact_audit_success'], 'status', 0, 'radio', 0, 1);
        showsetting($langs['fact_readaccess'], '', '', '<select name="readaccess">'.$groupoptions.'</select>');
        showsetting($langs['fact_stamp'], '', '', '<select name="stamp">'.$stampoptions.'</select>');
        if($fact['anonymous']) {
            $postuser = C::t('common_member')->fetch_all(explode(',', $setting['fact_anonymous']));
            $postuseroptions = '<option value="0">'.$langs['guest'].'</option>';
            foreach($postuser as $user) {
                $postuseroptions .= '<option value="'.$user['uid'].'" >'.$user['username'].'</option>';
            }
            $postuseroptions .= "<option value='$fact[uid]'>$fact[username] ($langs[fact_user])</option>";
            showsetting($langs['admincp_fact_choice_author'], '', '', '<select name="uid">'.$postuseroptions.'</select>');
        }
        showsetting($langs['fact_reward'].' '.($setting['fact_credit'] ?  '('.$_G['setting']['extcredits'][$setting['fact_credit']]['title'].')' : ''), 'reward', 0, 'text');
        showtagfooter('tbody');
        
        showsetting($langs['fact_return'], 'return', '', 'textarea');
        showsubmit('auditsubmit', 'submit', '<input type="hidden" name="factid" value="'.$fact['factid'].'">');
        
        showtablefooter();
        showformfooter();
    }
    
} else if($_GET['op'] == 'delete' && $_GET['formhash'] == FORMHASH) {
    C::t('#singcere_wechat#singcere_wechat_fact')->delete($_GET['factid']);
    cpmsg('setting_update_succeed', 'action=plugins&operation=config&do='.$pluginid.'&identifier='.$plugin[identifier].'&pmod='.$module['name'], 'succeed');
} else {
    
	admincp_showsubmenu(null, array(
		array($langs['admincp_fact_setting'], "plugins&operation=config&do=$pluginid&identifier=$plugin[identifier]&pmod=$module[name]&op=setting", $_GET['op'] == 'setting'),
		array($langs['admincp_fact_list'], "plugins&operation=config&do=$pluginid&identifier=$plugin[identifier]&pmod=$module[name]&op=list", $_GET['op'] != 'setting'),
		
	));
    
	if(submitcheck('settingsubmit')) {
		if(!isset($_G['cache']['forums'])) {
			loadcache('forums');
		}
	
		$fid = intval($_GET['setting']['fact_postfid']);
		if(!isset($_G['cache']['forums'][$fid])) {
			cpmsg($langs['admincp_fact_postfid_error'], '', 'error');
		}
	
		if($_GET['setting']['typeid']) {
			$class = C::t('forum_threadclass')->fetch_all_by_fid($_GET['setting']['fact_postfid']);
			if(in_array($_GET['setting']['fact_typeid'], array_keys($class))) {
				cpmsg($langs['admincp_fact_post_class'], '', 'error');
			}
		}
	
		$settings = array('singcere_wechat' => serialize($_GET['setting'] + $setting));
		C::t('common_setting')->update_batch($settings);
		updatecache('setting');
	
		cpmsg('setting_update_succeed', 'action=plugins&operation=config&do='.$pluginid.'&identifier='.$plugin[identifier].'&pmod='.$module['name'].'&op=setting', 'succeed');
	} else if(submitcheck('deletesubmit')) {
		C::t('#singcere_wechat#singcere_wechat_fact')->delete($_GET['delete']);
		cpmsg('setting_update_succeed', 'action=plugins&operation=config&do='.$pluginid.'&identifier='.$plugin[identifier].'&pmod='.$module['name'].'&op=setting', 'succeed');
	}

    $page = max(1, intval($_GET['page']));
    $perpage = 20;
    
    if($_GET['op'] == 'setting') {
        $forums = '<select id="postfid" name="setting[fact_postfid]" onchange="showclass(this.options[this.options.selectedIndex].value);" style="width:150px;"><option value="0">'.cplang('plugins_empty').'</option>'.showforumselect(FALSE, 0, $setting['fact_postfid'], TRUE).'</select>';
        $typeoptions = '';
        if($setting['fact_postfid']) {
            $class = C::t('forum_threadclass')->fetch_all_by_fid($setting['fact_postfid']);
            $typeoptions = '<option>'.$langs['admincp_fact_threadclass'].'</option>';
            foreach($class as $cls) {
                $cls['selected'] = $cls['typeid'] == $setting['fact_typeid'] ? 'selected="selected"' : '';
                $typeoptions .= "<option value='$cls[typeid]' $cls[selected]>$cls[name]</option>";
            }
        }
        $types = '<select id="forumclass" name="setting[fact_typeid]" style="width:100px">'.$typeoptions.'</select>';
        ?>
        
        <script type="text/JavaScript">
        	function showclass(fid) {
        		var x = new Ajax();
        		x.get('<?php echo ADMINSCRIPT;?>?action=plugins&operation=config&do=<?php echo $pluginid;?>&identifier=<?php echo $plugin[identifier];?>&pmod=<?php echo $module['name'];?>&op=getclass&inajax=1&fid=' + fid, function(s, x) {
        			$('forumclass').innerHTML = s;
        		});
        	}
        </script>
        <?php 
        
        $apicredits = '<option value="0">'.cplang('none').'</option>';
        foreach($_G['setting']['extcredits'] as $i => $credit) {
            $extcredit = 'extcredits'.$i.' ('.$credit['title'].')';
            $apicredits .= '<option value="'.$i.'" '.($i == intval($setting['fact_credit']) ? 'selected' : '').'>'.$extcredit.'</option>';
        }
        
        showformheader('plugins&operation=config&do='.$pluginid.'&identifier='.$plugin[identifier].'&pmod='.$module['name'].'&op=setting');
        showtableheader();
        showsetting($langs['admincp_fact_keyword'], 'setting[fact_keyword]', $setting['fact_keyword'], 'text', 0, 0, $langs['admincp_fact_keyword_tips']);
        showsetting($langs['admincp_fact_fid'], '', '', $forums.$types, 0, 0, $langs['admincp_fact_fid_tips']);
        showsetting($langs['admincp_fact_anonymous_choice'], 'setting[fact_anonymous]', $setting['fact_anonymous'], 'text', 0, 0, $langs['admincp_fact_anonymous_choice_tips']);
        showsetting($langs['admincp_fact_moderators'], 'setting[fact_moderators]', $setting['fact_moderators'], 'text', 0, 0, $langs['admincp_fact_moderators_tips']);
        showsetting($langs['admincp_fact_credit'], '', '', '<select name="setting[fact_credit]">'.$apicredits.'</select>', 0, 0, $langs['admincp_fact_credit_tips']);
        
        
        showsetting($langs['admincp_fact_lang'], 'setting[fact_lang]', $setting['fact_lang'], 'textarea', 0, 0, $langs['admincp_fact_lang_tips']);
        showsubmit('settingsubmit');
        showtablefooter();
        showformfooter();
    } else {
    	echo '<style>td img {width:25px; height:25px;vertical-align: middle;padding-right:10px;}</style>';
    	$counts = DB::result_first("SELECT count(*) FROM %t WHERE status <> 0", array('singcere_wechat_fact'));
    	
    	showformheader('plugins&operation=config&do='.$pluginid.'&identifier='.$plugin[identifier].'&pmod='.$module['name']);
    	showtableheader($langs['admincp_fact_list'].'<a href="plugin.php?id=singcere_wechat:fact&action=admincp" class="cside" target="_blank">'.$langs['admincp_fact_go_panel'].'</a>');
    	showsubtitle(array('del', 'OPENID', 'username', 'subject', $langs['admincp_fact_anonymous'], $langs['admincp_fact_mobile'], $langs['admincp_fact_dateline'], 'operation'), 'header', array('class="td25"', 'width="200"', 'class="td21"', '', 'class="td25"', 'class="td24"', 'class="td24"'));
    	if($counts) {
    	
    		$factlist = DB::fetch_all("SELECT * FROM %t WHERE status <> 0 ORDER BY dateline DESC ".DB::limit(($page-1)*$perpage, $perpage), array('singcere_wechat_fact'), 'factid');
    		foreach($factlist as $factid => $fact) {
    			showtablerow('', array(), array(
    			'<input class="checkbox" type="checkbox" name="delete[]" value="'.$factid.'">',
    			$fact['openid'],
    			$fact['uid'] ? avatar($fact[uid],small).'<a href="home.php?mod=space&uid='.$fact[uid].'" target="_blank">'.$fact[username].'</a>' : '',
    			$fact['subject'],
    			$fact['anonymous'] ? cplang('yes') : cplang('no'),
    			$fact['mobile'],
    			dgmdate($fact['dateline'], 'Y-m-d H:i'),
    			'<a href="' . ADMINSCRIPT . "?action=plugins&operation=config&do=$plugin[pluginid]&identifier=$plugin[identifier]&pmod=$module[name]&op=detail&factid=$factid" . '" class="act">'.cplang('edit').'</a>'.
    			'<a href="' . ADMINSCRIPT . "?action=plugins&operation=config&do=$plugin[pluginid]&identifier=$plugin[identifier]&pmod=$module[name]&factid=$fact[factid]&op=delete&formhash=".FORMHASH."" . '" class="act">'.cplang('delete').'</a>'.
    			($fact['status'] != 1 ? ($fact['status'] == 2 ? '<em>'.$langs['fact_status_2'].'</em>' : '<em>'.$langs['fact_status_-1'].'</em>') : '<a href="' . ADMINSCRIPT . "?action=plugins&operation=config&do=$plugin[pluginid]&identifier=$plugin[identifier]&pmod=$module[name]&op=audit&factid=$factid" . '" class="highlight act">'.$langs['fact_audit'].'</a>')
    			 
    			));
    		}
    	
    		$multipage = multi($counts, $perpage, $page, ADMINSCRIPT."?action=plugins&operation=config&do=$plugin[pluginid]&identifier=$plugin[identifier]&pmod=$module[name]$url");
    	
    	}
    	
    	showsubmit('deletesubmit', 'delete', 'select_all', '', $multipage);
    	showtablefooter();
    	showformfooter();
    }
    
    

}

function showforumselect($groupselectable = FALSE, $arrayformat = 0, $selectedfid = 0, $showhide = FALSE, $evalue = FALSE, $special = 0) {
    global $_G;

    if(!isset($_G['cache']['forums'])) {
        loadcache('forums');
    }
    $forumcache = &$_G['cache']['forums'];
    $forumlist = $arrayformat ? array() : '<optgroup label="&nbsp;">';
    foreach($forumcache as $forum) {
        if(!$forum['status'] && !$showhide) {
            continue;
        }
        if($selectedfid) {
            if(!is_array($selectedfid)) {
                $selected = $selectedfid == $forum['fid'] ? ' selected' : '';
            } else {
                $selected = in_array($forum['fid'], $selectedfid) ? ' selected' : '';
            }
        }
        if($forum['type'] == 'group') {
            if($arrayformat) {
                $forumlist[$forum['fid']]['name'] = $forum['name'];
            } else {
                $forumlist .= $groupselectable ? '<option value="'.($evalue ? 'gid_' : '').$forum['fid'].'" class="bold">--'.$forum['name'].'</option>' : '</optgroup><optgroup label="--'.$forum['name'].'">';
            }
            $visible[$forum['fid']] = true;
        } elseif($forum['type'] == 'forum' && isset($visible[$forum['fup']]) && (!$forum['viewperm'] || ($forum['viewperm'] && forumperm($forum['viewperm'])) || strstr($forum['users'], "\t$_G[uid]\t")) && (!$special || (substr($forum['allowpostspecial'], -$special, 1)))) {
            if($arrayformat) {
                $forumlist[$forum['fup']]['sub'][$forum['fid']] = $forum['name'];
            } else {
                $forumlist .= '<option value="'.($evalue ? 'fid_' : '').$forum['fid'].'"'.$selected.'>'.$forum['name'].'</option>';
            }
            $visible[$forum['fid']] = true;
        } elseif($forum['type'] == 'sub' && isset($visible[$forum['fup']]) && (!$forum['viewperm'] || ($forum['viewperm'] && forumperm($forum['viewperm'])) || strstr($forum['users'], "\t$_G[uid]\t")) && (!$special || substr($forum['allowpostspecial'], -$special, 1))) {
            if($arrayformat) {
                $forumlist[$forumcache[$forum['fup']]['fup']]['child'][$forum['fup']][$forum['fid']] = $forum['name'];
            } else {
                $forumlist .= '<option value="'.($evalue ? 'fid_' : '').$forum['fid'].'"'.$selected.'>&nbsp; &nbsp; &nbsp; '.$forum['name'].'</option>';
            }
        }
    }
    if(!$arrayformat) {
        $forumlist .= '</optgroup>';
        $forumlist = str_replace('<optgroup label="&nbsp;"></optgroup>', '', $forumlist);
    }
    return $forumlist;
}

function admincp_showsubmenu($title, $menus = array(), $right = '', $replace = array()) {
	
	$s = '<div style="margin-top:5px;">' . $right . '<ul class="tab1">';
	foreach ($menus as $k => $menu) {
		if (is_array($menu[0])) {
			$s .= '<li id="addjs' . $k . '" class="' . ($menu[1] ? 'current' : 'hasdropmenu') . '" onmouseover="dropmenu(this);"><a href="#"><span>' . cplang($menu[0]['menu']) . '<em>&nbsp;&nbsp;</em></span></a><div id="addjs' . $k . 'child" class="dropmenu" style="display:none;">';
			if (is_array($menu[0]['submenu'])) {
				foreach ($menu[0]['submenu'] as $submenu) {
					$s .= $submenu[1] ? '<a href="' . ADMINSCRIPT . '?action=' . $submenu[1] . '" class="' . ($submenu[2] ? 'current' : '') . '" onclick="' . $submenu[3] . '">' . cplang($submenu[0]) . '</a>' : '<a><b>' . cplang($submenu[0]) . '</b></a>';
				}
			}
			$s .= '</div></li>';
		} else {
			$s .= '<li' . ($menu[2] ? ' class="current"' : '') . '><a href="' . (!$menu[4] ? ADMINSCRIPT . '?action=' . $menu[1] : $menu[1]) . '"' . (!empty($menu[3]) ? ' target="_blank"' : '') . '><span>' . cplang($menu[0]) . '</span></a></li>';
		}
	}
	$s .= '</ul></div>';
	
	echo!empty($menus) ? '<div class="itemtitle">' . $s . '</div>' : $s;
}
