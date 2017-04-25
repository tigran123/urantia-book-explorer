<?php
include('search.php');
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
   $sign_before   = '[.,;“"«„!?(—–-]?\s?'; //здесь —, – и - это разные тире (emdash, endash, hyphen)
   $link_mask     = '~~##~~';                 //Это строка-подмена вместо следующего регулярного выражения
   $link_mask_re  = '(?:<\*{3}\d+?>)?';       //Шаблон замены линков
   $sign_after    = '\s?'.$link_mask.'[.,;’”"»“!?)—–-]*?\s?';
   $_em           = '(?:<\/em>)?'.$link_mask;

   $text = trim(preg_replace(['/[\\\\<>()\[\]]/u', '/([*+])+/u'],['', '$1'], $text));//Чистим текст запроса от лишнего
   if ($text == ''||$text == '*'||$text == '+') end_search(0, 0, '');                //Завершаем поиск, если строка поиска пустая или только * или +
   //в поиске реализованы спецсимволы-маски:
   //* - любое количество букв (от нуля и больше)
   //+ - любое количество букв (от единицы и больше)
   //цифры ищутся только по тексту (номер параграфа исключается)
   $search = ["/([.?])/u",                     //Экранируем точку и вопрос, и возможно обрамленные тегом
            "/([,;!—–-]|[^?]:)/u",             //Заменяем знаки препинания на возможно обрамленные тегом. Отдельно выделил : с предпроверкой НЕ впередистоящего ?
            "/([^\w])-/u",                     //Заменяем минус на символ длинного тире
            "/\'/u",                           //Заменяем одиночную кавычку на символ апострофа
            "/\*/u",                           //Заменяем символ * на спецстроку
            "/\+/u",                           //Заменяем символ + на спецстроку
            "/([*+]?\b(?!\w+>)\w+\b[*+]?)/u",  //Заменяем все слова в строке поиска, в том числе с маской *, на обрамленные в тэг <em> и возможные знаки препинания
            "/(?<![<\\\\])\//u"];              //Заменяем слеш на экранированный с условием, что это не закрывающий тэг

   $replace = ["\\\\$1".$_em,
               "$1".$_em,
               "$1(?:[—–])",
               "’?".$_em,
               "zzddzz",
               "ppddpp",
               "(?:<em>)?".$sign_before."(?<!<)\b$1\b(?!>)".$sign_after.$_em,
               "(?<![<\\\\\\\\])\\\\/"];
   $pattern = '(' . preg_replace($search, $replace, $text) . ')';

   //убираем первое вхождение знаков, чтобы не выделялись предшествующие пробелы, запятые и пр. символы...
   $pattern = preg_replace('/\[\.,;“"«„!\?\(—–-\]\?\\\\s\?/u', '', $pattern, 1);
   $search = ['zzddzz',                                   //Заменяем спецстроку на любое количество словесных символов
               'ppddpp',                                  //Заменяем спецстроку на любое количество словесных символов, как минимум один
               $sign_after.$_em.')',                      //Переставляем скобку левее
               $link_mask];                               //Заменяем маску ссылки на регулярное выражение
   $replace = ['\\w*',
               '\\w+',
               ')('.$sign_after.$_em.')',
               $link_mask_re];
   $pattern = '/' . str_replace($search, $replace, $pattern) . '/u'; 
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