<?php
/**
 *	[ÑûÇë×¢²á(invite_aboc.{modulename})] (C)2014-2099 Powered by aboc.
 *	Version: 1.0.0
 *	Date: 2014-8-30 21:01
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class plugin_invite_aboc {

    public $set = array();

    function __construct(){
        $this->plugin_invite_aboc();
    }

    function plugin_invite_aboc(){
        global $_G;
        $this->set = $_G['cache']['plugin']['invite_aboc'];
        $this->set['ban_group_id'] = @unserialize($this->set['ban_group_id']);
    }

    function common(){
        global $_G;
//        print_r($_G['member']);
        $fromuid = !empty($_G['cookie']['promotion']) && $_G['setting']['creditspolicy']['promotion_register'] ? intval($_G['cookie']['promotion']) : 0;
        if(!$fromuid || !$_G['uid']){
            return;
        }
        if(($_G['timestamp'] - $_G['member']['regdate']) < 200000){
            C::t("#invite_aboc#invite_aboc")->update_fromuid($_G['uid'], $_G['username'], $fromuid);
        }


    }

}

