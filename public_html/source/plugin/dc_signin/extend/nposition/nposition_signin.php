<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class nposition_signin extends extend_signin{

	public function mrun(){
		global $_G;
		$nposition = @include DISCUZ_ROOT.'./source/plugin/dc_signin/data/nposition.config.php';
		if(!$nposition['extcredit']||!$_G['setting']['extcredits'][$nposition['extcredit']]||!$nposition['reward'])return;
		$reward = explode(',',$nposition['reward']);
		$info = SigninInfo::Get();
		$credit = dintval($reward[$info['signinstats']['todaycount']-1]);
		if(!$credit)return;
		$jlmsg = str_replace('{n}',$info['signinstats']['todaycount'],$this->_lang['jlmsg']);
		updatemembercount($info['user']['uid'], array($nposition['extcredit']=>$credit), true, '', 0, '',$this->_lang['pluginname'],$jlmsg);
		return $this->_lang['ewjl'].$_G['setting']['extcredits'][$nposition['extcredit']]['title'].$credit;
	}

}
?>