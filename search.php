<?php
header("Content-Type: text/html; charset=utf-8"); 

$text = isset($_GET['text']) ?  $_GET['text'] : '';
if ($text) {
   $mod_idx = isset($_GET['mod_idx']) ?  $_GET['mod_idx'] : 0;
   $ic = isset($_GET['ic']) ?  $_GET['ic'] : 1;
   $search_part = isset($_GET['search_part']) ?  $_GET['search_part'] : 0;
   switch ($search_part) {
      case 0:
         $i_min = 0;
         $i_max = 196;
         break;
      case 1:
         $i_min = 0;
         $i_max = 31;
         break;
      case 2:
         $i_min = 32;
         $i_max = 56;
         break;
      case 3:
         $i_min = 57;
         $i_max = 119;
         break;
      case 4:
         $i_min = 120;
         $i_max = 196;
         break;
   }
   $pattern = '/(' . preg_replace('/\b(\w+)\b/u', '(<em>)?[.,;"«!?(–-]?\s?$1[.,;"»!?)–-]?\s?(<\\\\/em>)?', $text) . ')/u'; //здесь – и - это разные тире: первое длинное, второе короткое
   $pattern = preg_replace('/\[\.,;"«!\?\(–-\]\?\\\\s\?/u', '', $pattern, 1); //убираем первое вхождение, чтобы не выделялись предшествующие пробелы, запятые и пр. символы...
   if ($ic) $pattern .= 'i';
   $replace = '<span style="background-color:yellow;">$1</span>';
   for ($i = $i_min; $i <= $i_max; $i++) {
      $filename = sprintf("text/" . $mod_idx . "/p%03d.html", $i);
      $lines = file($filename);
      foreach($lines as $line) {
         $count = 0;
         $matched_line = preg_replace($pattern, $replace, $line, -1, $count);
         if ($count > 0) echo $matched_line;
      }
   }
}
?>
