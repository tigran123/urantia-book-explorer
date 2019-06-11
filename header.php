<?php

if (isset($_COOKIE['lang']))
   $lang = $_COOKIE['lang'];
else {
   $ip = get_client_ip();
   $country = is_private_ip($ip) ? 'EN' : trim(file_get_contents("http://ipinfo.io/{$ip}/country"));

   switch($country) {
      case 'UA':
         $lang = 'ua';
         break;
      case 'RU':
      case 'BY':
      case 'AM':
      case 'KZ':
         $lang = 'ru';
         break;
      default:
         $lang = 'en';
         break;
   }
   $expire = (int)(time() + 3600*24*365*100); // expire in 100 years
   setcookie('lang', $lang, $expire);
}

$shortcontext = 1;
if (isset($_COOKIE['shortcontext']))
   $shortcontext = $_COOKIE['shortcontext'];

$tooltips = 0;
if (isset($_COOKIE['tooltips']))
   $tooltips = $_COOKIE['tooltips'];

$animations = 1;
if (isset($_COOKIE['animations']))
   $animations = $_COOKIE['animations'];

$theme = 'cupertino';
if (isset($_COOKIE['theme']))
   $theme = $_COOKIE['theme'];

$scrollsync = 0;
if (isset($_COOKIE['scrollsync']))
   $scrollsync = $_COOKIE['scrollsync'];

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
<link rel='stylesheet' type='text/css' href='css/cookieBubble.min.css' />
<link rel='stylesheet' type='text/css' href='css/ubex.css' />
</head><body>
<div class='cookieBubble'>
   <div class='cb-wrapper'>
      <div class='cb-row'>
         <div class='cb-message'>
            <span>We use cookies to personalize your experience. By continuing to visit this website you agree to our use of cookies.</span>
            <a href='javascript:void(0)' class='gotit-btn'>GOT IT, I AGREE!</a>
         </div>
      </div>
   </div>
</div>";

$htmlfoot = "<script src='jquery/jquery-3.1.1.min.js'></script>
<script src='jquery/jquery-ui-1.12.1/jquery-ui.min.js'></script>
<script src='jquery/jquery.scrollTo-2.1.2/jquery.scrollTo.min.js'></script>
<script src='jquery/jquery.bonsai.js'></script>
<script src='jquery/jquery.mark.min.js' charset='UTF-8'></script>
<script src='jquery/cookieBubble.min.js'></script>
<script src='jquery/jquery.scrollSync.js'></script>
<script type='text/javascript'>var INPUT_SEARCH_STRING = '".$INPUT_SEARCH_STRING."';</script>
<script src='js/index.js'></script>
</body></html>";

function get_client_ip() {
   if (!empty($_SERVER['HTTP_CLIENT_IP']))
      return $_SERVER['HTTP_CLIENT_IP'];
   elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
      return $_SERVER['HTTP_X_FORWARDED_FOR'];
   else
      return $_SERVER['REMOTE_ADDR'];
}

function is_private_ip($ip) {
   $i = explode('.', $ip);

   if ($i[0] == 10 || $i[0] == 127)
       return true;
   else if ($i[0] == 172 && $i[1] > 15 && $i[1] < 32)
       return true;
   else if ($i[0] == 192 && $i[1] == 168)
       return true;

   error_log($ip."NOT a private IP!");
   return false;
}
?>
