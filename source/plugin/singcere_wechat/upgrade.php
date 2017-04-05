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

// add location
if ($_GET['fromversion'] < "1.5.8") {
    DB::query("ALTER TABLE %t ADD `lat` float(10,6) NOT NULL", array('singcere_wechat_bind'));
    DB::query("ALTER TABLE %t ADD `lng` float(10,6) NOT NULL", array('singcere_wechat_bind'));
}


if($_GET['fromversion'] < "1.7") {
    DB::query("ALTER TABLE %t ADD `setting` blob NOT NULL", array('singcere_wechat_bind'));
}

if($_GET['fromversion'] < "1.8.2") {
    $setting = (array)unserialize($_G['setting']['singcere_wechat']);
    if($setting['wechat_qrcode']) {
        $setting['wechat_qrcode'] = 'common/'.$setting['wechat_qrcode'];
        C::t('common_setting')->update_batch(array('singcere_wechat' => serialize($setting)));
        updatecache('setting');
    }
}

if($_GET['fromversion'] < "1.8.5") {
    $query = DB::query("SHOW INDEX FROM ".DB::table('singcere_wechat_bind'));
    while($temp = DB::fetch($query)) {
        if($temp['Key_name'] == 'uid') {
            DB::query("ALTER TABLE %t DROP INDEX uid", array('singcere_wechat_bind'));
            break;
        }
    }
}

if($_GET['fromversion'] < "1.8.6") {
$fact_table_sql = <<<EOF
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
  `return` varchar(255) NOT NULL DEFAULT '0',
  `tid` mediumint(8) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`factid`)
) ENGINE=MyISAM;
EOF;
    runquery($fact_table_sql);

}

if($_GET['fromversion'] < "3.0") {
    $pay_table_sql = <<<EOF
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
    runquery($pay_table_sql);
}


if($_GET['fromversion'] < "3.0.3") {
    DB::query("ALTER TABLE %t DROP INDEX openid", array('singcere_wechat_bind'));
    DB::query("ALTER TABLE %t ADD INDEX openid (`openid`)", array('singcere_wechat_bind'));
    DB::query("ALTER TABLE %t ADD INDEX unionid (`unionid`)", array('singcere_wechat_bind'));
}

if($_GET['fromversion'] < "3.1") {
    DB::query("ALTER TABLE %t ADD INDEX status (`status`)", array('singcere_wechat_cmd'));
}

$finish = TRUE;


?>