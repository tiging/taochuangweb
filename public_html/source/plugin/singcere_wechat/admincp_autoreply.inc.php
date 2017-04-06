<?php 

/**
 *      (C)2007-2012 singcere.net
 *      This is a freeware, But you have no right to modify or distribute
 *
 *      $Id$
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}

$langs = &$scriptlang['singcere_wechat'];

$operation = in_array($_GET['operation'], array('cus', 'ext', 'del', 'install')) ? $_GET['operation'] : 'cus';

admincp_showsubmenu(null, array(
    array(lang('plugin/singcere_wechat', 'admincp_autoreply_cus'), "plugins&operation=config&do=$plugin[pluginid]&identifier=$plugin[identifier]&pmod=$module[name]&operation=cus", $operation == 'cus'),
    array(lang('plugin/singcere_wechat', 'admincp_autoreply_ext'), "plugins&operation=config&do=$plugin[pluginid]&identifier=$plugin[identifier]&pmod=$module[name]&operation=ext", $operation == 'ext'),
));

if ($operation == 'ext') {
    if (!submitcheck('editsubmit')) {
        loadcache(array('plugin'));
        ?>
        <script type="text/JavaScript">
            var rowtypedata = [
            [[1,'<input type="text"  name="newdisplayorder[]" value="0" class="td25"/>', ''], 
             [1,'<input type="text" name="newpattern[]" value="" style="width:350px;"/>', ''],
             [1,'<?php echo showpluginselect();?>', ''],
            [5, '<div><span class="lightfont"><?php echo $langs['admincp_cmdaddnotice']; ?></span> <a href="javascript:;" class="deleterow" onClick="deleterow(this)"><?php cplang('delete', null, true); ?></a></div>']],
            ];
        </script>
        <?php
        showformheader("plugins&operation=config&do=$plugin[pluginid]&identifier=$plugin[identifier]&pmod=$module[name]&operation=ext", 'editsubmit');
        showtableheader('');
        showsubtitle(array(lang('plugin/singcere_wechat','admincp_autoreply_priority'), lang('plugin/singcere_wechat','admincp_autoreply_regular'), lang('plugin/singcere_wechat','admincp_autoreply_extend_plugin'), 'enable'), 'header', array('style="width:100px"', 'style="width:400px"', 'style="width:200px"'));
        $extlist = C::t('#singcere_wechat#singcere_wechat_cmd')->fetch_all_by_type('extension', -1, '>=', '');
        foreach ($extlist as $cmd) {
            showtablerow('',  array(), array(
                '<input type="text" value="' . $cmd['displayorder'] . '" name="displayorder[' . $cmd['id'] . ']" class="td25">',
                '<input type="text" value="' . $cmd['pattern'] . '" name="pattern[' . $cmd['id'] . ']" style="width:350px;">',
                showpluginselect($cmd['id'], $cmd['cmdname']),
                 '<input type="checkbox"' . ($cmd['status'] > 0 ? 'checked' : '') . ' name="status[' . $cmd['id'] . ']">',
                ));
        }
        showtablerow('', array('colspan="15"'), array("<div><a href=\"###\" onclick=\"addrow(this, 0)\" class=\"addtr\">$langs[admincp_autoreply_new]</a></div>"));
        showsubmit('editsubmit', 'submit', "");
        showtablefooter();
        showformfooter();
    } else {
        if (is_array($_GET['newpattern'])) {
            foreach ($_GET['newpattern'] as $key => $value) {
                $newpattern = trim($value);
                if (empty($newpattern)) {
                    continue;
                }
                C::t('#singcere_wechat#singcere_wechat_cmd')->insert(
                    array(
                        'displayorder' => $_GET['newdisplayorder'][$key],
                        'pattern' => $newpattern,
                        'cmdname' => $_GET['plugin'][$key],
                        'type' => 'extension',
                        'status' => 1,
                    )
                );
            }
        }
        if (is_array($_GET['pattern'])) {
            foreach ($_GET['pattern'] as $key => $value) {
                $pattern = trim($value);
                if (empty($pattern)) {
                    continue;
                }
                C::t('#singcere_wechat#singcere_wechat_cmd')->update($key, 
                    array(
                        'displayorder' => $_GET['displayorder'][$key],
                        'pattern' => $pattern,
                        'cmdname' => $_GET['plugin'][$key],
                        'status' => empty($_GET['status'][$key]) ? 0 : 1
                    )
                );
            }
        }
        cpmsg('setting_update_succeed', "action=plugins&operation=config&do=$plugin[pluginid]&identifier=$plugin[identifier]&pmod=$module[name]&operation=ext", 'succeed');
    }
} else if ($operation == 'cus') {
    ?>
    <script type="text/JavaScript">
        var rowtypedata = [
        [[1,'<input type="text" class="s-td-50" name="newdisplayorder[]" value="0" />', ''], [1,'<input type="text" class="td31" name="newalias[]" value="" />', ''],
        [5, '<div><span class="lightfont"><?php echo $langs['admincp_cmdaddnotice']; ?></span> <a href="javascript:;" class="deleterow" onClick="deleterow(this)"><?php cplang('delete', null, true); ?></a></div>']],

        [[1,'', ''], [1,'<?php echo $langs['admincp_autoreply_title']; ?><input type="text" class="td31" name="newresponsetitle[{1}][]" value="" />', ''], [1,'<span class=""><?php echo $langs['admincp_autoreply_desc']; ?></span><textarea type="text"  name="newresponsedescription[{1}][]" class="txt"></textarea>', 'longtxt'], [1,'<?php echo $langs['admincp_autoreply_picurl']; ?><input type="text" class="td31" name="newresponseimgurl[{1}][]" value="" />', ''], [1,'<?php echo $langs['admincp_autoreply_link']; ?><input type="text" class="td31" name="newresponselink[{1}][]" value="" />', ''],
        [5, '<div><span class="lightfont"><?php echo $langs['admincp_cmdaddnotice']; ?> </span> <a href="javascript:;" class="deleterow" onClick="deleterow(this)"><?php cplang('delete', null, true); ?></a></div>']],
        ];
    </script>
    <style>
        span.dtitle {width:30px;display:block; float:left;line-height: 40px;}
        
    </style>
    <?php
    if (!submitcheck('editsubmit', 1)) {
        showtips(lang('plugin/singcere_wechat', 'admincp_autoreply_tips'));
        showformheader("plugins&operation=config&do=$plugin[pluginid]&identifier=$plugin[identifier]&pmod=$module[name]&operation=cus", 'editsubmit');
        showtableheader('');
        showsubtitle(array($langs['admincp_autoreply_displayorder'], $langs['admincp_autoreply_keyword'], $langs['admincp_autoreply_text'], $langs['admincp_autoreply_type'], $langs['admincp_autoreply_enablel'], $langs['admincp_autoreply_operation']), 'header', array());
        $cuscmd = C::t('#singcere_wechat#singcere_wechat_cmd')->fetch_all_by_type('custom', -1, '>=', '');
        foreach ($cuscmd as $cmd) {
            showtablerow('', array('class="td25"', 'class="td31"', 'class="longtxt" style="width:400px"', 'class="td31"', 'class="td31"', ''), array(
                '<input type="text" value="' . $cmd['displayorder'] . '" name="displayorder[' . $cmd['id'] . ']" class="s-td-50">',
                '<input type="text" value="' . $cmd['alias'] . '" name="alias[' . $cmd['id'] . ']" class="td31"> ',
                '<textarea type="text" name="cmdrtn[' . $cmd['id'] . ']" class="txt" onblur="this.style.height=\'\'" onfocus="this.style.height=\'100px\';">'.$cmd['cmdrtn'].'</textarea>',
                '<input type="radio" value="1" name="responsetype[' . $cmd['id'] . ']" ' . ($cmd['responsetype'] == 1 ? 'checked' : '') . '>'.$langs['admincp_autoreply_text'].'<input type="radio" value="2" name="responsetype[' . $cmd['id'] . ']" ' . ($cmd['responsetype'] == 2 ? 'checked' : '') . '>'.$langs['admincp_autoreply_rich'],
                '<input type="checkbox"' . ($cmd['status'] > 0 ? 'checked' : '') . ' name="status[' . $cmd['id'] . ']">',
                '<a href="' . ADMINSCRIPT . "?action=plugins&operation=config&do=$plugin[pluginid]&identifier=$plugin[identifier]&pmod=$module[name]&operation=del&type=cmd&id=$cmd[id]"  . '">'.cplang('delete').'<a>'));
            $responsearray = C::t('#singcere_wechat#singcere_wechat_richresponse')->fetch_all_by_cmdid($cmd['id']);
            foreach ($responsearray as $response) {
                showtablerow("class='rich_row_$response[cmdid] richmsg' style='background:#FAFAFA;'", array('', '', 'class="longtxt"'), array(
                    '',
                    $langs['admincp_autoreply_title']."<input type='text' class='td31' value='$response[title]' name='responsetitle[$response[cmdid]][$response[id]]'>",
                    "<span class=''>$langs[admincp_autoreply_desc]</span>" . "<textarea type='text' name='responsedescription[$response[cmdid]][$response[id]]' class='txt'>$response[description]</textarea>",
                   "$langs[admincp_autoreply_picurl]<input type='text' class='td31' value='$response[imgurl]' name='responseimgurl[$response[cmdid]][$response[id]]'>",
                   "$langs[admincp_autoreply_link]<input type='text' class='td31' value='$response[link]' name='responselink[$response[cmdid]][$response[id]]'>",
                    '<a href="' . ADMINSCRIPT . "?action=plugins&operation=config&do=$plugin[pluginid]&identifier=$plugin[identifier]&pmod=$module[name]&operation=del&type=response&id=$response[id]" . '">'.cplang('delete').'<a>'
                ));
            }

            showtablerow("class='rich_row_$cmd[id]'", array('colspan="15"'), array('<div class="lastboard"><a href="###" onclick="addrow(this, 1, ' . $cmd['id'] . ')" class="addtr">'.$langs[admincp_autoreply_newrichitem].'</a><span></span></div>'));
        }
        showtablerow('', array('colspan="15"'), array("<div><a href=\"###\" onclick=\"addrow(this, 0)\" class=\"addtr\">$langs[admincp_autoreply_new]</a></div>"));
        showsubmit('editsubmit', 'submit', "");
        showtablefooter();
        showformfooter();
    } else {
        if (is_array($_GET['newalias'])) {
            foreach ($_GET['newalias'] as $key => $value) {
                $newaliasvalue = trim($value);
                if (empty($newaliasvalue)) {
                    continue;
                }
                C::t('#singcere_wechat#singcere_wechat_cmd')->insert(
                    array(
                        'displayorder' => $_GET['newdisplayorder'][$key],
                        'alias' => $newaliasvalue,
                        'cmdrtn' => $_GET['newcmdrtn'][$key],
                        'helptext' => $_GET['newhelptext'][$key],
                        'type' => 'custom', 'responsetype' => '1'
                    )
                );
            }
        }
        if (is_array($_GET['alias'])) {
            foreach ($_GET['alias'] as $key => $value) {
                $aliasvalue = trim($value);
                if (empty($aliasvalue)) {
                    continue;
                }
                C::t('#singcere_wechat#singcere_wechat_cmd')->update($key, 
                    array(
                        'displayorder' => $_GET['displayorder'][$key],
                        'alias' => $aliasvalue,
                        'cmdrtn' => $_GET['cmdrtn'][$key],
                        'helptext' => $_GET['helptext'][$key],
                        'type' => 'custom',
                        'responsetype' => $_GET['responsetype'][$key],
                        'status' => empty($_GET['status'][$key]) ? 0 : 1
                    )
                );
            }
        }
        if (is_array($_GET['newresponsetitle'])) {
            foreach ($_GET['newresponsetitle'] as $cmdid => $response) {
                foreach ($response as $key => $title) {
                    if (empty($title)) {
                        continue;
                    }
                    C::t('#singcere_wechat#singcere_wechat_richresponse')->insert(
                        array(
                            'cmdid' => $cmdid,
                            'title' => $_GET['newresponsetitle'][$cmdid][$key],
                            'imgurl' => $_GET['newresponseimgurl'][$cmdid][$key],
                            'link' => $_GET['newresponselink'][$cmdid][$key],
                            'description' => $_GET['newresponsedescription'][$cmdid][$key]
                        )
                    );
                }
            }
        }
        if (is_array($_GET['responsetitle'])) {
            foreach ($_GET['responsetitle'] as $cmdid => $response) {
                foreach ($response as $key => $title) {
                    if (empty($title)) {
                        continue;
                    }
                    C::t('#singcere_wechat#singcere_wechat_richresponse')->update($key, 
                        array(
                            'cmdid' => $cmdid,
                            'title' => $_GET['responsetitle'][$cmdid][$key],
                            'imgurl' => $_GET['responseimgurl'][$cmdid][$key],
                            'link' => $_GET['responselink'][$cmdid][$key],
                            'description' => $_GET['responsedescription'][$cmdid][$key]
                        )
                    );
                }
            }
        }
        cpmsg('setting_update_succeed', "action=plugins&operation=config&do=$plugin[pluginid]&identifier=$plugin[identifier]&pmod=$module[name]&operation=cus", 'succeed');
    }
} else if ($operation == 'del') {
    if ($_GET['type'] == 'cmd') {
        C::t('#singcere_wechat#singcere_wechat_cmd')->delete(array('id' => $_GET['id']));
    } else if ($_GET['type'] == 'response') {
        C::t('#singcere_wechat#singcere_wechat_richresponse')->delete(array('id' => $_GET['id']));
    }
    cpmsg($langs['admincp_cpmsg_deletesuccess'], "action=plugins&operation=config&identifier=$plugin[identifier]&pmod=$module[name]&operation=cus", 'succeed');
} else if ($operation == 'install') {
    $extname = trim($_GET['extname']);
    if(C::t('#singcere_wechat#singcere_wechat_cmd')->exist_by_cmdname($extname)) {
        cpmsg($langs['admincp_cpmsg_extinstalled'], 'action=plugins&operation=config&identifier=singcere_wechat&pmod=admincp_cmd&operation=ext', 'error');
    } else {
        $dir = DISCUZ_ROOT . './source/plugin/singcere_wechat/extension';
        $content = file_get_contents($dir . '/extension_' . $_GET['extname'] . '.php');
        if ($content) {
            $content = diconv($content, 'UTF-8', CHARSET);
            preg_match("/alias\:(.+?)\n/", $content, $r); $alias = trim($r[1]);
            preg_match("/helptext\:(.+?)\n/", $content, $r); $helptext = trim($r[1]);
            preg_match("/pattern\:(.+?)\n/", $content, $r); $pattern = trim($r[1]);
            $data = array(
                'cmdname' => dhtmlspecialchars($_GET['extname']),
                'alias' => dhtmlspecialchars($alias),
                'helptext' => dhtmlspecialchars($helptext),
                'pattern' => dhtmlspecialchars($pattern),
                'type' => 'extension',
            );
            C::t('#singcere_wechat#singcere_wechat_cmd')->insert($data);
            cpmsg($langs['admincp_cpmsg_extinstallsuccess'], 'action=plugins&operation=config&identifier=singcere_wechat&pmod=admincp_cmd&operation=ext', 'succeed');
        } else {
            cpmsg($langs['admincp_cpmsg_extnotexist'], 'action=plugins&operation=config&identifier=singcere_wechat&pmod=admincp_cmd&operation=ext', 'error');
        }
    }
    
}

function admincp_showsubmenu($title, $menus = array(), $right = '', $replace = array()) {
    $s = '<div style="margin-top:5px;">' . $right . '<ul class="tab1">';
    foreach ($menus as $k => $menu) {
        if (is_array($menu[0])) {
            $s .= '<li id="addjs' . $k . '" class="' . ($menu[1] ? 'current' : 'hasdropmenu') . '" onmouseover="dropmenu(this);"><a href="#"><span>' . cplang($menu[0]['menu']) . '<em>&nbsp;&nbsp;</em></span></a><div id="addjs' . $k . 'child" class="dropmenu" style="display:none;">';
            if (is_array($menu[0]['submenu'])) {
                foreach ($menu[0]['submenu'] as $submenu) {
                    $s .= $submenu[1] ? '<a href="' . ADMINSCRIPT . '?action=' . $submenu[1] . '" class="' . ($submenu[2] ? 'current' : '') . '" onclick="' . $submenu[3] . '">' . cplang($submenu[0]) . '</a>' : '<a><b>' . cplang($submenu[0]) . '</b></a>';
                }
            }
            $s .= '</div></li>';
        } else {
            $s .= '<li' . ($menu[2] ? ' class="current"' : '') . '><a href="' . (!$menu[4] ? ADMINSCRIPT . '?action=' . $menu[1] : $menu[1]) . '"' . (!empty($menu[3]) ? ' target="_blank"' : '') . '><span>' . cplang($menu[0]) . '</span></a></li>';
        }
    }
    $s .= '</ul></div>';
    echo!empty($menus) ? '<div class="itemtitle">' . $s . '</div>' : $s;
}

function showpluginselect($cmdid, $identifier) {
    global $_G;
    static $pluginlist;
    if(empty($pluginlist)) {
        $pluginlist = DB::fetch_all("SELECT * FROM %t", array('common_plugin'), 'identifier');
    }
    $pluginroot = DISCUZ_ROOT . './source/plugin/';
    $selHtml = '<select name="plugin['.$cmdid.']">';
    foreach($_G['setting']['plugins']['version'] as $key => $version) {
        if(file_exists($pluginroot.$key.'/wechat_module.class.php')) {
            $selHtml .= '<option value="'.$key.'" '.($identifier == $key ? 'selected' : '').'>'.$pluginlist[$key]['name'].'</option>';
        }
    }
    $selHtml .= '</select>';
    return $selHtml;
}
?>