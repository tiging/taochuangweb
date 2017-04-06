<?php


if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

class nposition_admin extends extend_admin{
	public function mrun(){
		global $_G;
		$npositionpath = DISCUZ_ROOT.'./source/plugin/dc_signin/data/nposition.config.php';
		if(submitcheck('submit')){
			$data = array(
				'extcredit'=>dintval($_GET['extcredit']),
				'reward'=>trim($_GET['reward']),
			);
			$configdata = 'return '.var_export($data, true).";\n\n";
			if($fp = @fopen($npositionpath, 'wb')) {
				fwrite($fp, "<?php\n//plugin dc_signin config file, DO NOT modify me!\n//Identify: ".md5($k.$configdata)."\n\n$configdata?>");
				fclose($fp);
			}
			cpmsg($this->_lang['succeed'], 'action=plugins&operation=config&do='.$pluginid.'&identifier=dc_signin&pmod=extend', 'succeed');
		}
		$nposition = @include $npositionpath;
		$creditstr = '<select name="extcredit"><option value="">'.cplang('plugins_empty').'</option>';
		foreach($_G['setting']['extcredits'] as $k=>$credit){
			if($nposition['extcredit']==$k)
				$creditstr .= '<option value="'.$k.'" selected>'.$credit['title'].'</option>';
			else
				$creditstr .= '<option value="'.$k.'">'.$credit['title'].'</option>';
		}
		$creditstr .= '</select>';
		showformheader('plugins&operation=config&identifier=dc_signin&pmod=extend&act=set&f=nposition');
		showtableheader($this->_lang['install_title']);
		showsetting($this->_lang['extcredit'], 'extcredit', $nposition['extcredit'], $creditstr);
		showsetting($this->_lang['reward'], 'reward',  $nposition['reward']?$nposition['reward']:'10,9,8,7,6,5,4,3,2,1','text','','',$this->_lang['rewardmsg']);
		showsubmit('submit');
		showformfooter();
	}

}
?>