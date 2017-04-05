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

global $_G;


$sql = <<<EOF
DROP TABLE IF EXISTS pre_singcere_wechat_bind;
DROP TABLE IF EXISTS pre_singcere_wechat_cmd;
DROP TABLE IF EXISTS pre_singcere_wechat_richresponse;
DROP TABLE IF EXISTS pre_singcere_wechat_tmplmsg;
DROP TABLE IF EXISTS pre_singcere_wechat_authcode;

DROP TABLE IF EXISTS pre_singcere_wechat_pay;
DROP TABLE IF EXISTS pre_singcere_wechat_redpack;
EOF;

runquery($sql);
$finish = TRUE;