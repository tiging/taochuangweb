<?php

/**
 *      (C)2007-2012 singcere.net
 *      This is a freeware, But you have no right to modify or distribute
 *      $Id$
 */
class plugin_singcere_wechat_base
{
    var $allow = false;

    function plugin_singcere_wechat_base()
    {
        global $_G;
        include_once template('singcere_wechat:module');
        if ($_G['setting']['bbclosed']) {
            return;
        }
        $this->allow = true;
    }

    function common_base()
    {
        global $_G;

        define('IN_WECHAT', strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false);

        if (!isset($_G['singcere_wechat'])) {
            $_G['singcere_wechat']['setting'] = unserialize($_G['setting']['singcere_wechat']);

            $_G['singcere_wechat']['abs_attachurl'] = strpos($_G['setting']['attachurl'], 'http://') === false ? $_G['siteurl'] . $_G['setting']['attachurl'] : $_G['setting']['attachurl'];
            $_G['singcere_wechat']['referer'] = !$_G['inajax'] && CURSCRIPT != 'member' ? $_G['basefilename'] . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '') : dreferer();
            $_G['singcere_wechat']['login_url'] = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $_G['singcere_wechat']['setting']['wechat_appId'] . '&redirect_uri=' .
                urlencode($_G['siteurl'] . 'plugin.php?id=singcere_wechat&op=OAuth&referer=' . urlencode($_G['singcere_wechat']['referer'])) . '&response_type=code&scope=snsapi_base&state=' . md5(FORMHASH) . '#wechat_redirect';
            $_G['singcere_wechat']['setting']['wechat_shareicon'] = $_G['singcere_wechat']['setting']['wechat_shareicon'] ? $_G['singcere_wechat']['abs_attachurl'] . $_G['singcere_wechat']['setting']['wechat_shareicon'] : $_G['siteurl'] . 'source/plugin/singcere_wechat/template/static/share-logo.png';

            $_G['singcere_wechat']['setting']['wechat_appsecret'] = authcode($_G['singcere_wechat']['setting']['wechat_appsecret'], 'DECODE', $_G['config']['security']['authkey']);
            $_G['singcere_wechat']['setting']['wechat_mchkey'] = authcode($_G['singcere_wechat']['setting']['wechat_mchkey'], 'DECODE', $_G['config']['security']['authkey']);
        }

        // Openid may be invalid or does not match with APPID
        if ($_G['uid']) {
            if ($bindmember = C::t('#singcere_wechat#singcere_wechat_bind')->fetch_by_uid($_G['uid'])) {
                $_G['member']['openid'] = $bindmember['openid'];
                $_G['member']['unionid'] = $bindmember['unionid'];
                $_G['member']['subscribe'] = $bindmember['subscribe'] == 1 ? 1 : 0;

                CURSCRIPT == 'home' && $_G['member']['isregister'] = $bindmember['isregister'];
                unset($bindmember);
            }
        }

