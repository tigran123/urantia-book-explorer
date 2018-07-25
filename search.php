<?php
//первоначальная установка переменных
function init_vars($search_part) {
   $ret_vars = [];
   switch ($search_part) {
      case 0:
         $ret_vars = [0, 196];
         break;
      case 1:
         $ret_vars = [0, 31];
         break;
      case 2:
         $ret_vars = [32, 56];
         break;
      case 3:
         $ret_vars = [57, 119];
         break;
      case 4:
         $ret_vars = [120, 196];
         break;
   }
   return $ret_vars;
}

//завершаем скрипт
function end_search($par_count, $match_count, $matches, $text) {
   //или здесь обрезать текст из matches перебирая все значения этого массиа 2018-06-30
   $json = ['par_count' => $par_count, 'match_count' => $match_count, 'matches' => $matches, 'text' => $text];
   echo json_encode($json);
   flush();
   exit;
}

//формируем разную строку вывода результата поиска в зависимости от установленного флажка настроек "shortcontext"
function output_line($line) {
   $shortcontext = 1;
   if (isset($_COOKIE['shortcontext']))
      $shortcontext = $_COOKIE['shortcontext'];
   if ($shortcontext == 1) {
      preg_match_all('/(?:\\b|^)(.{0,40})(<mark>.*<\/mark>)(.{0,40})(?:\\b|$)(.?)/u',$line,$shortline,PREG_SET_ORDER);
      $start_line=$shortline[0][1];
      $end_line=$shortline[0][3];
      $finish_symb=$shortline[0][4];
      $startdots=" ... ";
      $enddots=" ... ";
      if (trim($start_line) == null || strpos($line,$start_line) == 1 ) $startdots=" ";
      if ($end_line == null || $finish_symb == null ) $enddots="";
      $outputline[] = $startdots;
      $outputline[] = $shortline[0][1].$shortline[0][2].$shortline[0][3];
      $outputline[] = $enddots;
   } else {
      $outputline[] = "";
      $outputline[] = $line;
      $outputline[] = "";
   }
   return $outputline;
}

//формируем разную строку замены в зависимости от того, попал ли в результат тэг </i>
function text_replace($matches) {
   $m1 = $matches[1];
   $m2 = isset($matches[2]) ?  $matches[2] : '';
   if (stristr($m1,'</i>') != false && stristr($m1,'<i>') == false)
      return '</i><mark><i>'.$m1.'</mark>'.$m2;
   else
      return '<mark>'.$m1.'</mark>'.$m2;
}

//заменяем символы маски на регэкс
function mask2regexp($text) {
   $search = ["*",                                   //Заменяем символ * (любые символы) на спецстроку
              "+",                                   //Заменяем символ + (любые символы, как минимум один) на спецстроку
              "?"];                                  //Заменяем символ ? (любой один символ) на спецстроку;
   $replace = ['[\w\-]*',                            //Добавил \-, чтобы можно было найти сдвоенные слова через дефис
               '[\w\-]+',
               '\w'];
   return str_replace($search, $replace, $text);
}

//изменяем строку если нужно в нижний регистр
function str_i($text, $ic) {
   return ($ic) ? strtolower($text) : $text;
}
?>
