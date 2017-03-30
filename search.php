<?php
ini_set('memory_limit','300M');
header("Content-Type: text/html; charset=utf-8");

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
   $sign_before   ='[.,;“"«„!?(—–-]?\s?'; //здесь —, – и - это разные тире (emdash, endash, hyphen)
   $sign_after    ='\s?[.,;’”"»“!?)—–-]*?\s?';
   $_em           = '(?:<\/em>)?';

   if (preg_match('/(\d{1,3}):(\d{1,2})\.(\d{1,3})/', $text, $ref)) { //Recognise SRT ref 'Paper:Section.Paragraph'
      $filename = sprintf($textdir . "/p%03d.html", $ref[1]);
      $matched_lines = preg_grep('/<sup>'.$ref[1].':'.$ref[2].'.'.$ref[3].'<\/sup>/u', file($filename));
      foreach($matched_lines as $line) {
         $matches[] = $line;
         $match_count++;
         $par_count++;
      }
   } else {
      $time_start = microtime(true);
      $text = preg_replace('/[\<>()\[\]]/u','', $text); //экранируем текст запроса
      //в поиске реализованы спецсимволы-маски:
      //* - любое количество букв (от нуля и больше)
      //+ - любое количество букв (от единицы и больше)
      //цифры ищутся только по тексту (номер параграфа исключается)

      $search = ["/([.?])/u",                       //Экранируем точку и вопрос, и возможно обрамленные тегом
                 "/([,;!—–-]|[^?]:)/u",             //Заменяем знаки препинания на возможно обрамленные тегом. Отдельно выделил : с предпроверкой НЕ впередистоящего ?
                 "/([^\w])-/u",                     //Заменяем минус на символ длинного тире
                 "/\'/u",                           //Заменяем одиночную кавычку на символ апострофа
                 "/\*/u",                           //Заменяем символ * на спецстроку
                 "/\+/u",                           //Заменяем символ + на спецстроку
                 "/([*+]?\b(?!\w+>)\w+\b[*+]?)/u",  //Заменяем все слова в строке поиска, в том числе с маской *, на обрамленные в тэг <em> и возможные знаки препинания
                 "/\//u",                           //Заменяем слеш на экранированный с условием, что это не закрывающий тэг
                 "/zzddzz/u",                       //Заменяем спецстроку на любое количество словесных символов
                 "/ppddpp/u"];                      //Заменяем спецстроку на любое количество словесных символов, как минимум один

      $replace = ["\\\\$1".$_em,
                  "$1".$_em,
                  "$1(?:[—–])",
                  "’?".$_em,
                  "zzddzz",
                  "ppddpp",
                  "(?:<em>)?".$sign_before."(?<!<)\b$1\b(?!>)".$sign_after.$_em,
                  "(?<!<)\\\\/",
                  "\\\\w*",
                  "\\\\w+"];
      $pattern = '(' . preg_replace ($search, $replace, $text) . ')';

      //убираем первое вхождение знаков, чтобы не выделялись предшествующие пробелы, запятые и пр. символы...
      $pattern = preg_replace('/\[\.,;“"«„!\?\(—–-\]\?\\\\s\?/u', '', $pattern, 1);
      $pattern = str_replace($sign_after.$_em.')',')('.$sign_after.$_em.')', $pattern);//переставляем скобку левее
      $pattern = '/' . $pattern . '/u';
      if ($ic) $pattern .= 'i';
      if ($search_range > 0) {
         $replace = '<mark>$1</mark>';
         $matched_lines = preg_filter($pattern, $replace, file($textdir . "/toc.html"));
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
               $ref_a_match = preg_match('/<a\shref.*?a>/u', $line, $ref_a); //Запоминаем ссылку
               //Убираем заголовки, номера абзацев и ссылки
               $line = preg_replace(['/^<h4>.*\\\\n/m','/^.*?a>/u','/<a\shref.*?a>/u'], ['','','<***>'], $line);
               $matched_line = preg_replace_callback($pattern, 'text_replace', $line, -1, $count);
               if ($count > 0) {
                  $par_count++;
                  if($ref_a_match) $matched_line = str_replace('<***>',$ref_a[0], $matched_line); //Возвращаем ссылку обратно
                  $matches[] = "<p><span class='hit'>[".$count."/".$par_count."]&nbsp;</span>".$ref[1].$matched_line;
                  $match_count += $count;
               }
            }
         }
      }
      $matches[] = sprintf("%.4f s", microtime(true) - $time_start);
   }
}

$json = ['par_count' => $par_count, 'match_count' => $match_count, 'matches' => $matches];
echo json_encode($json);
flush();

//формируем разную строку замены в зависимости от того, попал ли в результат тэг </em>
function text_replace($matches) {
  if (stristr($matches[1],'</em>') != false && stristr($matches[1],'<em>') == false)
     return '</em><mark><em>'.$matches[1].'</mark>'.$matches[2];
  else
     return '<mark>'.$matches[1].'</mark>'.$matches[2];
}
?>