        include_once libfile('function/common', 'plugin/singcere_wechat');
    }

    function misc_activity_message($params)
    {        // 活动相关通知
        if (CURSCRIPT != 'forum') {
            return;
        }
        global $_G, $thread, $activity, $reason;
        $reason = cutstr(dhtmlspecialchars($_GET['reason']), 200);

        $thread = C::t('forum_thread')->fetch($_G['tid']);

        list($message) = $params['param'];
        if ($message == 'activity_completion') {        // 提交申请
            $reason = cutstr(dhtmlspecialchars($_GET['message']), 200);
            $ntcarr = array(
                $thread['authorid'] => $_G['username'] . ' ' . lang('plugin/singcere_wechat', 'wx_notice_act_apply'),
                $_G['uid']          => lang('plugin/singcere_wechat', 'wx_notice_act_wait'),
            );

            foreach ($ntcarr as $uid => $msg) {
                sc_wechat_notification('activity', array(
                    'to'    => 'uid:' . $uid,
                    'url'   => $_G['siteurl'] . 'forum.php?mod=viewthread&tid=' . $thread['tid'],
                    'color' => '#55990b',
                    'data'  => array(
                        'first'    => array('value' => $msg, 'color' => '#4998e7'),
                        'keynote1' => array('value' => $thread['subject'], 'color' => '#000000'),
                        'keynote2' => array('value' => dgmdate($activity['starttimefrom'], 'Y-m-d H:i') . ($activity['starttimeto'] ? ' - ' . dgmdate($activity['starttimeto'], 'Y-m-d H:i') : ''), 'color' => '#000000'),
                        'remark'   => array('value' => ($reason ? $reason : lang('plugin/singcere_wechat', 'wx_notice_act_viewmore')), 'color' => '#a3a3a3')
                    )
                ));
            }
        } else if ($message == 'activity_cancel_success') {        // 报名取消

        } else if ($message == 'activity_auditing_completion') {        // 审核通知, 允许参加,完善资料
            global $unverified;
            if (empty($unverified)) return;
            $tmpl_first = $_GET['operation'] == 'replenish' ? array('value' => lang('plugin/singcere_wechat', 'wx_notice_act_replenish'), 'color' => '#FF0000') : array('value' => lang('plugin/singcere_wechat', 'wx_notice_act_pass'), 'color' => '#55990b');
            foreach ($unverified as $uid) {
                sc_wechat_notification('activity', array(
                    'to'    => 'uid:' . $uid,
                    'url'   => $_G['siteurl'] . 'forum.php?mod=viewthread&tid=' . $thread['tid'],
                    'color' => '#55990b',
                    'data'  => array(
                        'first'    => $tmpl_first,
                        'keynote1' => array('value' => $thread['subject'], 'color' => '#000000'),
                        'keynote2' => array('value' => dgmdate($activity['starttimefrom'], 'Y-m-d H:i') . ($activity['starttimeto'] ? ' - ' . dgmdate($activity['starttimeto'], 'Y-m-d H:i') : ''), 'color' => '#000000'),
                        'remark'   => array('value' => ($reason ? $reason : lang('plugin/singcere_wechat', 'wx_notice_act_viewmore')), 'color' => '#a3a3a3')
                    )
                ));
            }
        } else if ($message == 'activity_delete_completion') {        // 拒绝通知
            global $uidarray;
            foreach ($uidarray as $uid) {
                sc_wechat_notification('activity', array(
                    'to'    => 'uid:' . $uid,
                    'url'   => $_G['siteurl'] . 'forum.php?mod=viewthread&tid=' . $thread['tid'],
                    'color' => '#55990b',
                    'data'  => array(
                        'first'    => array('value' => lang('plugin/singcere_wechat', 'wx_notice_act_delete'), 'color' => '#FF0000'),
                        'keynote1' => array('value' => $thread['subject'], 'color' => '#000000'),
                        'keynote2' => array('value' => dgmdate($activity['starttimefrom'], 'Y-m-d H:i') . ($activity['starttimeto'] ? ' - ' . dgmdate($activity['starttimeto'], 'Y-m-d H:i') : ''), 'color' => '#000000'),
                        'remark'   => array('value' => ($reason ? $reason : lang('plugin/singcere_wechat', 'wx_notice_act_viewmore')), 'color' => '#a3a3a3')
                    )
                ));
            }
        } else if ($message == 'activity_notification_success') {    // 发送通知
            global $uidarray;
            foreach ($uidarray as $uid) {
                sc_wechat_notification('activity', array(
                    'to'    => 'uid:' . $uid,
                    'url'   => $_G['siteurl'] . 'forum.php?mod=viewthread&tid=' . $thread['tid'],
                    'color' => '#55990b',
                    'data'  => array(
                        'first'    => array('value' => $reason, 'color' => '#a3a3a3'),
                        'keynote1' => array('value' => $thread['subject'], 'color' => '#000000'),
                        'keynote2' => array('value' => dgmdate($activity['starttimefrom'], 'Y-m-d H:i') . ($activity['starttimeto'] ? ' - ' . dgmdate($activity['starttimeto'], 'Y-m-d H:i') : ''), 'color' => '#000000'),
                        //'remark' => array('value' => ($reason ? $reason : lang('plugin/singcere_wechat', 'wx_notice_act_viewmore')), 'color' => '#a3a3a3')
                    )
                ));
            }
        }
    }

}

class plugin_singcere_wechat extends plugin_singcere_wechat_base
{

    function common()
    {
        $this->common_base();
    }

    function deletemember($param)
    {
        $uids = $param['param'][0];
        $step = $param['step'];
        if ($step == 'check' && $uids && is_array($uids)) {
            C::t('#singcere_wechat#singcere_wechat_bind')->delete_by_uid($uids);
        }
    }

    function global_login_extra()
    {
        global $_G;
        if (!$this->allow || $_G['inshowmessage']) {
            return;
        }
        return singcere_wechat_tpl_login_extra_bar();
    }

    function global_login_text()
    {
        global $_G;
        if (!$this->allow || $_G['uid']) {
            return;
        }
        return singcere_wechat_tpl_login_extra_bar(true);
    }

    function global_usernav_extra1()
    {
        global $_G;
        if (!$this->allow) {
            return;
        }
        if (!$_G['uid'] || $_G['member']['openid']) {
            return;
        }
        return singcere_wechat_tpl_user_bar();
    }

