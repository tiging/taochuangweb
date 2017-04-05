<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class plugin_iplus_gezi {
	function  __construct() {
		global $_G;
	    loadcache('plugin');
		$this->vars = $_G['cache']['plugin']['iplus_gezi'];
		$this->nav=$this->vars['nav'];
		$this->name=$this->vars['name'];
	}
	
	function global_header(){
		global $_G;
		loadcache('plugin');
		if($_G['cache']['plugin']['iplus_gezi']['local']==1&&$this->_pageplay()==1){
			unset($_G['setting']['output']['preg']['search']['plugin']);
			unset($_G['setting']['output']['preg']['replace']['plugin']);		
			return $this->_creat_gezi();
		}
	}
	function global_footer(){
		global $_G;
		loadcache('plugin');
		if($_G['cache']['plugin']['iplus_gezi']['local']==2&&$this->_pageplay()==1){
			unset($_G['setting']['output']['preg']['search']['plugin']);
			unset($_G['setting']['output']['preg']['replace']['plugin']);		
			return $this->_creat_gezi();
		}
	}	
	function _creat_gezi(){
		global $_G;
		loadcache('plugin');
		$vars=$_G['cache']['plugin']['iplus_gezi'];
		$maxnum=5*intval($vars['rows']);
		$wzlen=intval($vars['wzlen']);
		$wzcolor=$vars['wzcolor'];
		$adcolor=$vars['adcolor'];
		$cache=intval($vars['cache']);
		$styleid=intval($vars['styleid']);
		require_once DISCUZ_ROOT.'./source/discuz_version.php';
		$filepath=DISCUZ_ROOT.'./data/sysdata/cache_iplus_gezi.php';
		if(DISCUZ_VERSION=='X2') $filepath=DISCUZ_ROOT.'./data/cache/cache_iplus_gezi.php';	
		if(file_exists($filepath)) @include_once $filepath;
		if($_G['timestamp']-intval($lasttime)>$cache||$maxnum!=count($links)){//5分钟更新缓存 或者有增加行业更新
			$query = DB::query( "SELECT title,url,style FROM ".DB::table('iplus_gezi')." where lastdate>".$_G['timestamp']." ORDER BY id ASC LIMIT $maxnum");
			$links=array();
			$i=0;
			while($value=DB::fetch($query)){
				if($value){
					$style='style="';
					$value['title'] = dhtmlspecialchars(cutstr($value['title'],$wzlen,''));
					$fontarr=unserialize($value['style']);
					if($fontarr['fontcolor']) $style.='color:'.$fontarr['fontcolor'].';';
					else $style.='color:'.$adcolor.';';
					if($fontarr['fontweight']==1) $style.='font-weight: bold;';
					if($fontarr['fontstyle']==1) $style.='font-style: italic;';
					if($fontarr['textdecoration']==1) $style.='text-decoration: underline;';
					$style.='"';
					$value['style'] = $style;
					$links[]=$value;
					$i+=1;
				}
			}
			$default=$maxnum-$i;
			if($default){//默认补齐
				for($i=1;$i<=$default;$i++){
					$links[]=array('title'=>$vars['wztitle'],'url'=>'');
				}
			}
			@require_once libfile('function/cache');
			$cacheArray .= "\$links=".arrayeval($links).";\n\$lasttime=".$_G['timestamp'].";\n";
			writetocache('iplus_gezi', $cacheArray);			
		}
		include template('iplus_gezi:gz');
		return $return;
	}
	
	function _pageplay(){
		global $_G;		
		loadcache('plugin');
		$page=$_G['cache']['plugin']['iplus_gezi']['page'];
		if($page==1&&(CURSCRIPT=='portal'&&CURMODULE=='index')){//门户首页
			return 1;
		}
		if($page==2&&(CURSCRIPT=='forum'&&CURMODULE=='index')){//论坛首页
			return 1;
		}
		if($page==3&&(CURMODULE=='index')){//模块首页
			return 1;
		}
		if($page==4){//全站显示
			return 1;
		}
		return 0;
	
	}
	
	function global_cpnav_extra1() {
		loadcache('plugin');
		global $_G;
		if($this->nav==2) return '<a href="home.php?mod=spacecp&ac=plugin&id=iplus_gezi:adlist">'.$this->name.'</a>';
	}

	function global_nav_extra(){
		loadcache('plugin');
		global $_G;
		if($this->nav==3) return '<ul><li><a href="home.php?mod=spacecp&ac=plugin&id=iplus_gezi:adlist">'.$this->name.'</a></ul></li>';	
	}
	
	function global_footerlink(){
		loadcache('plugin');
		global $_G;
		if($this->nav==4) return '<span class="pipe">|</span><a href="home.php?mod=spacecp&ac=plugin&id=iplus_gezi:adlist">'.$this->name.'</a>';	
	}
	
	function global_usernav_extra2(){
		loadcache('plugin');
		global $_G;
		if($this->nav==5) return '<span class="pipe">|</span><a href="home.php?mod=spacecp&ac=plugin&id=iplus_gezi:adlist">'.$this->name.'</a>';		
	}	
}

?>