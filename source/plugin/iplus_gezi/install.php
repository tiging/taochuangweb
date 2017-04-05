<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$sql = <<<EOF
CREATE TABLE IF NOT EXISTS `pre_iplus_gezi` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `title` varchar(50) NOT NULL,
  `style` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `dateline` int(11) NOT NULL,
  `lastdate` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;



EOF;

runquery($sql);

$finish = true;

?>