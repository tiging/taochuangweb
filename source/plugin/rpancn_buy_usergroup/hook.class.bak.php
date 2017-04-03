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
			<li class="a"><a href="home.php?mod=spacecp&amp;ac=usergroup">' . lang('plugin/rpancn_buy_usergroup', 'rpancn_plugin_title') . '</a></li>	
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
								<a onclick="showWindow(\'group\', this.href, \'get\', 0);" class="xw1 xi2" href="plugin.php?id=rpancn_buy_usergroup:dobuy&ac=show&gid=' . $package_data[gid] . '">' . lang('plugin/rpancn_buy_usergroup', 'rpancn_buy') . '</a>	
								</td>	
							</tr>';
            }
            $str .= '	
				</tbody>	
			</table>	
			</div>	
			</span>';
        }
        return $str;
    }
}
?>