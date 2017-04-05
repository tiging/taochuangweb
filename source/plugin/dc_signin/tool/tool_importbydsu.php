<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class tool_importbydsu{
	var $title='&#23548;&#20837;[DSU]&#27599;&#26085;&#31614;&#21040;&#30340;&#25968;&#25454;';
	var $des='&#25226;[DSU]&#27599;&#26085;&#31614;&#21040;&#30340;&#25968;&#25454;&#23548;&#20837;&#21040;&#26412;&#25554;&#20214;&#20013;&#65292;&#26412;&#20250;&#26367;&#25442;&#24050;&#23384;&#22312;&#30340;&#29992;&#25143;&#20449;&#24687;';
	public function run(){
		global $page,$_G;
		$tablecheck=DB::result_first('show tables like \'%'.DB::table('dsu_paulsign').'%\'');
		if(!$tablecheck){
			cpmsg('&#23545;&#19981;&#36215;&#65292;&#26410;&#26816;&#32034;&#21040;[DSU]&#27599;&#26085;&#31614;&#21040;&#30340;&#25968;&#25454;&#34920;','action=plugins&operation=config&do='.$pluginid.'&identifier=dc_signin&pmod=tool','error');
		}
		$perpage = 1000;
		$start = ($page-1)*$perpage;
		$data=DB::fetch_all('SELECT * FROM '.DB::table('dsu_paulsign').' limit '.$start.','.$perpage);
		$count = count($data);
		$groups = C::t('#dc_signin#dc_signin_group')->getdata();
		$emot = C::t('#dc_signin#dc_signin_emot')->getdatabyxq();
		$reemot = array(
			'kx'=>1,
			'ng'=>2,
			'ym'=>4,
			'wl'=>3,
			'nu'=>10,
			'ch'=>5,
			'fd'=>8,
			'yl'=>7,
			'shuai'=>6,
		);
		foreach($data as $d){
			$sgid = 0;
			foreach($groups as $g){
				if($g['dayslower']<=$d['days']){
					$sgid=$g['id'];
				}else{
					break;
				}
				
			}
			$user = getuserbyuid($d['uid'],1);
			$uvd = array(
				'uid'=>$d['uid'],
				'dateline'=>$d['time'],
				'days'=>$d['days'],
				'monthdays'=>$d['mdays'],
				'monthcondays'=>0,
				'bcredit'=>$d['lastreward'],
				'credit'=>$d['reward'],
				'condays'=>$d['lasted'],
				'username'=>$user['username'],
				'sgid'=>$sgid,
				'emoticon'=>$emot[$reemot[$d['qdxq']]]['icon'],
				'xq'=>$emot[$reemot[$d['qdxq']]]['name'],
			);
			C::t('#dc_signin#dc_signin')->insert($uvd,false,true);
		}
		if($count==$perpage){
			cpmsg('&#27491;&#22312;&#36827;&#34892;[DSU]&#27599;&#26085;&#31614;&#21040;&#30340;&#25968;&#25454;&#23548;&#20837;'.(($page-1)*$perpage+$count),'action=plugins&operation=config&do='.$pluginid.'&identifier=dc_signin&pmod=tool&f=importbydsu&submit=yes&page='.($page+1).'&formhash='.FORMHASH,'loading');
		}
	}
	public function setting(){
		global $pluginid,$_G;
		$tablecheck=DB::result_first('show tables like \'%'.DB::table('dsu_paulsign').'%\'');
		if(!$tablecheck){
			cpmsg('&#23545;&#19981;&#36215;&#65292;&#26410;&#26816;&#32034;&#21040;[DSU]&#27599;&#26085;&#31614;&#21040;&#30340;&#25968;&#25454;&#34920;','action=plugins&operation=config&do='.$pluginid.'&identifier=dc_signin&pmod=tool','error');
		}
		cpmsg('&#26159;&#21542;&#36827;&#34892;[DSU]&#27599;&#26085;&#31614;&#21040;&#30340;&#25968;&#25454;&#23548;&#20837;','action=plugins&operation=config&do='.$pluginid.'&identifier=dc_signin&pmod=tool&f=importbydsu&submit=yes','form');
	}
}
?>