    function global_header()
    {
        return '<link rel="stylesheet" href="source/plugin/singcere_wechat/template/static/global.css" type="text/css" media="all">';
    }

    function global_footer()
    {
        global $_G;

        if (!$_G['uid'] && $_G['singcere_wechat']['setting']['discuz_loginbar']) {
            $allowpage = array('forum_index', 'forum_forumdisplay', 'forum_viewthread', 'portal_index', 'portal_list', 'portal_view', 'home_space', 'group_index');
            if ($this->allow && in_array(CURSCRIPT . '_' . CURMODULE, $allowpage)) {
                include template('singcere_wechat:loginbar');
                return $html;
            }
        }
        return '';
    }
}

class plugin_singcere_wechat_forum extends plugin_singcere_wechat
{

    function post_editorctrl_left_output()
    {
        //return tpl_post_editorctrl_left();
    }

    function post_success_message($params)
    {
        global $thread, $_G, $ac, $nauthorid;

        list($message) = $params['param'];
        list($ac, $nauthorid) = explode('|', authcode($_GET['noticeauthor'], 'DECODE'));
        $nauthorid = $nauthorid ? $nauthorid : $thread['authorid'];
        if ($message == 'post_reply_succeed' && $nauthorid != $_G['uid'] && getstatus($thread['status'], 6)) {
            sc_wechat_notification('forumreply', array(
                    'to'    => 'uid:' . $nauthorid,
                    'url'   => $_G['siteurl'] . 'home.php?mod=space&do=notice&view=mypost&type=post&authorization=1',
                    'color' => '#55990b',
                    'data'  => array(
                        'first'    => array('value' => $thread['subject'], 'color' => '#4998e7'),
                        'keyword1' => array('value' => $_G['username'] . ' ' . lang('plugin/singcere_wechat', 'reply_your_thread'), 'color' => '#000000'),
                        'keyword2' => array('value' => lang('plugin/singcere_wechat', 'wx_notice_forumreply'), 'color' => '#000000'),
                    ))
            );
        }
    }

    function viewthread_postbottom_output()
    {
        global $_G;

        $return = array();

        if ($_G['singcere_wechat']['setting']['reward_enable']) {
            if ($GLOBALS['page'] == 1 && $_G['forum_firstpid'] && $GLOBALS['postlist'][$_G['forum_firstpid']]['invisible'] == 0) {
                global $tips;
                $tipsarr = array();
                foreach (explode("\n", $_G['singcere_wechat']['setting']['reward_tips']) as $tips) {
                    if ($tips = trim($tips)) {
                        $tipsarr[] = $tips;
                    }
                }
                $tips = $tipsarr[$_G['tid'] % count($tipsarr)];
                $return[0] = tpl_reward_pannel();
            }
        }
        return $return;
    }
}

class plugin_singcere_wechat_home extends plugin_singcere_wechat
{

    function spacecp_pm_message($params)
    {
        global $_G, $ac, $touid, $newusers;
        list($message) = $params['param'];

        if ($_GET['op'] == 'send' && ($message == 'do_success' || $message == 'message_send_result')) {        // PM通知
            $touid = $touid ? $touid : $_GET['topmuid'];
            $newusers = !empty($newusers) ? $newusers : array($touid => '');
            foreach ($newusers as $uid => $username) {
                sc_wechat_notification('pm', array(
                        'to'    => 'uid:' . $uid,
                        'url'   => $_G['siteurl'] . 'home.php?mod=space&do=pm&authorization=1',
                        'color' => '#55990b',
                        'data'  => array(
                            'first'    => array('value' => '', 'color' => '#4998e7'),
                            'keyword1' => array('value' => lang('plugin/singcere_wechat', 'pm_new'), 'color' => '#000000'),
                            'keyword2' => array('value' => lang('plugin/singcere_wechat', 'wx_notice_pm'), 'color' => '#000000'),
                        ))
                );
            }
        }
    }

    function spacecp_friend_message($params)
    {
        global $_G, $ac;
        list($message) = $params['param'];

        if ($_GET['op'] == 'add' && ($message == 'request_has_been_sent' || $message == 'friends_add')) {        // 添加好友,通过好友通知
            sc_wechat_notification('pm', array(
                    'to'    => 'uid:' . intval($_GET['uid']),
                    'color' => '#55990b',
                    'data'  => array(
                        'keyword1' => array('value' => ($message == 'request_has_been_sent' ? $_G['username'] . lang('plugin/singcere_wechat', 'wx_notice_friend_request1') : lang('plugin/singcere_wechat', 'wx_notice_friend_request2', array('username' => $_G['username']))), 'color' => '#000000'),
                        'keyword2' => array('value' => lang('plugin/singcere_wechat', 'wx_notice_friend_addmessage'), 'color' => '#000000'),
                        'remark'   => array('value' => $_GET['note'], 'color' => '#a3a3a3')
                    ))
            );
        }

    }
}

