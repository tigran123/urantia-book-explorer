<?php
header("Content-Type: text/html; charset=utf-8"); 

$text = isset($_GET['text']) ?  $_GET['text'] : '';
if ($text) {
   $mod_idx = isset($_GET['mod_idx']) ?  $_GET['mod_idx'] : 0;
   $ic = isset($_GET['ic']) ?  $_GET['ic'] : 1;
   $pattern = "/" . $text . "/";
   if ($ic) $pattern .= "i";
   for ($i = 0; $i <= 196; $i++) {
      $filename = sprintf("text/" . $mod_idx . "/p%03d.html", $i);
      $lines = file($filename);
      foreach($lines as $line) if (preg_match($pattern, $line)) echo $line;
   }
}

?>
