<?php


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class vipreward_signin extends extend_signin{

	public function mrun(){
		global $_G;
		$isthread = false;
		$config = @include DISCUZ_ROOT.'./source/plugin/dc_signin/data/vipreward.config.php';
		if(!$config['open']||!$config['extcredit'])return;
		$info = SigninInfo::Get();
		$isvip = false;
		if($_G['cache']['plugin']['dc_vip']['open']&&$config['dcvip']){
			$uvip = C::t('#dc_vip#dc_vip')->fetch($info['user']['uid']);
			if($uvip&&$uvip['exptime']>=TIMESTAMP){
				$vipgroup = C::t('#dc_vip#dc_vip_group')->fetch($uvip['vgid']);
				$vghook = dunserialize($vipgroup['hook']);
				$credit = dintval($vghook['dc_signin']['vipsigin']);
				$isvip = true;
			}
		}
		if(in_array($info['user']['groupid'],$config['vipgroup'])||$isvip){
			if(!$credit)
				$credit = rand($config['minreward'],$config['maxreward']);
			$extcredit[$config['extcredit']] = $credit;
			updatemembercount($info['user']['uid'], $extcredit, true, '', 0, '',$this->_lang['pluginname'],$this->_lang['jlmsg']);
			return $this->_lang['vipreward'].$_G['setting']['extcredits'][$config['extcredit']]['title'].$credit;
		}
	}

}
?>