class plugin_singcere_wechat_member extends plugin_singcere_wechat
{
    function logging_method()
    {
        global $_G;
        if (!$this->allow) {
            return;
        }
        return singcere_wechat_tpl_login_bar();
    }

    function register_logging_method()
    {
        global $_G;
        if (!$this->allow) {
            return;
        }
        return singcere_wechat_tpl_login_bar();
    }
}


class mobileplugin_singcere_wechat extends plugin_singcere_wechat_base
{
    function common()
    {
        $this->common_base();

        if (isset($_GET['authorization'])) {
            if (!$this->allow) {
                return;
            }
            sc_wechat_auth(1);
        }

    }

    function global_header_mobile()
    {
        global $_G;

        $hHtml = '<link rel="stylesheet" href="source/plugin/singcere_wechat/template/static/global.css" type="text/css" media="all">';

        if (defined('IN_WECHAT') && $_G['singcere_wechat']['setting']['discuz_loadwxjs']) {
            $hHtml .= singcere_wechat_jssdk();
        }

        return $hHtml;
    }

    function global_footer_mobile()
    {
        global $_G, $wxData, $article, $metadescription;

        if (!defined('IN_WECHAT') || !$_G['singcere_wechat']['setting']['discuz_loadwxjs']) {
            return;
        }
        $jsTpl = '';
        if ($_G['basescript'] == 'forum' && CURMODULE == 'viewthread') {
            $wxData = array(
                'title'  => $_G['forum_thread']['subject'],
                'link'   => $_G['siteurl'] . 'forum.php?mod=viewthread&tid=' . $_G['tid'],
                'imgUrl' => $_G['singcere_wechat']['setting']['wechat_shareicon'],
                'desc'   => dhtmlspecialchars($metadescription)
            );
            $jsTpl = singcere_wechat_jsfooter();
        } else if ($_G['basescript'] == 'portal' && CURMODULE == 'view') {
            $wxData = array(
                'title'  => $article['title'],
                'link'   => $_G['siteurl'] . 'portal.php?mod=view&aid=' . $article['aid'],
                'imgUrl' => $article['pic'] ? $_G['singcere_wechat']['abs_attachurl'] . $article['pic'] : $_G['singcere_wechat']['setting']['wechat_shareicon'],
                'desc'   => dhtmlspecialchars(preg_replace("/\s/", '', $article['summary']))
            );
            $jsTpl = singcere_wechat_jsfooter();
        }

        return $jsTpl . tpl_global_footer();
    }


    function _createNonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

}

class mobileplugin_singcere_wechat_portal extends mobileplugin_singcere_wechat
{
    function view_article_procimghtml_output()
    {
        global $_G, $content;
        if (!IN_WECHAT) {
            return;
        }
        $content['content'] = preg_replace(
            array("/<a href=\"([^\"]*?)\".*>[\s]*<img.+src=\"([^\"]*?)\".*>[\s]*<\/a>/iU", "/<img.+src=\"([^\"]*?)\".*>/iU"),
            array("<a href='javascript:;' class='wxpreview' data-wcp='\\1'><img src='\\2'></a>", "<img src='\\1' class='wxpreview' data-wcp='\\1'>"),
            $content['content']
        );
    }
}

class mobileplugin_singcere_wechat_forum extends mobileplugin_singcere_wechat
{
    function viewthread_procimghtml_output()
    {
        global $postlist, $kkk, $_G;
        if (!IN_WECHAT) {
            return;
        }
        foreach ($postlist as &$post) {
            $post['message'] = preg_replace("/<a href=\"([^\"]*?)\".*>[\s]*(<img id=\"aimg_(\d+)\".*>[\s]*)<\/a>/ie", "\$this->_procImageTag('\\2', '\\3')", $post['message']);
        }
    }

    function _procImageTag($imgtag, $aid)
    {
        global $_G, $postlist;
        static $attachlist = array();
        if (empty($attachlist)) {
            foreach ($postlist as $post) {
                $attachlist += $post['attachments'];
            }
        }
        if (strpos($attachlist[$aid]['url'], 'http') === false) {
            $attachlist[$aid]['url'] = $_G['siteurl'] . $attachlist[$aid]['url'];
        }
        $imgtag = str_replace('&', '&amp;', $imgtag);
        $imgtag = str_replace('&amp;amp;', '&amp;', $imgtag);
        $imgtag = str_replace('\"', '"', $imgtag);
        return '<a href="javascript:;" class="wxpreview" data-wcp="' . $attachlist[$aid]['url'] . $attachlist[$aid]['attachment'] . '">' . $imgtag . '</a>';
    }

