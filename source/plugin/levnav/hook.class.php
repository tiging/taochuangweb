<?php

/**
 * Lev.levme.com [ 专业开发各种Discuz!插件 ]
 *
 * Copyright (c) 2013-2014 http://www.levme.com All rights reserved.
 *
 * Author: Mr.Lee <675049572@qq.com>
 *
 * Date: 2013-02-17 16:22:17 Mr.Lee $
 */


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class plugin_levnav_forum extends plugin_levnav {
}

class plugin_levnav {

	public static $PL_G, $_G, $PLNAME, $PLSTATIC, $PLURL, $lang = array(), $table, $navtitle, $uploadurl, $remote, $talk;

	public function __construct() {
		self::_init();
		self::$lang = self::_levlang();
	}

	public static function global_nav_extra() {
		$plstatic = self::$PLSTATIC;
		$mynavs = explode("\n", self::$PL_G['mynavs']);
		foreach ($mynavs as $r) {
			$_mynav = explode('==', $r);
			$_navarr[] = array('name'=> trim($_mynav[0]), 'link'=>trim($_mynav[1]));
		}
		$myicons = explode("\n", self::$PL_G['navicon']);
		foreach ($myicons as $r) { $_k++;
			$navbg .= '#levnav .nav_icon_'.$_k.' {background: url("'.$plstatic.'img/'.trim($r).'") no-repeat;}';
		}
		$icon = $_icon = 1;
		$navs = '<ul class="nav_icon_'.$icon.'">';
		foreach ($_navarr as $k => $v) {
			$_width = '';
			if ($k && $k%6 ==0) {
				$_icon++;
				$icon++;
				if ($_icon % 4 ==0) $_width = 'style="width:130px;"';
				if (!trim($myicons[$icon - 1])) $icon = 1;
				$navs .= '</ul><ul class="nav_icon_'.$icon.'" '.$_width.'>';
			}
			$navs .= '<li><a target="_blank" href="'.$v['link'].'">'.$v['name'].'</a></li>';
		}
		if (!self::_isopen('heightauto')) $height = 'height: 57px;'; 
		$navs .= '</ul>';
		$html = <<<EOF
		</div><div id="levnav">
		<style>
		#levnav {
			background: url("{$plstatic}img/navbg.jpg") repeat-x #F7F9FB;
		    border-color: #E2EAF1 #E3E3E3 #E3E3E3;
		    border-image: none;
		    border-right: 1px solid #E3E3E3;
		    border-style: solid;
		    border-width: 0 1px 1px;
		    {$height}
		    overflow: hidden;
		    padding-left: 10px;
		    margin: 0 atuo;
		}
		#levnav ul {float: left;height: 50px;overflow: hidden;padding-left: 50px;padding-top: 6px;width: 205px;}
		#levnav ul li {float: left;height: 22px;line-height: 22px;overflow: hidden;width: 65px;}
		#levnav ul li a {color: #666;}
		#levnav ul li a:hover {color: #CC0000;}
		$navbg
		</style>
		$navs
EOF;
		return $html;
	}
	
	public static function _init() {

		global $_G;
		self::$_G     = $_G;
		self::$PLNAME = 'levnav';
		self::$PL_G   = self::$_G['cache']['plugin'][self::$PLNAME];//print_r($PL_G);

		self::$PLSTATIC = 'source/plugin/'.self::$PLNAME.'/statics/';
		self::$PLURL    = 'plugin.php?id='.self::$PLNAME;
		self::$uploadurl= self::$PLSTATIC.'upload/common/';
		self::$remote   = 'plugin.php?id='.self::$PLNAME.':l&fh='.FORMHASH.'&m=';
	}

	public static function _levlang($string = '', $key = 0) {
		$sets  = $string ? $string : (!$key ? self::$PL_G['levlang'] : '');
		$lang  = array();
		if ($sets) {
			$array = explode("\n", $sets);
			foreach ($array as $r) {
				$thisr  = explode('=', trim($r));
				$lang[trim($thisr[0])] = trim($thisr[1]);
			}
			if (!$key) {
				$lang['extscore'] = self::$_G['setting']['extcredits'][self::$PL_G['scoretype']]['title'];
				$flang = lang('plugin/levnav');
				if (is_array($flang)) $lang = $lang + $flang;
			}
		}
		return $lang;
	}

	public static function _levdiconv($string, $in_charset = 'utf-8', $out_charset = CHARSET) {
		if(is_array($string)) {
			foreach($string as $key => $val) {
				$string[$key] = diconv($val, $in_charset, $out_charset);
			}
		} else {
			$string = diconv($string, $in_charset, $out_charset);
		}
		return $string;
	}
	
	public static function _isopen($key = 'close') {
		$isopen = unserialize(self::$PL_G['isopen']);
		if (is_array($isopen) && in_array($key, $isopen)) return TRUE;
	}
	
	public static function _loadextjs($jquery = 0) {
		global $_G;
		$js = '';
		if ($jquery && self::$_G['loadjquery'] !=1) {
			$_G['loadjquery'] = 1;
			$js .= '<script language="javascript" type="text/javascript" src="'.self::$PLSTATIC.'jquery.min.js"></script>
					 <script language="javascript" type="text/javascript">var $$ = jQuery.noConflict();</script>';
		}
		if (self::$_G['loadartjs'] !=1) {
			$_G['loadartjs'] = 1;
			$js .= '<script type="text/javascript" src="'.self::$PLSTATIC.'dialog417/dialog.js?skin=default"></script>
				  	<script type="text/javascript" src="'.self::$PLSTATIC.'dialog417/plugins/iframeTools.js"></script>';
		}
		return $js;
	}
	
}








