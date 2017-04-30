<?php
require('search.php');
ini_set('memory_limit','300M');
header("Content-Type: text/html; charset=utf-8");

$pattern_ar = '';
$matches = [];
$par_count = 0;
$match_count = 0;
$text = $_GET['text'];
if (isset($text)) {
   $mod_idx = isset($_GET['mod_idx']) ?  $_GET['mod_idx'] : 0;
   $textdir = "text/" . $mod_idx;
   $ic = isset($_GET['ic']) ?  $_GET['ic'] : 1;
   $search_range = isset($_GET['search_range']) ?  $_GET['search_range'] : 0;
   $search_part = isset($_GET['search_part']) ?  $_GET['search_part'] : 0;
   $time_start = microtime(true);

   list($i_min, $i_max) = init_vars($search_part);

   $sign_before   = '[.,;“"«„!?(—–-]?\s?'; //здесь —, – и - это разные тире (emdash, endash, hyphen)
   $link_mask     = '~~##~~';
   $link_mask_re  = '(?:<\*{3}\d+?>)?';
   $sign_after    = '\s?'.$link_mask.'[.,;’”"»“!?)—–-]*?\s?';
   $_em           = '(?:<\/em>)?'.$link_mask;

   $text = trim(preg_replace(['/[\\\\<>()\[\]]/u', '/([*+])+/u'],['', '$1'], $text));//Чистим текст запроса от лишнего
   if ($text == ''||$text == '*'||$text == '+') end_search(0, 0, '');                //Завершаем поиск, если строка поиска пустая или только * или +
   $text = preg_replace('/(?<=\s|^)\W+(?=\s|$)\s?|[^\s\w\*\+]/u', '', $text);        //Убираем любые символы, кроме пробела и (* или +) в слове
   if (trim($text) == false ) end_search(0, 0, '');
   //в поиске реализованы спецсимволы-маски:
   //* - любое количество букв (от нуля и больше)
   //+ - любое количество букв (от единицы и больше)
   //цифры ищутся только по тексту (номер параграфа исключается)
   $pattern = '/(\\b' . preg_replace(['/\*/u', '/\+/u', '/\s+/u'],
                                       ['\\w*', '\\w+', '\\b|\\b'],
                                       trim($text)) . '\\b)/u';   //Заменяем * и + на спецстроку, пробел на ИЛИ
   if (is_array($pattern_ar) == false) $pattern_ar[] = $pattern;
   $i_mode = ($ic) ? 'i' : '';
   if ($search_range > 0) {
      $replace = '<mark>$1</mark>';
      $matched_lines = preg_filter($pattern.$i_mode, $replace, file($textdir . "/toc.html"));
      foreach($matched_lines as $line) {
         $par_count++;
         $match_count++;
         $matches[] = "<span class='hit'>[".$par_count."]&nbsp;</span>".$line;
      }
   }
   if ($search_range != 2) {
      for ($i = $i_min; $i <= $i_max; $i++) {
         $filename = sprintf($textdir . "/p%03d.html", $i);
         $lines = file($filename);
         foreach($lines as $line) {
            preg_match('/^<p>(.*?a>)/u', $line, $ref);
            $mask = [];
            $ref_a_total = preg_match_all('/<a\shref.*?a>|<a\sclass.*?a>|<figure.*?figure>|<sup.*?sup>/u', $line, $all_ref);  //Запоминаем все ссылки из текста
            for ($r = 0; $r < $ref_a_total; $r++)
               $mask[] = '<***'.$r.'>';                                          //Уникальная маска для каждой ссылки
            $line = preg_replace(['/^<h4>.*\\\\n/m','/^.*?a>/u'], '', $line);    //Убираем заголовки, номера абзацев
            if ($ref_a_total) $line = str_replace($all_ref[0], $mask, $line);    //Убираем ссылки
            $p_count = 0;
            foreach ($pattern_ar as $pattern) {
               $line = preg_replace_callback($pattern.$i_mode, 'text_replace', $line, -1, $count);
               $match_count += $count;
               $p_count += $count;                                               //Подсчитываем вхождение всех слов в абзаце
            }
            if ($p_count > 0) {
               $par_count++;
               if ($ref_a_total) $line = str_replace($mask, $all_ref[0], $line); //Возвращаем ссылки обратно
               $matches[] = "<p><span class='hit'>[".$p_count."/".$par_count."]&nbsp;</span>".$ref[1].$line;
            }
         }
      }
   }
   $matches[] = sprintf("%.4f s", microtime(true) - $time_start);
}

end_search($par_count, $match_count, $matches);

?>