    // 为移动端发帖开启 接受回复通知
    function post_bottom_mobile()
    {
        return '<input type="hidden" name="allownoticeauthor" value="1">';
    }

    function post_success_message($params)
    {
        global $thread, $_G, $ac, $nauthorid;

        list($message) = $params['param'];
        list($ac, $nauthorid) = explode('|', authcode($_GET['noticeauthor'], 'DECODE'));
        $nauthorid = $nauthorid ? $nauthorid : $thread['authorid'];
        if ($message == 'post_reply_succeed' && $nauthorid != $_G['uid'] && getstatus($thread['status'], 6)) {
            sc_wechat_notification('forumreply', array(
                    'to'    => 'uid:' . $nauthorid,
                    'url'   => $_G['siteurl'] . 'home.php?mod=space&do=notice&view=mypost&type=post&authorization=1',
                    'color' => '#55990b',
                    'data'  => array(
                        'first'    => array('value' => $thread['subject'], 'color' => '#4998e7'),
                        'keyword1' => array('value' => $_G['username'] . ' ' . lang('plugin/singcere_wechat', 'reply_your_thread'), 'color' => '#000000'),
                        'keyword2' => array('value' => lang('plugin/singcere_wechat', 'wx_notice_forumreply'), 'color' => '#000000'),
                    ))
            );
        }
    }

    function viewthread_postbottom_mobile_output()
    {
        global $_G, $tips, $rewardlist, $count;
        $return = array();
        if ($_G['singcere_wechat']['setting']['reward_enable']) {
            if ($GLOBALS['page'] == 1 && $_G['forum_firstpid'] && $GLOBALS['postlist'][$_G['forum_firstpid']]['invisible'] == 0) {
                global $tips;
                $tipsarr = array();
                foreach (explode("\n", $_G['singcere_wechat']['setting']['reward_tips']) as $tips) {
                    if ($tips = trim($tips)) {
                        $tipsarr[] = $tips;
                    }
                }
                $tips = $tipsarr[$_G['tid'] % count($tipsarr)];
                $count = DB::result_first("SELECT COUNT(*) FROM %t WHERE fromid = %d AND fromtype = %s AND transaction_id <> ''", array('singcere_wechat_pay', $_G['tid'], 'SC_WECHAT_REWARD'));
                if ($count) {
                    $rewardlist = DB::fetch_all("SELECT * FROM %t WHERE fromid = %d AND fromtype = %s AND transaction_id <> '' ORDER BY dateline DESC limit 24", array(
                        'singcere_wechat_pay', $_G['tid'], 'SC_WECHAT_REWARD'
                    ));
                }
                $return[0] = tpl_reward_pannel();
            }
        }
        return $return;

    }
}

class mobileplugin_singcere_wechat_member extends mobileplugin_singcere_wechat
{
    function logging_bottom_mobile()
    {
        global $_G;
        if (!$this->allow) {
            return;
        }
        if ($_G['singcere_wechat']['setting']['discuz_allowregister'] && IN_WECHAT) {
            return tpl_login_bottom_wechat();
        }
    }
}

class mobileplugin_singcere_wechat_home extends mobileplugin_singcere_wechat
{
    function spacecp_pm_message($params)
    {
        global $_G, $ac, $touid, $newusers;
        if ($ac != 'pm' || $_GET['op'] != 'send') return;

        list($message) = $params['param'];
        if ($message == 'do_success' || $message == 'message_send_result') {
            $touid = $touid ? $touid : $_GET['topmuid'];
            $newusers = !empty($newusers) ? $newusers : array($touid => '');

            foreach ($newusers as $uid => $username) {
                sc_wechat_notification('pm', array(
                        'to'    => 'uid:' . $uid,
                        'url'   => $_G['siteurl'] . 'home.php?mod=space&do=pm&authorization=1',
                        'color' => '#55990b',
                        'data'  => array(
                            'first'    => array('value' => '', 'color' => '#4998e7'),
                            'keyword1' => array('value' => lang('plugin/singcere_wechat', 'pm_new'), 'color' => '#000000'),
                            'keyword2' => array('value' => lang('plugin/singcere_wechat', 'wx_notice_pm'), 'color' => '#000000'),
                        ))
                );
            }
        }
    }
}

class SC_WeChat
{

    static $QRCODE_EXPIRE = 1800;

