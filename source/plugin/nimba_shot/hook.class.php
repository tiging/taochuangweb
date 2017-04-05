<?php
/*
 * 主页：http://addon.discuz.com/?@ailab
 * 人工智能实验室：Discuz!应用中心十大优秀开发者！
 * 插件定制 联系QQ594941227
 * From www.ailab.cn
 */
 
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class plugin_nimba_shot {
	public $hotstyle='and order by a.views DESC';
	public $lname='';
	public $rname='';
	public $namecolor='#1078BB';
	public $bordercolor='#ccc';
	public $ulcolor='#000';
	public $bgcolor='#fff';
	public $forums=array();
	public $ban=array();
	public $days=0;
	public $other=4;
	public $rightwidth=275;
	
	function __construct(){
	    loadcache('plugin');
		global $_G;
		$this->vars=$_G['cache']['plugin']['nimba_shot'];
		if($this->vars['hotstyle']==1) $this->hotstyle='order by views DESC';
		if($this->vars['hotstyle']==2) $this->hotstyle='order by replies DESC';
		if($this->vars['hotstyle']==3) $this->hotstyle='order by lastpost DESC';
		if($this->vars['hotstyle']==4) $this->hotstyle='order by dateline DESC';
		if($this->vars['hotstyle']==5) $this->hotstyle='order by rand()';
		$this->lname=$this->vars['lname'];
		$this->rname=$this->vars['rname'];
		$this->namecolor=$this->vars['namecolor'];
		$this->bordercolor=$this->vars['bordercolor'];
		$this->ulcolor=$this->vars['ulcolor'];
		$this->bgcolor=$this->vars['bgcolor'];
		$this->forums=unserialize($this->vars['forums']);
		$this->ban=unserialize($this->vars['ban']);
		$this->days=empty($this->vars['days'])? (time()-2592000):(time()-intval($this->vars['days'])*86400);
		$this->other=empty($this->vars['other'])? 4:$this->vars['other'];
		$this->rightwidth=empty($this->vars['rightwidth'])? 275:$this->vars['rightwidth'];
		$this->cachetime=intval($this->vars['cachetime']);
	
	}
	
 	function userhot(){
		global $_G;
		$uid=empty($_G['forum_thread']['authorid'])? $_G['uid']: $_G['forum_thread']['authorid'];
		if($this->ban[0]=='') unset($this->ban[0]);
		$notin='';
		if(count($this->ban)){
			$notin='and fid not in('.implode(',',$this->ban).')';
		}
		$data=DB::fetch_all("SELECT tid,subject FROM ".DB::table('forum_thread')." where authorid='$uid' $notin and displayorder>=0 ".$this->hotstyle." LIMIT 0,6");
		return $data;
	}
	
 	function bbshot(){
		global $_G;
		$uid=empty($_G['forum_thread']['authorid']) ? $_G['uid']: $_G['forum_thread']['authorid'];
		if($this->ban[0]=='') unset($this->ban[0]);
		$notin='';
		if(count($this->ban)){
			$notin='and fid not in('.implode(',',$this->ban).')';
		}
		$data=DB::fetch_all("SELECT tid,subject FROM ".DB::table('forum_thread')." where authorid!='$uid' $notin and displayorder>=0 and dateline>'".$this->days."' ".$this->hotstyle." LIMIT 0,6");
		return $data;
	}
	
	function getpic(){
		global $_G;
		$uid=empty($_G['forum_thread']['authorid']) ? $_G['uid']: $_G['forum_thread']['authorid'];
		if($this->ban[0]=='') unset($this->ban[0]);
		$notin='';
		if(count($this->ban)){
			$notin='and a.fid not in('.implode(',',$this->ban).')';
		}
		$maps=array(
			1=>'order by a.views DESC',
			2=>'order by a.replies DESC',
			3=>'order by a.lastpost DESC',
			4=>'order by a.dateline DESC',
			5=>'order by rand()',
		);
		$this->hotstyle=$maps[$this->vars['hotstyle']];
		$value=DB::fetch_first("SELECT a.tid,a.subject,b.attachment as image from ".DB::table('forum_thread')." as a left join ".DB::table('forum_threadimage')." as b on a.tid=b.tid where a.attachment>0 and a.displayorder>=0 and b.attachment!='' $notin and a.displayorder>=0 ".$this->hotstyle."  LIMIT 0,1");
		return $value;
	}
	
 	function getother($num){//补齐方法
		global $_G;
		$uid=empty($_G['forum_thread']['authorid']) ? $_G['uid']: $_G['forum_thread']['authorid'];
		if($this->ban[0]=='') unset($this->ban[0]);
		$notin='';
		if(count($this->ban)){
			$notin='and fid not in('.implode(',',$this->ban).')';
		}
		if($this->other==1) $order='order by dateline DESC';
		elseif($this->other==2) $order='order by lastpost DESC';
		elseif($this->other==3) $order='order by views DESC';
		
		$data=DB::fetch_all("SELECT tid,subject FROM ".DB::table('forum_thread')." where authorid!=$uid $notin and displayorder>=0 $order LIMIT 0,$num");
		return $data;
	}
}

