<?php
/**
 *		作者：rpan.cn
 *		版权所有：阿木 & rpancn
 *		QQ:399051063
 *		申明：此插件非开源软件，您不得对插件源代码进行任何形式任何目的的再发布。
 *		=========================================================================
 *			  承接discuz插件、模板仿制定制业务，价格便宜交期快QQ399051063
 *		=========================================================================
 */
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class plugin_rpancn_buy_usergroup
{
    function global_usernav_extra3()
    {
        //spacecp_credit_extra   global_usernav_extra3
        global $_G;
        if (!$_G['uid']) {
            return;
        }
        $config = $_G['cache']['plugin']['rpancn_buy_usergroup'];
        if ($config['usernav_on']) {
            $str = empty($config['linktitle'])?'':'<a  href="' . $config['linkurl'] . '" style="color:' . $config['linkcolor'] . '; "><strong>' . $config['linktitle'] . '</strong></a><span class="pipe">|</span>';
        }
        if ($config['headnav_on']) {
			//$orderquery = DB::query("SELECT * FROM " . DB::table('common_nav') . " where url like %%");
			//$order = DB::fetch($orderquery);
        }
        return $str;
    }
}



class plugin_rpancn_buy_usergroup_home extends plugin_rpancn_buy_usergroup
{
    function spacecp_usergroup_top_output()
    {
        global $_G;
		$usergroups = $_G[cache][usergroups];
        $var = $_G['cache']['plugin']['rpancn_buy_usergroup'];
        $ac  = $_GET['ac']; //action: SHOW,BUY
        if (!$_G['uid'] || $var['is_open'] == "0") {
            $str = '';
        } else {
            $str = '<span class="rpancn_buy_usergroup_gui" >	
			<div style="padding-bottom:20px;"><ul class="tb cl">	
			<li class="a"><a href="home.php?mod=spacecp&amp;ac=usergroup">' . lang('userdefined', 'title') . '</a></li>	
			</ul>
			<table cellspacing="0" cellpadding="0" class="dt mtm mbm">	
				<tbody class="th">	
					<tr>	
						<th>' . lang('plugin/rpancn_buy_usergroup', 'rpancn_plugin_usergroup') . '</th>	
						<th>'. lang('plugin/rpancn_buy_usergroup', 'rpancn_day_price') .'</th>	
						<th>'. lang('plugin/rpancn_buy_usergroup', 'rpancn_buy_mindays') .'</th>	
						<th></th>	
						<th>' . lang('plugin/rpancn_buy_usergroup', 'rpancn_do') . '</th>
					</tr>	
				</tbody>	
				<tbody>';
            
            
			$query = DB::query("SELECT * FROM ".DB::table('rpancn_buy_user_group_package')." ORDER BY sort_id");
			while($package_data = DB::fetch($query)) {
                $str .= '	
							<tr class="">	
								<td><a target="_blank" class="xi2" href="home.php?mod=spacecp&amp;ac=usergroup&amp;gid=' . $package_data[gid] . '">' . $usergroups[$package_data[gid]][grouptitle] . '</a></td>	
								<td>' . $package_data[pricepday] . ' ' . lang('plugin/rpancn_buy_usergroup', 'rpancn_yuan') .'/'.($package_data[minday]>=1?lang('plugin/rpancn_buy_usergroup', 'rpancn_day'):lang('plugin/rpancn_buy_usergroup', 'rpancn_forever')). '</td>	
								<td>' . $package_data[minday] . ' '.lang('plugin/rpancn_buy_usergroup', 'rpancn_day').'</td>	
								<td></td>	
								<td>	
								<button type="button" style="border: 1px solid #d2a000;box-shadow: 0 1px 2px #fedd71 inset,0 -1px 0 #a38b39 inset,0 -2px 3px #fedd71 inset;background: -webkit-linear-gradient(top,#fece34,#d8a605);background: -moz-linear-gradient(top,#fece34,#d8a605);width: 90px;line-height: 30px;text-align: center;font-weight: bold;color: #fff;text-shadow: 1px 1px 1px #333;border-radius: 5px;position: relative;overflow: hidden;" onclick="showWindow(\'group\', this.getAttribute(\'href\'), \'get\', 0);" href="plugin.php?id=rpancn_buy_usergroup:dobuy&ac=show&gid=' . $package_data[gid] . '">' . lang('plugin/rpancn_buy_usergroup', 'rpancn_buy') . '</button>	
								</td>	
							</tr>';
            }
            $str .= '	
				</tbody>	
			</table>	
			</div>	
			</span>';
			
			/**
 *		作者：taochuangweb
 *		加入自定义内容显示
 *		QQ:149779331
 */			
			$str .= '<div class="bm_h cl">
					<h2>
					<a><font color="red">'.lang('userdefined', 'explain00').'</font></a>
					</h2>
					</div>';
					
			$str .= '<div class="bm_c txtstyle">
					<table width="100%" class="notestyle"><tbody><tr>
					<td>'.lang('userdefined', 'explain01').'</td>
					</tr>
					<tr>
					<td>'.lang('userdefined', 'explain02').'</td>
					</tr>
					<tr>
					<td>'.lang('userdefined', 'explain03').'</td>
					</tr>
					<tr>
					<td>'.lang('userdefined', 'explain04').'</td>
					</tr>
					<tr>
					<td>'.lang('userdefined', 'explain05').'</td>
					</tr>
					<tr>
					<td>'.lang('userdefined', 'explain06').'</td>
					</tr>
					<tr>
					<td><font color="red">'.lang('userdefined', 'explain07').'</font></td>
					</tr>
					</tbody></table>
					</div>';
        }
        return $str;
    }
}
?>