    static public function getqrcode()
    {
        global $_G;
        $code = 0;
        $i = 0;
        do {
            $code = rand(100000, 999999);
            $codeexists = C::t('#singcere_wechat#singcere_wechat_authcode')->fetch_by_code($code);
            $i++;
        } while ($codeexists && $i < 10);

        if ($codeexists) {
            showmessage('singcere_wechat:qrcode_create_error');
        }

        $codeenc = urlencode(base64_encode(authcode($code, 'ENCODE', $_G['config']['security']['authkey'])));
        C::t('#singcere_wechat#singcere_wechat_authcode')->insert(array('sid' => $_G['cookie']['saltkey'], 'uid' => $_G['uid'], 'code' => $code, 'createtime' => TIMESTAMP), 0, 1);

        if (!discuz_process::islocked('clear_singcere_wechat_authcode')) {
            C::t('#singcere_wechat#singcere_wechat_authcode')->delete_history();
            discuz_process::unlock('clear_singcere_wechat_authcode');
        }
        return array($codeenc, $code);
    }

    static public function redirect($type)
    {
        global $_G;
        $hook = unserialize($_G['setting']['wechatredirect']);
        if (!$hook || !in_array($hook['plugin'], $_G['setting']['plugins']['available'])) {
            return;
        }
        if (!preg_match("/^[\w\_]+$/i", $hook['plugin']) || !preg_match('/^[\w\_\.]+\.php$/i', $hook['include'])) {
            return;
        }
        include_once DISCUZ_ROOT . 'source/plugin/' . $hook['plugin'] . '/' . $hook['include'];
        if (!class_exists($hook['class'], false)) {
            return;
        }
        $class = new $hook['class'];
        if (!method_exists($class, $hook['method'])) {
            return;
        }
        $return = $class->$hook['method']($type);
        if ($return) {
            return $return;
        }
    }

    static public function getnewname($username)
    {
        global $_G;
        $newname = cutstr(WeChatEmoji::clear($username), 15, '');

        if($newname) {
            $censorexp = '/^(' . str_replace(array('\\*', "\r\n", ' '), array('.*', '|', ''), preg_quote(($_G['setting']['censoruser'] = trim($_G['setting']['censoruser'])), '/')) . ')$/i';
            $newname = preg_replace($censorexp, '', $newname);

            $guestexp = '\xA1\xA1|\xAC\xA3|^Guest|^\xD3\xCE\xBF\xCD|\xB9\x43\xAB\xC8';
            $newname = preg_replace("/\s+|^c:\\con\\con|[%,\*\"\s\<\>\&]|$guestexp/is", '', $newname);
        }

        if(dstrlen($newname) >= 3) {
            loaducenter();
            if (uc_get_user($newname)) {
                $newname = cutstr($newname, 6, '');
                $newname = 'wx_' . $newname . '_' . random(5);
            }
        } else if($newname) {
            $newname = 'wx_' . $newname . '_' . random(5);
        } else {
            $newname = 'wx_' . random(5);
        }

        return $newname;
    }