class plugin_nimba_shot_forum extends plugin_nimba_shot{
	function viewthread_modaction(){
		global $_G;	
		loadcache('plugin');
		if(!$_G['forum_thread']['author']) return '';
		if($this->cachetime){//开启了缓存
			$cachepath=DISCUZ_ROOT.'./data/sysdata/cache_nimba_shot_'.$_G['tid'].'.php';
			$lasttime=0;
			if(file_exists($cachepath)){
				@require_once $cachepath;
			}
			if(TIMESTAMP-$lasttime<$this->cachetime&&isset($cache_html)){//缓存未过期，直接返回
				return base64_decode($cache_html);
			}
		}
		
		
		$filepath=DISCUZ_ROOT.'./data/sysdata/cache_nimba_hoturl.php';
		if(file_exists($filepath)){
			@require_once $filepath;
		}
		$cache=0;
		if(count($hoturl)){
			$rr='';
			foreach($hoturl as $k=>$v){
				if($v['title']&&$v['url']){
					$rr.='<li style="line-height:25px; height:25px;width:'.$this->rightwidth.'px;overflow:hidden"><a href="'.$v['url'].'" target="_blank"><font style="color:'.$this->ulcolor.';">'.$v['title'].'</font></a><br></li>';
					$cache++;
				}
			}
		}
		if($_G['fid']&&in_array($_G['fid'],$this->forums)){
			$userhot=$this->userhot();
			if(count($userhot)<6&&$this->other!=4){
				$num=6-count($userhot);
				$otherhot=$this->getother($num);
				$userhot=array_merge($userhot,$otherhot);
			}
			$l='';
			foreach($userhot as $k=>$v){
				$l.='<li style="line-height:25px; height:25px; float:left; width:280px;overflow:hidden"><img src="static/image/common/arrow_right.gif" align="absmiddle" alt="heatlevel"/><a href="forum.php?mod=viewthread&tid='.$v['tid'].'" target="_blank"><font style="color:'.$this->ulcolor.';">'.$v['subject'].'</font></a><br></li>';
			}
			$bbshot=$this->bbshot();
			$r='';
			foreach($bbshot as $k=>$v){
				$cache++;
				if($cache>6) break;
				$r.='<li style="line-height:25px; height:25px;width:'.$this->rightwidth.'px;overflow:hidden"><a href="forum.php?mod=viewthread&tid='.$v['tid'].'" target="_blank"><font style="color:'.$this->ulcolor.';">'.$v['subject'].'</font></a><br></li>';
			}
			if($rr) $r=$rr.$r;
			if($hotpic['title']&&$hotpic['url']&&$hotpic['src']){
				$pichtml='<a href="'.$hotpic['url'].'" target="_blank"  style="display:block;color:#444;">
					<img style="width:156px; height:120px;display:block;padding:2px;border: 1px solid '.$this->bordercolor.';" src="'.$hotpic['src'].'" alt="'.$hotpic['title'].'" />
				</a>	
				<a  href="'.$hotpic['url'].'" title="'.$hotpic['title'].'" target="_blank" ><font style="color:'.$this->ulcolor.';">'.$hotpic['title'].'</font></a>';
			}else{
				$pic=$this->getpic();
				$pichtml='<a href="forum.php?mod=viewthread&tid='.$pic['tid'].'" target="_blank"  style="display:block;color:#444;">
					<img style="width:156px; height:120px;display:block;padding:2px;border: 1px solid '.$this->bordercolor.';" src="data/attachment/forum/'.$pic['image'].'" alt="'.$pic['subject'].'" />
				</a>	
				<a  href="forum.php?mod=viewthread&tid='.$pic['tid'].'" title="'.$pic['subject'].'" target="_blank" ><font style="color:'.$this->ulcolor.';">'.$pic['subject'].'</font></a>';
			}
			$cache_html= '<div style="background-color:'.$this->bgcolor.';border: 1px solid '.$this->bordercolor.'; height: 190px;margin: 10px 0;padding: 0px 0px 10px 10px;width:100%;">
		<div style="float:left; width:37%;margin-right:15px">
			<h2 style="font-size:14px; color:'.$this->namecolor.'; float:left; line-height:28px">'.$this->lname.'</h2>
			<ul style="float:left; ">
			'.$l.'
			</ul>
		</div>  
		<div style="float:left;width:60%;">
			<h2 style="font-size:14px; color:'.$this->namecolor.';line-height:28px">'.$this->rname.'</h2>
			<div style="float:left;text-align:center; width:164px;overflow:hidden;height:150px;line-height:26px;">
				'.$pichtml.'
			</div>
			<ul style="margin-left:12px;float:left;line-height:25px">
			'.$r.'
			</ul>
		</div>
	</div>'; 
			if($this->cachetime){
				$cachename='cache_html';
				@require_once libfile('function/cache');
				$cacheArray .= "\$$cachename='".base64_encode($cache_html)."';\n";
				$cacheArray .= "\$lasttime='".TIMESTAMP."';\n";
				writetocache('nimba_shot_'.$_G['tid'], $cacheArray);					
			}
			return $cache_html;
		}
	}
}

?>