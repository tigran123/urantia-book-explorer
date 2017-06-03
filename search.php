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
function end_search($par_count, $match_count, $matches) {
   $json = ['par_count' => $par_count, 'match_count' => $match_count, 'matches' => $matches];
   echo json_encode($json);
   flush();
   exit;
}

//формируем разную строку замены в зависимости от того, попал ли в результат тэг </em>
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
   $replace = ['\w*',
               '\w+',
               '\w'];
   return str_replace($search, $replace, $text);
}

//изменяем строку если нужно в нижний регистр
function str_i($text, $ic) {
   return ($ic) ? strtolower($text) : $text;
}
?>
