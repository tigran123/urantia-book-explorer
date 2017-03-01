<?php
header("Content-Type: text/html; charset=utf-8"); 

$text = isset($_GET['text']) ?  $_GET['text'] : '';
if ($text) {
   $mod_idx = isset($_GET['mod_idx']) ?  $_GET['mod_idx'] : 0;
   $ic = isset($_GET['ic']) ?  $_GET['ic'] : 1;
   setlocale(LC_ALL, 'en_GB.UTF-8');
   $cmd = "LANG=en_GB.UTF-8 fgrep --no-filename ";
   if ($ic) $cmd .= "--ignore-case ";
   $cmd .= escapeshellarg($text) . " text/" . $mod_idx . "/p???.html";

   //$cmd = "LANG=en_GB.UTF-8 sed -ne 's/" . escapeshellarg($text) . "//gi'" . " text/" . $mod_idx . "/p???.html";

   $output = [];
   exec($cmd, $output);
   foreach($output as $line) {
     echo $line;
   }
}
?>
