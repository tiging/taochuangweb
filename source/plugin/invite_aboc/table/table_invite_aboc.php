<?php
/**
 * table_invite_aboc.php
 * User: aboc
 * Date: 14-9-2
 * Time: 下午6:54
 */

class table_invite_aboc extends discuz_table {

    public $set = array();

    function __construct() {
        $this->_table = 'invite_aboc';
        $this->_pk    = 'uid';
        parent::__construct();
        global $_G;
        $this->set = $_G['cache']['plugin']['invite_aboc'];
        $this->set['ban_group_id'] = @unserialize($this->set['ban_group_id']);
        $this->set['number'] = 20;
        $this->set['day'] = 0;
    }


    function update_fromuid($uid, $username, $fromuid) {
        global $_G;
//        print_r($this->set);
        $frominfo = C::t("common_member")->fetch($fromuid);
        if(!$frominfo){
            return;
        }
        if($this->set['ban_group_id'] && in_array($frominfo['groupid'],$this->set['ban_group_id'])){
            return;
        }
        if (DB::fetch_first("SELECT * FROM %t WHERE addip=%s LIMIT 1", array($this->_table, $_G['clientip']))) {
            $status = 0;
        } else {
            $status = 1;
        }
        if (!DB::fetch_first("SELECT * FROM %t WHERE uid='%d' AND fromuid='%d' LIMIT 1", array($this->_table, $uid, $fromuid))) {
            DB::insert($this->_table, array(
                'uid'      => $uid,
                'username' => $username,
                'fromuid'  => $fromuid,
                'addtime'  => $_G['timestamp'],
                'addip'    => $_G['clientip'],
                'status'   => $status,
            ));
            if($status){
                //获取数量
                if($this->set['number']<=0){
                    return;
                }
                if(!$this->set['upgrade_group_id']){
                    return;
                }
                if($fromuid['groupid']!=$this->set['upgrade_group_id']){
                    //设置到期后的用户组为common_member_field_forum字段groupterms
                    //当前用户组等于预升级的用户组,则不做任何动作
                    $num = $this->get_fromuid_num($fromuid);
                    if($num > $this->set['number']){
                        //可以升级
                        $member_field = C::t("common_member_field_forum")->fetch($fromuid);
                        $groupterms = @unserialize($member_field['groupterms']);
                        $groupexpiry = 0;
//                        var_dump($this->set['day']);exit;
//                        echo "uid='$fromuid'";
                        //更改用户组
                        DB::update("common_member",array(
                            'groupid'=>$this->set['upgrade_group_id'],
                            'groupexpiry'=>$groupexpiry,
                        ),"uid='$fromuid'");
                    }
                }

            }
        }
    }

    function get_fromuid_num($fromuid){
        $row = DB::fetch_first("SELECT COUNT(uid) AS num FROM %t WHERE fromuid='%d' AND status=1",array($this->_table,$fromuid));
        return $row['num'];
    }

} 