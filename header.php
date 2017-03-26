<?php

$lang = 'ru';
if (isset($_COOKIE['lang']))
   $lang = $_COOKIE['lang'];

$tooltips = 1;
if (isset($_COOKIE['tooltips']))
   $tooltips = $_COOKIE['tooltips'];

$animations = 1;
if (isset($_COOKIE['animations']))
   $animations = $_COOKIE['animations'];

$drafts = 0;
if (isset($_COOKIE['drafts']))
   $drafts = $_COOKIE['drafts'];

$theme = 'cupertino';
if (isset($_COOKIE['theme']))
   $theme = $_COOKIE['theme'];

require 'msg_' . $lang . '.php';

$htmlhead = "<!DOCTYPE html>
<html lang='eng-US'>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
<meta name='description' content='$DESCRIPTION'>
<meta name='rating' content='general'>
<meta name='author' content='$AUTHOR'>
<link rel='shortcut icon' href='img/favicon.ico'> 
<title>$TITLE</title>
<link id='stylesheet' rel='stylesheet' href='jquery/jquery-ui-themes-1.12.1/themes/$theme/jquery-ui.min.css'>
<link rel='stylesheet' type='text/css' href='jquery/jquery.bonsai.css' />
<link rel='stylesheet' type='text/css' href='css/ubex.css' />
</head><body>";

$htmlfoot = "<script src='jquery/jquery-3.1.1.min.js'></script>
<script src='jquery/jquery-ui-1.12.1/jquery-ui.min.js'></script>
<script src='jquery/jquery.scrollTo-2.1.2/jquery.scrollTo.min.js'></script>
<script src='jquery/jquery.bonsai.js'></script>
<script src='jquery/jquery.mark.min.js' charset='UTF-8'></script>
<script src='js/index.js'></script>
</body></html>";
?>
