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

global $_G, $installlang;

$sql = <<<EOF

CREATE TABLE IF NOT EXISTS `pre_singcere_wechat_bind` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `openid` char(32) NOT NULL DEFAULT '',
  `unionid` char(32) NOT NULL DEFAULT '',
  `uid` mediumint(8) NOT NULL DEFAULT '0',
  `username` char(15) NOT NULL DEFAULT '',
  `status` smallint(6) NOT NULL DEFAULT '0',
  `dateline` int(10) NOT NULL DEFAULT '0',
  `nickname` varchar(32) NOT NULL DEFAULT '',
  `sex` tinyint(1) NOT NULL DEFAULT '0',
  `counts` smallint(6) NOT NULL DEFAULT '0',
  `lastauth` int(10) NOT NULL DEFAULT '0',
  `subscribe` tinyint(1) NOT NULL DEFAULT '-1',
  `isregister` tinyint(1) unsigned NOT NULL default '0',
  `lat` float(10,6) NOT NULL,
  `lng` float(10,6) NOT NULL,
  `setting` blob NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `openid` (`openid`),
  KEY `unionid` (`unionid`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `pre_singcere_wechat_cmd` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `cmdname` varchar(32) NOT NULL DEFAULT '',
  `alias` varchar(128) NOT NULL DEFAULT '',
  `cmdrtn` varchar(255) NOT NULL DEFAULT '',
  `helptext` varchar(255) NOT NULL DEFAULT '',
  `pattern` varchar(255) NOT NULL DEFAULT '',
  `displayorder` tinyint(2) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `type` varchar(10) NOT NULL DEFAULT '',
  `responsetype` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `pre_singcere_wechat_richresponse` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `cmdid` int(11) NOT NULL DEFAULT '0',
  `title` varchar(32) NOT NULL DEFAULT '',
  `imgurl` varchar(128) NOT NULL DEFAULT '',
  `link` varchar(128) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;
    
CREATE TABLE IF NOT EXISTS `pre_singcere_wechat_tmplmsg` (
  `msgid` int(11) UNSIGNED NOT NULL,
  `openid` char(32) NOT NULL DEFAULT '',
  `template` varchar(64) NOT NULL DEFAULT '',
  `url` varchar(128) NOT NULL DEFAULT '',
  `topcolor` char(6) NOT NULL DEFAULT '',
  `data` text NOT NULL,
  `errcode` varchar(6) NOT NULL DEFAULT '',
  `status` varchar(255) NOT NULL DEFAULT '',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`msgid`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `pre_singcere_wechat_authcode` (
  `sid` char(6) NOT NULL,
  `code` int(10) unsigned NOT NULL,
  `uid` mediumint(8) unsigned NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `createtime` int(10) unsigned NOT NULL,
  PRIMARY KEY (`sid`),
  UNIQUE KEY `code` (`code`),
  KEY `createtime` (`createtime`)
) ENGINE=MyISAM;
    
CREATE TABLE IF NOT EXISTS `pre_singcere_wechat_fact` (
  `factid` int(10) NOT NULL AUTO_INCREMENT,
  `openid` char(32) NOT NULL DEFAULT '',
  `uid` mediumint(8) NOT NULL DEFAULT '0',
  `username` char(15) NOT NULL DEFAULT '',
  `subject` char(80) NOT NULL DEFAULT '',
  `message` mediumtext NOT NULL,
  `attachment` blob NOT NULL,
  `anonymous` tinyint(1) NOT NULL DEFAULT '0',
  `mobile` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `return` varchar(255) NOT NULL DEFAULT '0',
  `tid` mediumint(8) NOT NULL DEFAULT '0',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`factid`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `pre_singcere_wechat_pay` (
  `id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `openid` char(32) NOT NULL DEFAULT '',
  `unionid` char(32) NOT NULL DEFAULT '',
  `uid` mediumint(8) NOT NULL DEFAULT 0,
  `username` char(15) NOT NULL DEFAULT '',
  `out_trade_no` varchar(32) NOT NULL DEFAULT '',
  `product_id` varchar(32) NOT NULL DEFAULT '',
  `senceid` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'JSAPI:1 NATIVE:2 APP:3 MICROPAY:4',
  `prepay_id` varchar(64) NOT NULL DEFAULT '',
  `fee_type` varchar(16) NOT NULL DEFAULT '',
  `total_fee` int(10) NOT NULL DEFAULT 0,
  `refund_fee` int(10) NOT NULL DEFAULT 0,
  `attach` varchar(127) NOT NULL DEFAULT '',
  `transaction_id` char(32) NOT NULL DEFAULT '',
  `err_code` varchar(32) NOT NULL DEFAULT '',
  `fromtype` varchar(32) NOT NULL DEFAULT '',
  `fromid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `dateline` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `out_trade_no` (`out_trade_no`),
  KEY `transaction_id` (`transaction_id`),
  KEY `fromid` (`fromid`, `fromtype`, `dateline`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `pre_singcere_wechat_redpack` (
  `id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `re_openid` char(32) NOT NULL DEFAULT '',
  `mch_billno` varchar(32) NOT NULL DEFAULT '',
  `send_name` varchar(32) NOT NULL DEFAULT '',
  `total_amount` int(10) NOT NULL DEFAULT 0,
  `total_num` int(10) NOT NULL DEFAULT 0,
  `amt_type` varchar(32) NOT NULL DEFAULT '',
  `wishing` char(127) NOT NULL DEFAULT '',
  `act_name` varchar(32) NOT NULL DEFAULT '',
  `remark` varchar(255) NOT NULL DEFAULT '',
  `fromtype` varchar(32) NOT NULL DEFAULT '',
  `fromid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `send_listid` varchar(32) NOT NULL DEFAULT '',
  `dateline` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mch_billno` (`mch_billno`),
  KEY `send_listid` (`send_listid`),
  KEY `fromid` (`fromid`, `fromtype`, `dateline`)
) ENGINE=MyISAM;
EOF;

runquery($sql);
$finish = TRUE;

?>