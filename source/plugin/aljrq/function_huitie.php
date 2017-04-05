<?php
function notification_add_huitie1($touid, $type, $note, $notevars = array(), $system = 0,$uid,$username) {
		$_G['uid']=intval(daddslashes($uid));
		$_G['member']['username']=$username;
		$_G['username']=$username;
		$touid=intval(daddslashes($touid));
		if(!($tospace = getuserbyuid($touid))) {
			return false;
		}
		space_merge($tospace, 'field_home');
		$filter = empty($tospace['privacy']['filter_note'])?array():array_keys($tospace['privacy']['filter_note']);

		if($filter && (in_array($type.'|0', $filter) || in_array($type.'|'.$_G['uid'], $filter))) {
			return false;
		}
		
		$notevars['actor'] = "<a href=\"home.php?mod=space&uid=$_G[uid]\">".$_G['member']['username']."</a>";
		if(!is_numeric($type)) {
			$vars = explode(':', $note);
			
			if(count($vars) == 2) {
				$notestring = lang('plugin/'.$vars[0], $vars[1], $notevars);
			} else {
				//debug($notevars);
				$notestring = lang('notification', $note, $notevars);
				
			}
			$frommyapp = false;
		} else {
			$frommyapp = true;
			$notestring = $note;
		}

		$oldnote = array();
		if($notevars['from_id'] && $notevars['from_idtype']) {
			$oldnote = DB::fetch_first("SELECT * FROM ".DB::table('home_notification')."
			WHERE from_id='$notevars[from_id]' AND from_idtype='$notevars[from_idtype]' AND uid='$touid'");
		}
		if(empty($oldnote['from_num'])) $oldnote['from_num'] = 0;
		$notevars['from_num'] = $notevars['from_num'] ? $notevars['from_num'] : 1;
		$setarr = array(
			'uid' => $touid,
			'type' => $type,
			'new' => 1,
			'authorid' => $_G['uid'],
			'author' => $_G['username'],
			'note' => $notestring,
			'dateline' => time(),
			'from_id' => $notevars['from_id'],
			'from_idtype' => $notevars['from_idtype'],
			'from_num' => ($oldnote['from_num']+$notevars['from_num'])
		);

		if($system) {
			$setarr['authorid'] = 0;
			$setarr['author'] = '';
		}
		$pkId = 0;
		if($oldnote['id']) {
			DB::update('home_notification', $setarr, array('id'=>$oldnote['id']));
			$pkId = $oldnote['id'];
		} else {
			$oldnote['new'] = 0;
			$pkId = DB::insert('home_notification', $setarr);
		}
		$banType = array('task');
		if($_G['setting']['cloud_status'] && !in_array($type, $banType)) {
			$noticeService = Cloud::loadClass('Service_Client_Notification');
			if($oldnote['id']) {
				$noticeService->update($touid, $pkId, $setarr['from_num'], $setarr['dateline']);
			} else {
				$extra = $type == 'post' ? array('pId' => $notevars['pid']) : array();
				$noticeService->add($touid, $pkId, $type, $setarr['authorid'], $setarr['author'], $setarr['from_id'], $setarr['from_idtype'], $setarr['note'], $setarr['from_num'], $setarr['dateline'], $extra);
			}
		}

		if(empty($oldnote['new'])) {
			DB::query("UPDATE ".DB::table('common_member')." SET newprompt=newprompt+1 WHERE uid='$touid'");

			require_once libfile('function/mail');
			$mail_subject = lang('notification', 'mail_to_user');
			sendmail_touser($touid, $mail_subject, $notestring, $frommyapp ? 'myapp' : $type);
		}

		if(!$system && $_G['uid'] && $touid != $_G['uid']) {
			DB::query("UPDATE ".DB::table('home_friend')." SET num=num+1 WHERE uid='$_G[uid]' AND fuid='$touid'");
		}
	}
?>