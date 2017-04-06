<?php
/**
 *		作者：rpan.cn
 *		版权所有：阿木 & rpancn
 *		QQ:399051063
 *		申明：此插件非开源软件，您不得对插件源代码进行任何形式任何目的的再发布。
 *		=========================================================================
 *			  承接discuz插件、模板仿制定制业务，价格便宜交期快QQ399051063
 *		=========================================================================
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$sql = <<<EOF

DROP TABLE IF EXISTS cdb_rpancn_buy_user_group_record;
DROP TABLE IF EXISTS cdb_rpancn_buy_user_group_package;

EOF;


$finish = TRUE;