<?php
/*
	Install Uninstall Upgrade AutoStat System Code
*/
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$sql = <<<EOF
CREATE TABLE IF NOT EXISTS `pre_plugin_ljmajia` (
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `username` varchar(45) NOT NULL default '',
  `status` tinyint(3) unsigned NOT NULL default '1',
  `insert_time` int(11) NOT NULL,
  PRIMARY KEY  (`uid`),
  UNIQUE KEY `username_UNIQUE` (`username`)
);
CREATE TABLE IF NOT EXISTS `pre_alj_session` (
  `ming` varchar(255) NOT NULL,
  `timestamp` varchar(255) NOT NULL
);
CREATE TABLE IF NOT EXISTS `pre_alj_count` (
  `id` int(10) NOT NULL auto_increment,
  `tid` int(10) NOT NULL,
  `subject` varchar(255)  NOT NULL,
  `dateline` bigint(20) NOT NULL,
  `replies` int(10) NOT NULL,
  `fid` int(10) NOT NULL,
  `huitie` mediumtext  NOT NULL,
  `uid` int(11) NOT NULL,
  `username` varchar(255)  NOT NULL,
  PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `pre_alj_count_pro` (
  `id` int(10) NOT NULL auto_increment,
  `aid` int(10) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `dateline` bigint(20) NOT NULL,
  `replies` int(10) NOT NULL,
  `huitie` mediumtext NOT NULL,
  `uid` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
)
EOF;

runquery($sql);
//finish to put your own code
$finish = TRUE;
?>