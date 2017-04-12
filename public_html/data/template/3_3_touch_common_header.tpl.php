<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Cache-control" content="<?php if($_G['setting']['mobile']['mobilecachetime'] > 0) { ?><?php echo $_G['setting']['mobile']['mobilecachetime'];?><?php } else { ?>no-cache<?php } ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
<meta name="format-detection" content="telephone=no" />
<meta name="keywords" content="<?php if(!empty($metakeywords)) { echo dhtmlspecialchars($metakeywords); } ?>" />
<meta name="description" content="<?php if(!empty($metadescription)) { echo dhtmlspecialchars($metadescription); ?> <?php } ?>,<?php echo $_G['setting']['bbname'];?>" />
<title><?php if(!empty($navtitle)) { ?><?php echo $navtitle;?> <?php } if(empty($nobbname)) { ?> - <?php echo $_G['setting']['bbname'];?> - <?php } ?> ÊÖ»ú°æ</title>
<link rel="stylesheet" href="<?php echo $_G['style']['styleimgdir'];?>/touch/common/elecnation_style_gold.css" type="text/css" media="all">
<link href="<?php echo $_G['style']['styleimgdir'];?>/touch/common/images/apple-touch-icon.png" rel="apple-touch-icon" />
<link href="<?php echo $_G['style']['styleimgdir'];?>/touch/common/images/apple-touch-icon-76x76.png" rel="apple-touch-icon" sizes="76x76" />
<link href="<?php echo $_G['style']['styleimgdir'];?>/touch/common/images/apple-touch-icon-120x120.png" rel="apple-touch-icon" sizes="120x120" />
<link href="<?php echo $_G['style']['styleimgdir'];?>/touch/common/images/apple-touch-icon-152x152.png" rel="apple-touch-icon" sizes="152x152" />
<script src="<?php echo STATICURL;?>js/mobile/jquery-1.8.3.min.js?<?php echo VERHASH;?>" type="text/javascript"></script>

<script type="text/javascript">var STYLEID = '<?php echo STYLEID;?>', STATICURL = '<?php echo STATICURL;?>', IMGDIR = '<?php echo IMGDIR;?>', VERHASH = '<?php echo VERHASH;?>', charset = '<?php echo CHARSET;?>', discuz_uid = '<?php echo $_G['uid'];?>', cookiepre = '<?php echo $_G['config']['cookie']['cookiepre'];?>', cookiedomain = '<?php echo $_G['config']['cookie']['cookiedomain'];?>', cookiepath = '<?php echo $_G['config']['cookie']['cookiepath'];?>', showusercard = '<?php echo $_G['setting']['showusercard'];?>', attackevasive = '<?php echo $_G['config']['security']['attackevasive'];?>', disallowfloat = '<?php echo $_G['setting']['disallowfloat'];?>', creditnotice = '<?php if($_G['setting']['creditnotice']) { ?><?php echo $_G['setting']['creditnames'];?><?php } ?>', defaultstyle = '<?php echo $_G['style']['defaultextstyle'];?>', REPORTURL = '<?php echo $_G['currenturl_encode'];?>', SITEURL = '<?php echo $_G['siteurl'];?>', JSPATH = '<?php echo $_G['setting']['jspath'];?>';</script>

<script src="<?php echo STATICURL;?>js/mobile/common.js?<?php echo VERHASH;?>" type="text/javascript" charset="<?php echo CHARSET;?>"></script>
</head>

<body class="bg">