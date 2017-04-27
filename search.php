<?php
//первоначальная установка переменных
function init_vars($search_part) {
   $ret_vars = [];
   switch ($search_part) {
      case 0:
         $ret_vars['i_min'] = 0;
         $ret_vars['i_max'] = 196;
         break;
      case 1:
         $ret_vars['i_min'] = 0;
         $ret_vars['i_max'] = 31;
         break;
      case 2:
         $ret_vars['i_min'] = 32;
         $ret_vars['i_max'] = 56;
         break;
      case 3:
         $ret_vars['i_min'] = 57;
         $ret_vars['i_max'] = 119;
         break;
      case 4:
         $ret_vars['i_min'] = 120;
         $ret_vars['i_max'] = 196;
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
   if (stristr($m1,'</em>') != false && stristr($m1,'<em>') == false)
      return '</em><mark><em>'.$m1.'</mark>'.$m2;
   else
      return '<mark>'.$m1.'</mark>'.$m2;
}
?>