    static public function register($username, $inapi = 0, $groupid = 0, $sex = 0)
    {
        global $_G;
        if (!$username && IN_WECHATAPI) {
            echo WeChatServer::getXml4Txt(lang('message', 'undefined_action'));
            exit;
        } else if (!$username) {
            showmessage('undefined_action');
        }

        if (!$_G['singcere_wechat']['setting']) {
            $_G['singcere_wechat']['setting'] = unserialize($_G['setting']['singcere_wechat']);
        }

        loaducenter();
        $groupid = !$groupid ? $_G['setting']['newusergroupid'] : $groupid;

        $password = md5(random(10));
        $email = 'wechat_' . strtolower(random(10)) . '@null.null';

        $usernamelen = dstrlen($username);
        if ($usernamelen < 3) {
            $username = 'wx_' . $username . '_' . random(5);
        }
        if ($usernamelen > 15) {
            if (!IN_WECHATAPI) {
                showmessage('profile_username_toolong');
            } else {
                echo WeChatServer::getXml4Txt(lang('message', 'profile_username_toolong'));
                exit;
            }
        }

        $censorexp = '/^(' . str_replace(array('\\*', "\r\n", ' '), array('.*', '|', ''), preg_quote(($_G['setting']['censoruser'] = trim($_G['setting']['censoruser'])), '/')) . ')$/i';

        if (@preg_match($censorexp, $username)) {
            if (!IN_WECHATAPI) {
                showmessage('profile_username_protect');
            } else {
                echo WeChatServer::getXml4Txt(lang('message', 'profile_username_protect'));
                exit;
            }
        }

        if (!$_G['singcere_wechat']['setting']['discuz_disableregrule']) {
            loadcache('ipctrl');
            if ($_G['cache']['ipctrl']['ipregctrl']) {
                foreach (explode("\n", $_G['cache']['ipctrl']['ipregctrl']) as $ctrlip) {
                    if (preg_match("/^(" . preg_quote(($ctrlip = trim($ctrlip)), '/') . ")/", $_G['clientip'])) {
                        $ctrlip = $ctrlip . '%';
                        $_G['setting']['regctrl'] = $_G['setting']['ipregctrltime'];
                        break;
                    } else {
                        $ctrlip = $_G['clientip'];
                    }
                }
            } else {
                $ctrlip = $_G['clientip'];
            }

            if ($_G['setting']['regctrl']) {
                if (C::t('common_regip')->count_by_ip_dateline($ctrlip, $_G['timestamp'] - $_G['setting']['regctrl'] * 3600)) {
                    if (!IN_WECHATAPI) {
                        showmessage('register_ctrl', null, array('regctrl' => $_G['setting']['regctrl']));
                    } else {
                        echo WeChatServer::getXml4Txt(lang('message', 'register_ctrl', array('regctrl' => $_G['setting']['regctrl'])));
                        exit;
                    }
                }
            }

            $setregip = null;
            if ($_G['setting']['regfloodctrl']) {
                $regip = C::t('common_regip')->fetch_by_ip_dateline($_G['clientip'], $_G['timestamp'] - 86400);
                if ($regip) {
                    if ($regip['count'] >= $_G['setting']['regfloodctrl']) {
                        if (!IN_WECHATAPI) {
                            showmessage('register_flood_ctrl', null, array('regfloodctrl' => $_G['setting']['regfloodctrl']));
                        } else {
                            echo WeChatServer::getXml4Txt(lang('message', 'register_flood_ctrl', array('regfloodctrl' => $_G['setting']['regfloodctrl'])));
                            exit;
                        }
                    } else {
                        $setregip = 1;
                    }
                } else {
                    $setregip = 2;
                }
            }

            if ($setregip !== null) {
                if ($setregip == 1) {
                    C::t('common_regip')->update_count_by_ip($_G['clientip']);
                } else {
                    C::t('common_regip')->insert(array('ip' => $_G['clientip'], 'count' => 1, 'dateline' => $_G['timestamp']));
                }
            }
        }

        $uid = uc_user_register(addslashes($username), $password, $email, '', '', $_G['clientip']);
        if ($uid <= 0) {
            if (!IN_WECHATAPI) {
                if ($uid == -1) {
                    showmessage('profile_username_illegal');
                } elseif ($uid == -2) {
                    showmessage('profile_username_protect');
                } elseif ($uid == -3) {
                    showmessage('profile_username_duplicate');
                } elseif ($uid == -4) {
                    showmessage('profile_email_illegal');
                } elseif ($uid == -5) {
                    showmessage('profile_email_domain_illegal');
                } elseif ($uid == -6) {
                    showmessage('profile_email_duplicate');
                } else {
                    showmessage('undefined_action');
                }
            } else {
                if ($uid == -1) {
                    echo WeChatServer::getXml4Txt(lang('message', 'profile_username_illegal'));
                } elseif ($uid == -2) {
                    echo WeChatServer::getXml4Txt(lang('message', 'profile_username_protect'));
                } elseif ($uid == -3) {
                    echo WeChatServer::getXml4Txt(lang('message', 'profile_username_duplicate'));
                } elseif ($uid == -4) {
                    echo WeChatServer::getXml4Txt(lang('message', 'profile_email_illegal'));
                } elseif ($uid == -5) {
                    echo WeChatServer::getXml4Txt(lang('message', 'profile_email_domain_illegal'));
                } elseif ($uid == -6) {
                    echo WeChatServer::getXml4Txt(lang('message', 'profile_email_duplicate'));
                } else {
                    echo WeChatServer::getXml4Txt(lang('message', 'undefined_action'));
                }
                exit;
            }
        }

        $init_arr = array(
            'credits' => explode(',', $_G['setting']['initcredits']),
            'profile' => array('gender' => $sex),
        );
        C::t('common_member')->insert($uid, $username, $password, $email, $_G['clientip'], $groupid, $init_arr);

        if ($_G['setting']['regctrl'] || $_G['setting']['regfloodctrl']) {
            C::t('common_regip')->delete_by_dateline($_G['timestamp'] - ($_G['setting']['regctrl'] > 72 ? $_G['setting']['regctrl'] : 72) * 3600);
            if ($_G['setting']['regctrl']) {
                C::t('common_regip')->insert(array('ip' => $_G['clientip'], 'count' => -1, 'dateline' => $_G['timestamp']));
            }
        }

        if ($_G['setting']['regverify'] == 2) {
            C::t('common_member_validate')->insert(array(
                'uid'         => $uid,
                'submitdate'  => $_G['timestamp'],
                'moddate'     => 0,
                'admin'       => '',
                'submittimes' => 1,
                'status'      => 0,
                'message'     => '',
                'remark'      => '',
            ), false, true);
            manage_addnotify('verifyuser');
        }

        setloginstatus(array(
            'uid'      => $uid,
            'username' => $username,
            'password' => $password,
            'groupid'  => $groupid,
        ), 0);

        //统计
        include_once libfile('function/stat');
        updatestat('register');

        return $uid;
    }

