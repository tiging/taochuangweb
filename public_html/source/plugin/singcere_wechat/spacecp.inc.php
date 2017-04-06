<?php
/**
 *      (C)2007-2012 singcere.net
 *      This is a freeware, But you have no right to modify or distribute
 *
 *      $Id$
 */

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}


if($_GET['action'] == 'checkusername') {
    checkusername($_GET['newusername'], 1);
    exit;
}


loaducenter();

$allowusername = UC_CONNECT == 'mysql' || (UC_DBHOST == $_G['config']['db']['1']['dbhost'] && UC_DBUSER == $_G['config']['db']['1']['dbuser']);

$bindmember = C::t('#singcere_wechat#singcere_wechat_bind')->fetch_by_uid($_G['uid']);


if(submitcheck('resetpwsubmit') && $bindmember['isregister']) {
    if($allowusername) {
        $newusername = checkusername($_GET['newusername']);
    }
    
    if(strlen($_GET['newpassword1']) < 6) {
        showmessage(lang('message', 'profile_password_tooshort', array('pwlength' => 6)));
    }

    if($_G['setting']['strongpw']) {
        $strongpw_str = array();
        if(in_array(1, $_G['setting']['strongpw']) && !preg_match("/\d+/", $_GET['newpassword1'])) {
            $strongpw_str[] = lang('member/template', 'strongpw_1');
        }
        if(in_array(2, $_G['setting']['strongpw']) && !preg_match("/[a-z]+/", $_GET['newpassword1'])) {
            $strongpw_str[] = lang('member/template', 'strongpw_2');
        }
        if(in_array(3, $_G['setting']['strongpw']) && !preg_match("/[A-Z]+/", $_GET['newpassword1'])) {
            $strongpw_str[] = lang('member/template', 'strongpw_3');
        }
        if(in_array(4, $_G['setting']['strongpw']) && !preg_match("/[^a-zA-z0-9]+/", $_GET['newpassword1'])) {
            $strongpw_str[] = lang('member/template', 'strongpw_4');
        }
        if($strongpw_str) {
            showmessage(lang('member/template', 'password_weak').implode(',', $strongpw_str));
        }
    } 
    if($_GET['newpassword1'] !== $_GET['newpassword2']) {
        showmessage('profile_passwd_notmatch');
    }
    if(!$_GET['newpassword1'] || $_GET['newpassword1'] != addslashes($_GET['newpassword1'])) {
        showmessage('profile_passwd_illegal');
    }
    
    $updatearr = array('isregister' => 0);
    // 修改密码
    uc_user_edit(addslashes($_G['member']['username']), null, $_GET['newpassword1'], null, 1);
    C::t('common_member')->update($_G['uid'], array('password' => md5(random(10))));
    // 修改用户名 via UC_DB
    if($allowusername && $newusername != $_G['member']['username']) {
        global $uc_controls;
        $updatearr['username'] = $newusername;
        DB::query('UPDATE %t SET username = %s WHERE username = %s', array('common_member', $newusername, $_G['member']['username']));
        $uc_controls['user']->db->query("UPDATE " . UC_DBTABLEPRE . "members SET username='$newusername' WHERE username='{$_G[member][username]}'");
        C::t('common_member')->update_cache($_G['uid'], array('username' => $newusername));
    }
    
    C::t('#singcere_wechat#singcere_wechat_bind')->update($bindmember['id'], $updatearr);
    showmessage('singcere_wechat:spacecp_reset_success', dreferer());
}



function checkusername($username, $return = 0) {
    global $_G;
    $username = trim($username);
    $usernamelen = dstrlen($username);
    if($usernamelen < 3) {
        showmessage('profile_username_tooshort', '', array(), array('handle' => false));
    } elseif($usernamelen > 15) {
        showmessage('profile_username_toolong', '', array(), array('handle' => false));
    }
    
    if($username == $_G['username']) {
        $return && showmessage('succeed', '', array(), array('handle' => false));
        return $username;
    }
    
    $ucresult = uc_user_checkname($username);
    
    if($ucresult == -1) {
        showmessage('profile_username_illegal', '', array(), array('handle' => false));
    } elseif($ucresult == -2) {
        showmessage('profile_username_protect', '', array(), array('handle' => false));
    } elseif($ucresult == -3) {
        if(C::t('common_member')->fetch_by_username($username) || C::t('common_member_archive')->fetch_by_username($username)) {
            showmessage('singcere_wechat:spacecp_username_exist', '', array(), array('handle' => false));
        } else {
            showmessage('register_activation', '', array(), array('handle' => false));
        }
    }
    
    $censorexp = '/^('.str_replace(array('\\*', "\r\n", ' '), array('.*', '|', ''), preg_quote(($_G['setting']['censoruser'] = trim($_G['setting']['censoruser'])), '/')).')$/i';
    if($_G['setting']['censoruser'] && @preg_match($censorexp, $username)) {
        showmessage('profile_username_protect', '', array(), array('handle' => false));
    }
    
    $return && showmessage('succeed', '', array(), array('handle' => false));
        return $username;
}
