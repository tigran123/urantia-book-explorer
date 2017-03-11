<?php
header("Content-Type: text/html; charset=utf-8"); 

$text = isset($_GET['text']) ?  $_GET['text'] : '';
if ($text) {
   $mod_idx = isset($_GET['mod_idx']) ?  $_GET['mod_idx'] : 0;
   $ic = isset($_GET['ic']) ?  $_GET['ic'] : 1;
   $search_part = isset($_GET['search_part']) ?  $_GET['search_part'] : 0;
   switch ($search_part) {
      case 0:
         $ic_min = 0;
         $ic_max = 196;
         break;
      case 1:
         $ic_min = 0;
         $ic_max = 31;
         break;
      case 2:
         $ic_min = 32;
         $ic_max = 56;
         break;
      case 3:
         $ic_min = 57;
         $ic_max = 119;
         break;
      case 4:
         $ic_min = 120;
         $ic_max = 196;
         break;
      default:
         $ic_min = 0;
         $ic_max = 196;
         break;
   }
   $pattern = "/" . $text . "/";
   if ($ic) $pattern .= "i";
   for ($i = $ic_min; $i <= $ic_max; $i++) {
      $filename = sprintf("text/" . $mod_idx . "/p%03d.html", $i);
      $lines = file($filename);
      foreach($lines as $line) if (preg_match($pattern, $line)) echo $line;
   }
}
?>
