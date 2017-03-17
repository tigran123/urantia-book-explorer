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
   $search_range = isset($_GET['search_range']) ?  $_GET['search_range'] : 0;

   //Заменяем все знаки препинания на возможно обрамленные тегом, а одиночную кавычку на символ апострофа
   $text = preg_replace(['/([.?])/u', '/([,;:!–-])/u', '/(\')/u'], ['\\\\$1(<\/em>)?', '$1(<\/em>)?', '’(<\/em>)?'], $text);

   //Заменяем все слова в строке поиска на обрамленные в тэг <em> и возможные знаки препинания
   //здесь – и - это разные тире: первое длинное, второе короткое
   $text = preg_replace('/\b(\w+)\b/u', '(<em>)?[.,;“"«!?(–-]?\s?$1\s?[.,;’”"»!?)–-]?\s?(<\/em>)?', $text);
   //убираем первое вхождение знаков, чтобы не выделялись предшествующие пробелы, запятые и пр. символы...
   $text = preg_replace('/\[\.,;“"«!\?\(–-\]\?\\\\s\?/u', '', $text, 1);
   $pattern = '/(' . $text . ')/u';
   if ($ic) $pattern .= 'i';
   $textdir = "text/" . $mod_idx;
   $replace = '<span style="background-color:yellow;">$1</span>';
   $time_start = microtime(true);
   if ($search_range > 0) {
      $matched_lines = preg_filter($pattern, $replace, file($textdir . "/toc.html"));
      foreach($matched_lines as $line) echo $line;
   }
   if ($search_range != 2) {
      for ($i = $i_min; $i <= $i_max; $i++) {
         $filename = sprintf($textdir . "/p%03d.html", $i);
         $matched_lines = preg_filter($pattern, $replace, file($filename));
         foreach($matched_lines as $line) {
            $count = 0;
            str_replace("<h4>", "", $line, $count);
            if ($count ==0) echo $line;
         }
      }
   }
   echo "<p>" . sprintf("%.4fs",microtime(true) - $time_start);
}
?>
