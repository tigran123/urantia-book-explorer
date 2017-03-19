<?php
ini_set('memory_limit','300M');
header("Content-Type: text/html; charset=utf-8");

$text = isset($_GET['text']) ?  $_GET['text'] : '';
$text = preg_replace('/[\/<>()\[\]]/u','', $text); //экранируем текст запроса
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
   $sign_before   ='[.,;“"«„!?(—–-]?\s?'; //здесь – и - это разные тире: первое длинное, второе короткое
   $sign_after    ='\s?[.,;’”"»“!?)—–-]*?\s?';
   $_em           = '(?:<\/em>)?';
   
   //в поиске реализованы спецсимволы-маски:
   //* - любое количество букв (от нуля и больше)
   //+ - любое количество букв (от единицы и больше)
   //цифры ищутся только по тексту (номер параграфа исключается)
   
   $search = array ("/([.?])/u",  					//Экранируем точку и вопрос, и возможно обрамленные тегом
                   "/([,;!—–-]|[^?]:)/u",				//Заменяем знаки препинания на возможно обрамленные тегом. Отдельно выделил : с предпроверкой НЕ впередистоящего ?
                   "/([^\w])-/u",					//Заменяем минус на символ длинного тире
                   "/\'/u",						//Заменяем одиночную кавычку на символ апострофа
                   "/\*/u",						// делаем замену символа * на спецстроку
                   "/\+/u",						// делаем замену символа + на спецстроку
                   "/([*+]?\b(?!\w+>)\w+\b[*+]?)/u",			//Заменяем все слова в строке поиска, в том числе с маской *, на обрамленные в тэг <em> и возможные знаки препинания
                   "/zzzz/u",						//заменяем спецстроку на любое количество словесных символов
                   "/pppp/u"); 						//заменяем спецстроку на любое количество словесных символов, как минимум один	

   $replace = array ("\\\\$1".$_em,
                     "$1".$_em,
                     "$1(?:[—–])",
                     "’?".$_em,
                     "zzzz",
                     "pppp",
                     "(?:<em>)?".$sign_before."\b$1\b".$sign_after.$_em,
                     "\\\\w*",
                     "\\\\w+");
   $pattern 	 = '(' . preg_replace ($search, $replace, $text) . ')';

   $search_range = isset($_GET['search_range']) ?  $_GET['search_range'] : 0;
   //убираем первое вхождение знаков, чтобы не выделялись предшествующие пробелы, запятые и пр. символы...
   $pattern = preg_replace('/\[\.,;“"«„!\?\(—–-\]\?\\\\s\?/u', '', $pattern, 1); //preg_quote
   //убираем последнее вхождение знаков, чтобы не выделялись завершающие пробелы, запятые и пр. символы...

   $pattern = str_replace($sign_after.$_em.')',')('.$sign_after.$_em.')', $pattern);//переставляем скобку левее
   $pattern = '/' . $pattern . '/u';

   if ($ic) $pattern .= 'i';
   $textdir = "text/" . $mod_idx;
   $time_start = microtime(true);
   $matches = [];
   $total = 0;
   if ($search_range > 0) {
      $replace = '<span style="background-color:yellow;">$1</span>';
      $matched_lines = preg_filter($pattern, $replace, file($textdir . "/toc.html"));
      foreach($matched_lines as $line) {
         $matches[] = $line;
         $total++;
      }
   }
   if ($search_range != 2) {
      for ($i = $i_min; $i <= $i_max; $i++) {
         $filename = sprintf($textdir . "/p%03d.html", $i);
         $lines = file($filename);
         foreach($lines as $line) {
            $count = 0;
	    preg_match('/^.*?a>/u', $line, $number_of_item);
	    $line = preg_replace(['/^<h4>.*\n/m','/^.*?a>/u'], '', $line); //убираем заголовки и номера абзацев, чтобы в них не искало
            $matched_line = preg_replace_callback ($pattern, 'text_replace', $line, -1, $count);
            if ($count > 0) {
               $matches[] = $number_of_item[0].$matched_line;
               $total++;
            }
         }
      }
   }
   $matches[] = sprintf("%.4f seconds", microtime(true) - $time_start);
   $json = ['total' => $total, 'matches' => $matches];
   echo json_encode($json);
   flush();
}

//формируем разную строку замены в зависимости от того, попал ли в результат тэг </em>
function text_replace($matches){
  if (stristr($matches[1],'</em>')<>false && stristr($matches[1],'<em>')==false) {
    $replace='</em><span style="background-color:yellow;"><em>'.$matches[1].'</span>'.$matches[2];
  }
  else{
     $replace='<span style="background-color:yellow;">'.$matches[1].'</span>'.$matches[2];
  }
  return $replace;
}
?>
