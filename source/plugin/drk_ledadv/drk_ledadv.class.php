<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
//error_reporting(E_ALL);
if(!defined('IN_DISCUZ')){
    exit('Aecsse Denied');
}

class plugin_drk_ledadv{
  var $forumss    = 0;
  var $position   = 'left';
  var $direction  = '3';
  var $speed      = 30;
  var $textcolor1 = '#FFFF00';
  var $textcolor2 = '#FF5300';
  var $fontsize   = 30;
  var $spacing    = 8;
  var $ledheight  = 52;
  var $ledstyle   = 'solid';
  var $ledcolor   = '#000';
  var $clockwidth = 170;
  var $messagelen = 1.5;
  var $texts      = array();
  function plugin_drk_ledadv(){
    global $_G;
    if(!isset($_G['cache']['plugin']['drk_ledadv'])){
      loadcache('drk_ledadv');
    }
    $this->var        = $_G['cache']['plugin']['drk_ledadv'];
    $this->isopen     = $this->var['isopen']; // 是否启用
    $this->selecttype = $this->var['selecttype']; // 显示主题广告语
    $this->isforum    = ($this->selecttype == 1 || $this->selecttype == 3) ? 1 : 0; // 是否显示主题
    $this->ismyadv    = ($this->selecttype == 2 || $this->selecttype == 3) ? 1 : 0; // 是否显示广告语
    $this->forums     = $this->var['forums']; // 版块选择
    $this->threadnum  = $this->var['threadnum'];
    $this->threadtype = $this->var['threadtype'];
    $this->myadv      = $this->var['myadv'];
    $this->showclock  = $this->var['showclock'];
    $this->clocks     = $this->var['clocks'];
    $this->clockwidth = $this->var['clockwidth'] ? $this->var['clockwidth'] : $this->clockwidth;
    $this->direction  = $this->var['direction'] ? $this->var['direction'] : $this->direction; // 滚动方向
    $this->forumss    = $this->var['forumss'] ? $this->var['forumss'] : $this->forumss;
    $this->position   = $this->var['position'] ? $this->var['position'] : $this->position;
    $this->speed      = $this->var['speed'] ? $this->var['speed'] : $this->speed;
    $this->isall      = $this->var['isall'];
    $this->isportal   = $this->var['isportal'];
    $this->islist     = $this->var['islist'];
    $this->isfup      = $this->var['isfup'];
    $this->isweater   = $this->var['isweater'];
    $this->ledheight  = $this->var['ledheight'] ? $this->var['ledheight'] : $this->ledheight;
    $this->ledwidth   = $this->var['ledwidth'];
    $this->ledstyle   = $this->var['ledstyle'] ? $this->var['ledstyle'] : $this->ledstyle;
    $this->ledcolor   = $this->var['ledcolor'] ? $this->var['ledcolor'] : $this->ledcolor;
    $this->fontsize   = $this->var['fontsize'] ? $this->var['fontsize'] : $this->fontsize;
    $this->textcolor1 = $this->var['textcolor1'] ? $this->var['textcolor1'] : $this->textcolor1;
    $this->textcolor2 = $this->var['textcolor2'] ? $this->var['textcolor2'] : $this->textcolor2;
    $this->ismessage  = $this->var['ismessage'];
    $this->spacing    = $this->var['spacing'] ? $this->var['spacing'] : $this->spacing;
    $this->messagelen = $this->var['messagelen'] ? $this->var['messagelen'] : $this->messagelen;
    $this->weaterposition = $this->var['weaterposition'];
    $this->isview     = $this->var['isview'];
    $this->iforum     = $this->var['isforum'];
    $this->ledimg     = $this->var['ledimg'];
    $this->typeface   = $this->var['typeface'];
    $this->suspension = $this->var['suspension'];
    $this->style      = $this->var['style'];
    $this->script     = '<script>var speed = '.$this->speed.';</script>';
    if($this->isopen) $_G['setting']['seohead'] = $_G['setting']['seohead'].$this->script;
  }
  function _getthread1(){
    global $_G;
    if($this->isforum && $this->threadnum){
      return $this->_getthread2();
    }
    return '';
  }
  function _getthread2(){
    foreach (unserialize($this->forums) as $value) {
      $query = DB::query("select a.fid, a.tid, a.subject, b.message from ".DB::table('forum_thread')." a left join ".DB::table('forum_post')." b on a.tid = b.tid where a.fid = '$value' and b.invisible = '0' and b.status = '0' and b.first = '1' ".$this->_getwhere());
      while ($val = DB::fetch($query)) {
        $std = ($this->direction > 2) ? '<td style="white-space:nowrap;padding:0 10px 0 10px;vertical-align:middle;">' : '<p style="color:{$textcolor};">';
        $etd = ($this->direction > 2) ? '</td>' : '</p>';
        $stdm = ($this->direction > 2) ? '<div style="clear:both;margin-top:-'.$this->spacing.'px;color:{$textcolor}">' : '<div style="clear:both;color:{$textcolor}">';
        $etdm = '</div>';
        $sa = '<div><a href="forum.php?mod=viewthread&tid='.$val['tid'].'" target="_blank" style="font-size:'.$this->fontsize.'px;font-family:'.$this->typeface.';color:{$textcolor};">';
        $ea = '</a></div>';
        $subject = dhtmlspecialchars(strip_tags($val['subject']));
        $message = dhtmlspecialchars(strip_tags($val['message']));
        $drk_ubb = new drk_ubb();
        $message = $drk_ubb->drk_ubb($message);
        $message = $stdm.substr($message, 0, $this->messagelen * strlen($subject)).$etdm;
        $message = $this->ismessage ? $message : '';
        $this->texts[] = $std.$sa.$subject.$ea.$message.$etd;
      }
    }
  }
  function _getwhere(){

    $where = '';
    switch ($this->threadtype) {
      case 1:
        $where = " order by a.dateline Desc limit 0,$this->threadnum";
        break;
      case 2:
        $where = " order by a.views Desc limit 0,$this->threadnum";
        break;
      default:
        $where = " order by a.replies Desc limit 0,$this->threadnum";
        break;
    }
    return $where;
  }
  function _getmyadv(){
    if(!$this->ismyadv || empty($this->myadv)) return '';
    $myadv = explode(chr(10), $this->myadv);
    $std = ($this->direction > 2) ? '<td style="white-space:nowrap;padding:0 10px 0 10px;vertical-align:middle;">' : '<p>';
    $etd = ($this->direction > 2) ? '</td>' : '</p>';
    foreach ($myadv as $value){
      $split = explode('|', $value);
      $this->texts[] = $std.'<a href="'.$split[0].'" target="_blank" style="font-size:'.$this->fontsize.'px;font-family:'.$this->typeface.';color:{$textcolor};">'.  dhtmlspecialchars($split[1]).'</a>'.$etd;
    }
  }
  function _gettext(){
     $i = 0;
     $text = '';
     $this->_getmyadv();
     $this->_getthread1();
     foreach($this->texts as $value){
        if(is_array($value)){
           foreach ($value as $$val) {
              $textcolor = (($i % 2) == 0) ? $this->textcolor1 : $this->textcolor2;
              $color = array('{$textcolor}' => $textcolor);
              $$val  = strtr($$val, $color);
              $text .= $$val;
              $i++;
           }
        }else{
           $textcolor = (($i % 2) == 0) ? $this->textcolor1 : $this->textcolor2;
           $color = array('{$textcolor}' => $textcolor);
           $value = strtr($value, $color);
           $text .= $value;
           $i++;
        }
     }
     return $text;
  }
  function _getclocks(){
    $clock = '<object codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="'.$this->clockwidth.'" align="left" height="50">';
    $clock .= '<param name="movie" value="./source/plugin/drk_ledadv/image/clock'.$this->clocks.'.swf">';
    $clock .= '<param name="quality" value="high">';
    $clock .= '<param name="wmode" value="transparent">';
    $clock .= '<embed src="./source/plugin/drk_ledadv/image/clock'.$this->clocks.'.swf" quality="high" wmode="transparent" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="'.$this->clockwidth.'" height="50" value="transparent">';
    $clock .= '</object>';
    return $clock;
  }
  function _ledout(){
    $output  = '';
    switch ($this->direction) {
       case 1:
          $output = $this->_upcode();
          break;
       case 2:
          $output = $this->_downcode();
          break;
       case 3:
          $output = $this->_leftcode();
          break;
       default:
          $output = $this->_rightcode();
          break;
    }
    return $output;
  }
  function _upcode(){
     return $this->_getudTable('drk_colee0', 'drk_colee1', 'drk_colee2');
  }
  function _downcode(){
     return $this->_getudTable('drk_colee_bottom0', 'drk_colee_bottom1', 'drk_colee_bottom2');
  }
  function _leftcode(){
     return $this->_getlrTable('drk_colee_left0', 'drk_colee_left1', 'drk_colee_left2');
  }
  function _rightcode(){
     return $this->_getlrTable('drk_colee_right0', 'drk_colee_right1', 'drk_colee_right2');
  }
  function _getudcode($id0, $id1, $id2){
     $html .= '<div class="wp cl" style="background:url(./source/plugin/drk_ledadv/image/drk_led.png);">
      <div style="float:left;margin-right:-150px;width:220px;margin-top:12px;">'.$this->_getclocks().'</div>
      <div style="float:right;margin-left:0;width:105px;">'.($this->isweater ? $this->_getweater():'').'</div>
      <div id="'.$id0.'" class="wrap_m" style="width:auto;margin:0 5px 3px 225px;height:76px;overflow:hidden;">
      <div id="'.$id1.'" style="text-align:'.$this->position.';">'.$this->_gettext().'</div>
      <div id="'.$id2.'" style="text-align:'.$this->position.';"></div>
      </div></div>';
     $html .= '<script src="source/plugin/drk_ledadv/image/drk_marquee'.$js.'.js" type="text/javascript"></script>';
     return $html;
  }
  function _getlrcode($id0, $id1, $id2){
     $html .= '<div class="wp cl" style="background:url(./source/plugin/drk_ledadv/image/drk_led.png);">
      <div style="float:left;margin-right:-150px;width:220px;margin-top:12px;">'.$this->_getclocks().'</div>
      <div style="float:right;margin-left:0;width:105px;">'.($this->isweater ? $this->_getweater():'').'</div>
      <div class="wrap_m" style="width:auto;margin:0 5px 3px 225px;">
      <div id="'.$id0.'" class="colee_right" style="width:auto;margin:0 0 0 0;overflow:hidden;">
      <table cellpadding="0" cellspacing="0" border="0">
      <tr>
      <td id="'.$id1.'" valign="top" align="center">
      <table cellpadding="2" cellspacing="0" border="0">
      <tr align="center">'.$this->_gettext().'</tr>
      </table>
      </td>
      <td id="'.$id2.'" valign="top"></td>
      </tr>
      </table>
      </div></div></div>';
     $html .= '<script src="source/plugin/drk_ledadv/image/drk_marquee'.$js.'.js" type="text/javascript"></script>';
     return $html;
  }
  function _getweater($n){
    if($n == 1){
      $weater = '<iframe name="weather_inc" src="http://tianqi.xixik.com/cframe/6" width="115" height="82" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" style="background:#FFFFFF;"></iframe>';
    }else{
      $weater = '<iframe name="weather_inc" src="http://tianqi.xixik.com/cframe/12" width="100%" height="68" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" style="background:#FFFFFF;"></iframe>';
    }
    return $weater;
  }
  function global_header(){
     if(!$this->isopen){ return ''; }
     if($this->isall){
        return $this->_getTable();
     }elseif(in_array($_GET['fid'], unserialize($this->forumss))){
        return $this->_getTable();
     }elseif($this->isfup && $_GET['gid']){
        $query = DB::query("select fup from ".DB::table('forum_forum')." where fid in (".implode(',', unserialize($this->forumss)).")");
        while ($fup = DB::fetch($query)) {
          if(in_array($_GET['gid'], $fup)){
            return $this->_getTable();
          }
        }
     }elseif($this->iforum && CURSCRIPT == 'forum' && empty($_GET['mod'])){
        return $this->_getTable();
     }elseif($this->isportal){
       if(CURSCRIPT == 'portal'){
         if($_GET['mod'] == 'index'){
            return $this->_getTable();
         }elseif($this->islist && $_GET['mod'] == 'list'){
            return $this->_getTable();
         }elseif($this->isview && $_GET['mod'] == 'view'){
            return $this->_getTable();
         }
       }
     }
  }
  function _getTable(){
    $html = '';
    $html .= '<div class="wp cl">';
    $html .= '<table style="table-layout:fixed;height:'.$this->ledheight.'px;width:100%;border:'.$this->ledstyle.' '.$this->ledwidth.'px '.$this->ledcolor.';background:url('.$this->ledimg.');">';
    $html .= '<tr>';
    if($this->showclock){
         $html .= '<td style="width:'.$this->clockwidth.'px;">';
         $html .= $this->_getclocks();
         $html .= '</td>';
    }
    $html .= '<td id="drk_ledtd" style="width:100%;">';
    $html .= $this->_ledout();
    $html .= '</td>';
    if($this->isweater && $this->weaterposition == 1){
         $html .= '<td style="width:115px;overflow:hidden;">';
         $html .= $this->_getweater($this->weaterposition);
         $html .= '</td>';
    }
    $html .= '</tr>';
    if($this->isweater && $this->weaterposition == 2){
         $html .= '<tr>';
         $html .= '<td colspan="3" style="padding:5px;" align="center">';
         $html .= $this->_getweater($this->weaterposition);
         $html .= '</td>';
         $html .= '</tr>';
    }
    $html .= '</table>';
    $html .= '</div>';
    $html .= '<script src="source/plugin/drk_ledadv/image/drk_marquee'.$this->direction.'.js" type="text/javascript"></script>';

    return $html;

  }
  function _getlrTable($id0, $id1, $id2){
    $html = '';
    $html .= '<div id="'.$id0.'" style="overflow:hidden;width:auto;margin:0 5px 0 5px;">';
    $html .= '<table cellpadding="0" cellspacing="0">';
    $html .= '<tr>';
    $html .= '<td id="'.$id1.'" valign="top" align="center">';
    $html .= '<table cellpadding="0" cellspacing="0" class="fonttable">';
    $html .= '<tr>';
    $html .= $this->_gettext();
    $html .= '</tr>';
    $html .= '</table>';
    $html .= '</td>';
    $html .= '<td id="'.$id2.'" valign="top"></td>';
    $html .= '</tr>';
    $html .= '</table>';
    $html .= '</div>';

    return $html;
  }
  function _getudTable($id0, $id1, $id2){
     $html = '';
     $html .= '<div id="'.$id0.'" style="overflow:hidden;height:'.$this->ledheight.'px;width:auto;text-align:'.$this->position.'">';
     $html .= '<div id="'.$id1.'" style="text-align:'.$this->position.'">';
     $html .= $this->_gettext();
     $html .= '</div>';
     $html .= '<div id="'.$id2.'"></div>';
     $html .= '</div>';

     return $html;
  }
}

class drk_ubb{
   function drk_ubb($Text) {
        $Text = preg_replace("/\[.+?\]/is", "\\1", $Text);
        return $Text;
  }
}
?>
