<?php


if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

class vipreward_admin extends extend_admin{
	public function mrun(){
		global $lang,$_G;
		$viprewardpath = DISCUZ_ROOT.'./source/plugin/dc_signin/data/vipreward.config.php';
		if(submitcheck('submit')){
			$data = array(
				'open'=>dintval($_GET['open']),
				'vipgroup'=>dintval($_GET['vipgroup'],1),
				'dcvip'=>dintval($_GET['dcvip']),
				'extcredit'=>dintval($_GET['extcredit']),
				'minreward'=>dintval($_GET['minreward']),
				'maxreward'=>dintval($_GET['maxreward']),
			);
			$configdata = 'return '.var_export($data, true).";\n\n";
			if($fp = @fopen($viprewardpath, 'wb')) {
				fwrite($fp, "<?php\n//plugin dc_signin config file, DO NOT modify me!\n//Identify: ".md5($k.$configdata)."\n\n$configdata?>");
				fclose($fp);
			}
			cpmsg($this->_lang['succeed'], 'action=plugins&operation=config&do='.$pluginid.'&identifier=dc_signin&pmod=extend', 'succeed');
		}
		$vipreward = @include $viprewardpath;
		$query = C::t('common_usergroup')->range_orderby_credit();
		$groupselect = array();
		foreach($query as $group) {
			$group['type'] = $group['type'] == 'special' && $group['radminid'] ? 'specialadmin' : $group['type'];
			$groupselect[$group['type']] .= '<option value="'.$group['groupid'].'"'.(@in_array($group['groupid'], $vipreward['vipgroup']) ? ' selected' : '').'>'.$group['grouptitle'].'</option>';
		}
		$groupstr = '<select name="vipgroup[]" size="10" multiple="multiple"><option value="">'.cplang('plugins_empty').'</option><optgroup label="'.$lang['usergroups_member'].'">'.$groupselect['member'].'</optgroup>'.
			($groupselect['special'] ? '<optgroup label="'.$lang['usergroups_special'].'">'.$groupselect['special'].'</optgroup>' : '').
			($groupselect['specialadmin'] ? '<optgroup label="'.$lang['usergroups_specialadmin'].'">'.$groupselect['specialadmin'].'</optgroup>' : '').
			'<optgroup label="'.$lang['usergroups_system'].'">'.$groupselect['system'].'</optgroup></select>';
		$creditstr = '<select name="extcredit"><option value="">'.cplang('plugins_empty').'</option>';
		foreach($_G['setting']['extcredits'] as $k=>$credit){
			if($vipreward['extcredit']==$k)
				$creditstr .= '<option value="'.$k.'" selected>'.$credit['title'].'</option>';
			else
				$creditstr .= '<option value="'.$k.'">'.$credit['title'].'</option>';
		}
		showformheader('plugins&operation=config&identifier=dc_signin&pmod=extend&act=set&f=vipreward');
		showtableheader($this->_lang['install_title']);
		showsetting($this->_lang['open'], 'open', $vipreward['open']);
		showsetting($this->_lang['extcredit'], 'extcredit', $nposition['extcredit'], $creditstr);
		showsetting($this->_lang['dcvip'], 'dcvip', $vipreward['dcvip'],'radio','','',$this->_lang['dcvipmsg']);
		showsetting($this->_lang['vipgroup'], 'vipgroup', $vipreward['vipgroup'], $groupstr);
		showsetting($this->_lang['minreward'], 'minreward', $vipreward['minreward'],'number');
		showsetting($this->_lang['maxreward'], 'maxreward', $vipreward['maxreward'],'number');
		
		showsubmit('submit');
		showformfooter();
	}

}
?>