    /**
     * 同步微信头像处理
     */
    static public function syncAvatar($uid, $avatar)
    {

        if (!$uid || !$avatar) {
            return false;
        }

        if (!$content = dfsockopen($avatar)) {
            return false;
        }

        $tmpFile = DISCUZ_ROOT . './data/avatar/' . TIMESTAMP . random(6);
        file_put_contents($tmpFile, $content);

        if (!is_file($tmpFile)) {
            return false;
        }

        $result = SC_uploadUcAvatar::upload($uid, $tmpFile);
        unlink($tmpFile);

        C::t('common_member')->update($uid, array('avatarstatus' => '1'));

        return $result;
    }


}

/**
 * 上传至uc头像
 */
class SC_uploadUcAvatar
{
    public static function upload($uid, $localFile)
    {

        global $_G;
        if (!$uid || !$localFile) {
            return false;
        }

        list($width, $height, $type, $attr) = getimagesize($localFile);
        if (!$width) {
            return false;
        }

        if ($width < 10 || $height < 10 || $type == 4) {
            return false;
        }

        $imageType = array(1 => '.gif', 2 => '.jpg', 3 => '.png');
        $fileType = $imgType[$type];
        if (!$fileType) {
            $fileType = '.jpg';
        }
        $avatarPath = $_G['setting']['attachdir'];
        $tmpAvatar = $avatarPath . './temp/upload' . $uid . $fileType;
        file_exists($tmpAvatar) && @unlink($tmpAvatar);
        file_put_contents($tmpAvatar, file_get_contents($localFile));

        if (!is_file($tmpAvatar)) {
            return false;
        }

        $tmpAvatarBig = './temp/upload' . $uid . 'big' . $fileType;
        $tmpAvatarMiddle = './temp/upload' . $uid . 'middle' . $fileType;
        $tmpAvatarSmall = './temp/upload' . $uid . 'small' . $fileType;

        $image = new image;
        if ($image->Thumb($tmpAvatar, $tmpAvatarBig, 200, 250, 1) <= 0) {
            return false;
        }
        if ($image->Thumb($tmpAvatar, $tmpAvatarMiddle, 120, 120, 1) <= 0) {
            return false;
        }
        if ($image->Thumb($tmpAvatar, $tmpAvatarSmall, 48, 48, 2) <= 0) {
            return false;
        }

        $tmpAvatarBig = $avatarPath . $tmpAvatarBig;
        $tmpAvatarMiddle = $avatarPath . $tmpAvatarMiddle;
        $tmpAvatarSmall = $avatarPath . $tmpAvatarSmall;

        $avatar1 = self::byte2hex(file_get_contents($tmpAvatarBig));
        $avatar2 = self::byte2hex(file_get_contents($tmpAvatarMiddle));
        $avatar3 = self::byte2hex(file_get_contents($tmpAvatarSmall));

        $extra = '&avatar1=' . $avatar1 . '&avatar2=' . $avatar2 . '&avatar3=' . $avatar3;
        $result = self::uc_api_post_ex('user', 'rectavatar', array('uid' => $uid), $extra);

        @unlink($tmpAvatar);
        @unlink($tmpAvatarBig);
        @unlink($tmpAvatarMiddle);
        @unlink($tmpAvatarSmall);

        return true;
    }

    public static function byte2hex($string)
    {
        $buffer = '';
        $value = unpack('H*', $string);
        $value = str_split($value[1], 2);
        $b = '';
        foreach ($value as $k => $v) {
            $b .= strtoupper($v);
        }

        return $b;
    }

    public static function uc_api_post_ex($module, $action, $arg = array(), $extra = '')
    {
        $s = $sep = '';
        foreach ($arg as $k => $v) {
            $k = urlencode($k);
            if (is_array($v)) {
                $s2 = $sep2 = '';
                foreach ($v as $k2 => $v2) {
                    $k2 = urlencode($k2);
                    $s2 .= "$sep2{$k}[$k2]=" . urlencode(uc_stripslashes($v2));
                    $sep2 = '&';
                }
                $s .= $sep . $s2;
            } else {
                $s .= "$sep$k=" . urlencode(uc_stripslashes($v));
            }
            $sep = '&';
        }
        $postdata = uc_api_requestdata($module, $action, $s, $extra);
        return uc_fopen2(UC_API . '/index.php', 500000, $postdata, '', true, UC_IP, 20);
    }
}

