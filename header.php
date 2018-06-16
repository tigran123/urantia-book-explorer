<?php

if (isset($_COOKIE['lang']))
   $lang = $_COOKIE['lang'];
else {
   $ip = get_client_ip();
   $country = trim(file_get_contents("http://ipinfo.io/{$ip}/country"));

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

$tooltips = 0;
if (isset($_COOKIE['tooltips']))
   $tooltips = $_COOKIE['tooltips'];

$animations = 1;
if (isset($_COOKIE['animations']))
   $animations = $_COOKIE['animations'];

$theme = 'cupertino';
if (isset($_COOKIE['theme']))
   $theme = $_COOKIE['theme'];

$scrollsync = 1;// сохраняет значение флажка по дефолту вкл
if (isset($_COOKIE['scrollsync']))// если переменная инициализированна в куки файлах то:
   $scrollsync = $_COOKIE['scrollsync'];// считать значение и записать его в переменную

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
<script src='https://code.jquery.com/jquery-1.11.3.min.js' type='text/javascript'></script>
</head><body>";

$htmlfoot = "<script src='jquery/jquery-3.1.1.min.js'></script>
<script src='jquery/jquery-ui-1.12.1/jquery-ui.min.js'></script>
<script src='jquery/jquery.scrollTo-2.1.2/jquery.scrollTo.min.js'></script>
<script src='jquery/jquery.bonsai.js'></script>
<script src='jquery/jquery.mark.min.js' charset='UTF-8'></script>
<script src='jquery/jquery.scrollSync.js'></script> <!-- Подключаем файл с кодом-->
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
?>
