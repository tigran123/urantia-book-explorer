<?php
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
