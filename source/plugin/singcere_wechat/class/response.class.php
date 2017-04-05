<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id$
 */

if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class WxMpResponse{
	private static $expire = 1296000;
	private static $systemcmd;
	private static $customcmd;
	private static $extensioncmd;
	private static $cmdlist;
	
	function __construct() {
		global $_G;
		
		if(!$_G['singcere_wechat']) {
		    $_G['singcere_wechat']['setting'] = unserialize($_G['setting']['singcere_wechat']);
		    $_G['singcere_wechat']['setting']['wechat_appsecret'] = authcode($_G['singcere_wechat']['setting']['wechat_appsecret'], 'DECODE', $_G['config']['security']['authkey']);
		}
		
		$cmdlist = C::t('#singcere_wechat#singcere_wechat_cmd')->fetch_all_by_status(0, '<>');
		foreach($cmdlist as &$cmd) {
			$cmd['alias'] = empty($cmd['alias']) ? $cmd['cmdname'] : $cmd['alias'];
			if($cmd['type'] == 'system') {
				self::$systemcmd[$cmd['cmdname']] = $cmd;
			} else if($cmd['type'] == 'extension') {
				self::$extensioncmd[$cmd['cmdname']] = $cmd;
			}
		}
		$this->_cmdsort($cmdlist);
		self::$cmdlist = $cmdlist;
	}
	
	function receiveallstart($param) {
	    global $_G;
	    
	    list($data) = $param;
	    
	    if(!is_writable(session_save_path())) {
	        if(!is_dir(DISCUZ_ROOT.'data/tmp')) mkdir(DISCUZ_ROOT.'data/tmp', 0700);
	        session_save_path(DISCUZ_ROOT.'data/tmp');
	    }
	    session_id(md5($data['from'].$_G['config']['security']['authkey']));
	    if(!session_start()) {
	        echo WeChatServer::getXml4Txt('session initiate failed');
	        exit;
	    }
	}
	
	function receiveallend($param) {
	    
	}
	
	function subscribe($param) {
		list($data) = $param;
		global $_G;

		if($data['key']) {	// 未关注, 扫描
			$data['content'] = $data['key'];
			if(is_numeric($data['key']) && strlen($data['key']) == 6) {
				$this->_sendnum($data);
			} else {
				$this->_query($data);
			}
			exit;
		} else {
		    $data['content'] = 'subscribe';
		}
		
        
        $bindmember = C::t('#singcere_wechat#singcere_wechat_bind')->fetch_by_openid($data['from']);
        if($bindmember) {
        	C::t('#singcere_wechat#singcere_wechat_bind')->update($bindmember['id'], array(
        		'subscribe' => 1,
        	));
        }
        $this->_query($data);
	}
	
	function scan($param) {
		list($data) = $param;
        if($data['key']) {	// 已关注, 扫描
			$data['content'] = $data['key'];
			if(is_numeric($data['key']) && strlen($data['key']) == 6) {
				$this->_sendnum($data);
			} else {
				$this->_query($data);
			}
			exit;
		}
	}

	function unsubscribe($param) {
		list($data) = $param;
		$bindmember = C::t('#singcere_wechat#singcere_wechat_bind')->fetch_by_openid($data['from']);
		if($bindmember) {
			C::t('#singcere_wechat#singcere_wechat_bind')->update($bindmember['id'], array(
				'subscribe' => 0,
			));
		}
	}

	function image($param) {
	    global $_G;
	    list($data) = $param;
        if($_SESSION['wx']['fact_receive']) {
            $factlang = array();
            foreach(explode("\n", $_G['singcere_wechat']['setting']['fact_lang']) as $langsetting) {
                list($langkey, $langval) = explode('=', $langsetting);
                $langkey && $langval && $factlang[$langkey] = trim($langval);
            }
            
            $fact = C::t('#singcere_wechat#singcere_wechat_fact')->fetch_unused_by_openid($data['from']);
            $fact['attachment'] = unserialize($fact['attachment']);
            $fact['attachment'][$data['mid']] = $data['url'];
            C::t('#singcere_wechat#singcere_wechat_fact')->update_unused_by_openid($data['from'], array('attachment' => serialize($fact['attachment'])));
            echo WeChatServer::getXml4Txt($factlang['fact_receive_continue'] ? $factlang['fact_receive_continue'] : lang('plugin/singcere_wechat', 'fact_receive_continue'));
            exit;
        }
	}
	
	function location($param) {
	    list($data) = $param;
	    if($data['type'] == 'event') {  // 上报分支
	        $json = WeChatClient::get("http://apis.map.qq.com/ws/coord/v1/translate?locations={$data[la]},{$data[lo]}&type=1&key=EC5BZ-XRV3G-5PZQD-IXEXW-CGA56-NHFXI");
	        $json = json_decode($json, true);
	        if($json['status'] == 0) {
	            C::t('#singcere_wechat#singcere_wechat_bind')->update_by_openid($data['from'], array('lat' => $json['locations'][0]['lat'], 'lng' => $json['locations'][0]['lng']));
	        }
	    }
	    exit("");
	}
	
	function click($param) {
	    global $_G;
	    
	    $param[0]['content'] = $param[0]['key'];
		$this->text($param);
	}
	
	function templatesendjobfinish($param) {
	    list($data) = $param;
	    if($data['msg_id']) {
	        C::t('#singcere_wechat#singcere_wechat_tmplmsg')->update($data['msg_id'], array('status' => $data['status']));
	    }
	}
	
	function text($param) {
		global $_G;
	    
		list($data) = $param;
		$data['content'] = diconv($data['content'], 'UTF-8');
	
		if(is_numeric($data['content']) && strlen($data['content']) == 6) {     // 登录/绑定分支
		    unset($_SESSION['wx']);
			$this->_sendnum($data);
		} else if($_G['singcere_wechat']['setting']['dkf_allowservice'] && (($data['type'] == 'text' && trim($_G['singcere_wechat']['setting']['dkf_keyword']) === '*') || in_array($data['content'], explode(',', $_G['singcere_wechat']['setting']['dkf_keyword'])))) {    // ��ͷ���֧
		    unset($_SESSION['wx']);
		    $this->_transferCustomerService($data);
		} else if(in_array($data['content'], explode(',', $_G['singcere_wechat']['setting']['fact_keyword'])) || $_SESSION['wx']['fact_receive']) {     // 爆料分支
		    $factlang = array();
		    foreach(explode("\n", $_G['singcere_wechat']['setting']['fact_lang']) as $langsetting) {
		        list($langkey, $langval) = explode('=', $langsetting);
		        $langkey && $langval && $factlang[$langkey] = trim($langval);
		    }
		    
			$data['content'] = trim(strtolower($data['content']));
			
			
			if($_SESSION['wx']['fact_receive'] && $data['content'] != 'ok') {
			    unset($_SESSION['wx']);
				echo WeChatServer::getXml4Txt($factlang['fact_receive_error'] ? $factlang['fact_receive_error'] : lang('plugin/singcere_wechat', 'fact_receive_error'));
				exit;
			}
	
			if($data['content'] == 'ok' && $_SESSION['wx']['fact_receive']) {
				unset($_SESSION['wx']);
				$fact = C::t('#singcere_wechat#singcere_wechat_fact')->fetch_unused_by_openid($data['from']);
				echo WeChatServer::getXml4Txt($factlang['fact_receive_end'] ? "<a href=\"$_G[siteurl]plugin.php?id=singcere_wechat:fact&factid=$fact[factid]\">$factlang[fact_receive_end]</a>" : lang('plugin/singcere_wechat', 'fact_receive_end', array('url' => $_G['siteurl'].'plugin.php?id=singcere_wechat:fact&factid='.$fact['factid'])));
				exit;
			} else {
				C::t('#singcere_wechat#singcere_wechat_fact')->delete_unused_by_openid($data['from']);
				C::t('#singcere_wechat#singcere_wechat_fact')->insert(array('openid' => $data['from'], 'status' => 0,'dateline' => TIMESTAMP));
				$_SESSION['wx']['fact_receive'] = true;
				echo WeChatServer::getXml4Txt($factlang['fact_receive_welcome'] ? $factlang['fact_receive_welcome'] : lang('plugin/singcere_wechat', 'fact_receive_welcome'));
				exit;
			}
		} else {
			$this->_query($data);
		}
	}
	
	function _sendnum($data) {
	    global $_G;
	    
	    $authcode = C::t('#singcere_wechat#singcere_wechat_authcode')->fetch_by_code($data['content']);
	    if($authcode && !$authcode['status']) {
	    	require_once libfile('function/member');
	        require_once DISCUZ_ROOT . './source/plugin/singcere_wechat/singcere_wechat.class.php';
	        $wechat_client = new WeChatClient($_G['singcere_wechat']['setting']['wechat_appId'], $_G['singcere_wechat']['setting']['wechat_appsecret']);
	        
	        $bindmember = C::t('#singcere_wechat#singcere_wechat_bind')->fetch_by_openid($data['from']);
			if(empty($bindmember)) {
				$info = $wechat_client->getUserInfoById($data['from']);
				if($info['unionid']) {
					$bindmember = C::t('#singcere_wechat#singcere_wechat_bind')->fetch_by_unionid($info['unionid']);
				}
			}

	        if(!$authcode['uid'] && $bindmember) {     // 登录微信已绑定的账号
	            C::t('#singcere_wechat#singcere_wechat_authcode')->update($authcode['sid'], array('uid' => $bindmember['uid'], 'status' => 1));
	        } else if(!$authcode['uid'] && empty($bindmember)) {       // 注册新账号
				// 此处用户已关注, 直接使用info接口
	            $regname = SC_WeChat::getnewname($info['nickname']);

	            $uid = SC_WeChat::register($regname, 1, 0, $info['sex']);
	            
	            if($uid) {
	                SC_WeChat::syncAvatar($uid, $info['headimgurl']);
	                C::t('#singcere_wechat#singcere_wechat_bind')->insert(array(
    	                'openid' => $info['openid'],
    	                'uid' => $uid,
    	                'username' => $regname,
    	                'nickname' => dhtmlspecialchars(diconv($info['nickname'], 'utf-8', CHARSET)),
    	                'sex' => intval($info['sex']),
    	                'dateline' => TIMESTAMP,
    	                'unionid' => $info['unionid'],
    	                'lastauth' => TIMESTAMP,
    	                'counts' => 1,
    	                'subscribe' => $info['subscribe'],
    	                'isregister' => 1,
	                ));
	                C::t('#singcere_wechat#singcere_wechat_authcode')->update($authcode['sid'], array('uid' => $uid, 'status' => 1));
	                
	                if($_G['singcere_wechat']['setting']['discuz_credit'] && $_G['singcere_wechat']['setting']['discuz_regreward']) {
	                    updatemembercount($_G['uid'], array('extcredits'.$_G['singcere_wechat']['setting']['discuz_credit'] => $_G['singcere_wechat']['setting']['discuz_regreward']));
	                }
	            } else {
	                echo WeChatServer::getXml4Txt(lang('plugin/singcere_wechat', 'qrcode_register_failed'));
	            }
	        } else if($authcode['uid']) {
	            $member = getuserbyuid($authcode['uid'], 1);
	            $current_bind_member = C::t('#singcere_wechat#singcere_wechat_bind')->fetch_by_uid($authcode['uid']);
	            if($bindmember && $bindmember['uid'] != $authcode['uid']) {    // 绑定账号不一致
	                C::t('#singcere_wechat#singcere_wechat_bind')->delete($current_bind_member['id']);
	                C::t('#singcere_wechat#singcere_wechat_bind')->update($bindmember['id'], array('uid' => $member['uid'], 'username' => $member['username'], 'isregister' => $current_bind_member['isregister']), true);
	            } else if(empty($bindmember)) {
	                if(empty($current_bind_member)) {
                        C::t('#singcere_wechat#singcere_wechat_bind')->insert(array(
    	                    'openid' => $info['openid'],
    	                    'uid' => $member['uid'],
    	                    'username' => $member['username'],
    	                    'nickname' => dhtmlspecialchars(diconv($info['nickname'], 'utf-8', CHARSET)),
    	                    'sex' => intval($info['sex']),
    	                    'dateline' => TIMESTAMP,
    	                    'unionid' => $info['unionid'],
    	                    'lastauth' => TIMESTAMP,
    	                    'counts' => 1,
    	                    'subscribe' => $info['subscribe'])
                        );
	                } else {
	                    C::t('#singcere_wechat#singcere_wechat_bind')->update($current_bind_member['id'], array(
	                       'openid' => $info['openid'], 
	                       'unionid' => $info['unionid'], 
	                       'nickname' => dhtmlspecialchars(diconv($info['nickname'], 'utf-8', CHARSET)),
	                       'sex' => intval($info['sex']),
	                       'subscribe' => $info['subscribe'])
	                    );
	                }
	            }
	            C::t('#singcere_wechat#singcere_wechat_authcode')->update($authcode['sid'], array('status' => 1));
	        }
	        
	        $openid = $info ? $info['openid'] : $bindmember['openid'];
	        $username = !$authcode['uid'] && $bindmember ? $bindmember['username'] : ($authcode['uid'] ? $member['username'] : $regname);
	        
	        if(function_exists('sc_wechat_notification')) {
	            echo WeChatServer::getXml4Txt(lang('plugin/singcere_wechat', 'qrcode_success_text'));
	        }

	        $return = sc_wechat_notification('login', array(
	            'to' => 'openid:'.$openid,
	            'url' => $_G['siteurl'].'home.php?mod=space&do=profile&authorization=1',
	            'color' => '#9AD222',
	            'data' => array(
	            	'first' => array('value' => lang('plugin/singcere_wechat', 'qrcode_success_tmpl_first'), 'color' => '#000000'),
	                'keyword1' => array('value' => $username, 'color' => '#9AD222'),
	                'keyword2' => array('value' => dgmdate(TIMESTAMP, 'Y-m-d H:i'), 'color' => '#9AD222'),
	                'remark' => array('value' => lang('plugin/singcere_wechat', 'qrcode_success_tmpl_remark'), 'color' => '#000000'), 
	            )
	        ));
	        
	        if(!$return) {
	            echo WeChatServer::getXml4Txt(lang('plugin/singcere_wechat', 'qrcode_success_text'));
	        }

	    }
	}
	
	function _transferCustomerService($data) {
	    global $_G;
	    
	    $inworktime = true;
	    if($_G['singcere_wechat']['setting']['dkf_worktimelimit']) {
	        $inworktime = false;
	        $todayts = strtotime(dgmdate(TIMESTAMP, 'Ymd'));
	        foreach($_G['singcere_wechat']['setting']['dkf_worktime'] as $time) {
	            if(TIMESTAMP > $todayts + $time && TIMESTAMP < $todayts + $time + 1800) {
	                $inworktime = true;
	                break;
	            }
	        }
	    }

		// 工作时间优先, 非工作时间直接拒绝
	    if(!$_G['singcere_wechat']['setting']['dkf_priority'] && !$inworktime) {
	        echo WeChatServer::getXml4Txt($_G['singcere_wechat']['setting']['dkf_notworktime']);
	        exit;
	    }
	    
	    $wechat_client = new WeChatClient($_G['singcere_wechat']['setting']['wechat_appId'], $_G['singcere_wechat']['setting']['wechat_appsecret']);
	    $info = $wechat_client->getCustomerServiceList();
	    
	    if(count($info['kf_online_list']) == 0) {
	        echo WeChatServer::getXml4Txt(!$inworktime ? $_G['singcere_wechat']['setting']['dkf_notworktime'] : $_G['singcere_wechat']['setting']['dkf_offline']);
	        exit;
	    }
	    
	    echo WeChatServer::getXml4CustomerService();
	    $wechat_client = new WeChatClient($_G['singcere_wechat']['setting']['wechat_appId'], $_G['singcere_wechat']['setting']['wechat_appsecret']);
	    $wechat_client->sendTextMsg($data['from'],  $_G['singcere_wechat']['setting']['dkf_greet']);
	}
	
	function _query($data) {
	    global $_G;

		$key = preg_replace("/ +/"," ", $data['content']);
		list($cmdname, $actionstr) = explode(' ', $key, 2);
		$match = '';

		// full match
		foreach(self::$cmdlist as $cmd) {
			if($cmd['type'] == 'custom' && in_array($cmdname, explode(',', $cmd['alias']))) {
				$match = $cmd;
				unset($_SESSION['wx']);
				break;
			}
		}
        
		if($match) {
		    if($match['responsetype'] == 1) {
		        echo WeChatServer::getXml4Txt($match['cmdrtn']);
		    } else {
		        foreach(C::t('#singcere_wechat#singcere_wechat_richresponse')->fetch_all_by_cmdid($match['id']) as $id => $richitem) {
		            $list[] = array(
		                'title' => $richitem['title'],
		                'desc' => $richitem['description'],
		                'pic' => $richitem['imgurl'],
		                'url' => $richitem['link'],
		            );
		        }
		        echo WeChatServer::getXml4RichMsgByArray($list);
		    }
		    
		    exit;
		} 

		// session match
		if(isset($_SESSION['wx']['extend'])) {
            @include_once DISCUZ_ROOT.'./source/plugin/'.$_SESSION['wx']['extend'].'/wechat_module.class.php';
            if(class_exists('wechat_module')) {
                $extmodule = new wechat_module($data);
		        $extmodule->doResponse();
            } else {
                echo WeChatServer::getXml4Txt('No found the module in '.$_SESSION['wx']['extend']);
                unset($_SESSION['wx']);
            }
            exit;
		}

		// extension match
		foreach(self::$cmdlist as $cmd) {
		    if($cmd['type'] != 'extension') continue;
		    if(preg_match($cmd['pattern'], $key)) {
		        @include_once DISCUZ_ROOT.'./source/plugin/'.$cmd['cmdname'].'/wechat_module.class.php';
		        if(class_exists('wechat_module')) {
		            $extmodule = new wechat_module($data);
		            $extmodule->doResponse();
		        } else {
		            echo WeChatServer::getXml4Txt('No found module in '.$cmd['cmdname']);
		        }
		        exit;
		    }
		}
		unset($_SESSION['wx']);
	}
	
	function _push($cmd, $actionstr) {
		global $wechat;
		switch($cmd['type']) {
			case 'system':
				return $this->{$cmd['cmdname']}($actionstr);
			case 'extension':
				require_once 'source/plugin/singcere_wxpublic/extension/extension_'.$cmd['cmdname'].'.php';
				$classname = 'extension_'.$cmd['cmdname'];
				$ext = new $classname($wechat, $this->postobj, $actionstr);
				$ext->doResponse();
		
			case 'plugin':
		
			case 'custom':
				if($cmd['responsetype'] == 1) {
					return $this->output($cmd['cmdrtn']);
				} else {
					$responselist = C::t('#singcere_wxpublic#singcere_wxpublic_richresponse')->fetch_all_by_cmdid($cmd['id']);
					return $this->output($responselist, 2);
				}
		}
	}
	
	function _custom($type, $keyword = '') { 
		global $_G;
		loadcache('wechat_response');
		$response = & $_G['cache']['wechat_response'];
		$query = $type == 'text' ? $response['query']['text'][$keyword] : $response['query']['subscribe'];
		if($query) {
			if(preg_match("/^\[resource=(\d+)\]/", $query, $r)) {
				$resource = C::t('#wechat#mobile_wechat_resource')->fetch($r[1]);
				if(!$resource['type']) {
					$list = array(array(
							'title' => $resource['data']['title'],
							'desc' => $resource['data']['desc'],
							'pic' => $resource['data']['pic'],
							'url' => $resource['data']['url'],
					));
				} else {
					$mergeids = array_keys($resource['data']['mergeids']);
					$sresource = C::t('#wechat#mobile_wechat_resource')->fetch_all($mergeids);
					$list = array();
					foreach($resource['data']['mergeids'] as $id => $order) {
						$list[] = array(
								'title' => $sresource[$id]['data']['title'],
								'desc' => $sresource[$id]['data']['desc'],
								'pic' => $sresource[$id]['data']['pic'],
								'url' => $sresource[$id]['data']['url'],
						);
					}
				}
				echo WeChatServer::getXml4RichMsgByArray($list);
				exit;
			} else {
				echo WeChatServer::getXml4Txt($query);
			}
			exit;
		}
		return 0;
	}
	
	function _cmdsort(&$cmdlist) {
		$orderarray = $typearray = array();
		$type = array('system' => 1, 'extension' => 2, 'custom' => 3);
		foreach($cmdlist as $key => $cmd) {
			$orderarray[$key] = $cmd['displayorder'];
			$typearray[$key] = $type[$cmd['type']];
		}
		array_multisort($typearray, SORT_ASC, $orderarray, SORT_DESC, $cmdlist);
	}
	
}