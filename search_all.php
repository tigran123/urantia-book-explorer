<?php
require('search.php');
ini_set('memory_limit','300M');
header("Content-Type: text/html; charset=utf-8");

$pattern_ar = [];
$matches = [];
$par_count = 0;
$match_count = 0;
$text = $_GET['text'];
if (isset($text)) {
   $mod_idx = isset($_GET['mod_idx']) ?  $_GET['mod_idx'] : 0;
   $textdir = "text/" . $mod_idx;
   $ic = isset($_GET['ic']) ?  $_GET['ic'] : 1;
   $text = str_i($text,$ic);
   $search_range = isset($_GET['search_range']) ?  $_GET['search_range'] : 0;
   $search_part = isset($_GET['search_part']) ?  $_GET['search_part'] : 0;
   $time_start = microtime(true);
   list($i_min, $i_max) = init_vars($search_part);

   $re = '/\(?([\w*+]+-?[\w*+]*)\s+([\/@]{1,2})(?:\[(\d+),(\d+)\]|(\d+))\s+([\w*+]+-?[\w*+]*)\)?/u';       //Выделение запроса о расстоянии между словами
   preg_match_all($re, $text, $words_dist_req, PREG_PATTERN_ORDER, 0);           //Запоминаем запросы о расстоянии
   $text = str_replace($words_dist_req[0], '', trim($text));                     //Очищаем их из основного запроса

   $text = trim(preg_replace(['/[\\\\<>()\[\]]/u', '/([*+])+/u'],['', '$1'], $text));//Чистим текст запроса от лишнего
   if (($text == ''||$text == '*'||$text == '+') && empty($words_dist_req[0]) == true ) end_search(0, 0, '');                //Завершаем поиск, если строка поиска пустая или только * или +
   $text = preg_replace('/(?<=\s|^)\W+(?=\s|$)\s?|[^\s\w\*\+\-]/u', '', $text);      //Убираем любые символы, кроме пробела, *, + или - в слове
   if (trim($text) == false && empty($words_dist_req) == true ) end_search(0, 0, '');
   //в поиске реализованы спецсимволы-маски:
   //* - любое количество букв (от нуля и больше)
   //+ - любое количество букв (от единицы и больше)
   //цифры ищутся только по тексту (номер параграфа исключается)
   $text = mask2regexp(trim($text));
   $pattern_ar          = preg_split('/\s+/',trim($text));                       //Создаем из строки поиска массив из слов
   if (is_array($pattern_ar) == false) $pattern_ar = [];

   //Подготавливаем массив слов из запроса по расстоянию
   foreach ($words_dist_req[0] as $key => $words_dist) {
      $word1         = $words_dist_req[1][$key];
      $word2         = $words_dist_req[6][$key];
      $words_dist_req['Word1_AsRegex'][$key] = 0; //Флаг - искать слово1 как регэкс (1) или просто (0)
      $words_dist_req['Word2_AsRegex'][$key] = 0; //Флаг - искать слово2 как регэкс (1) или просто (0)
      if (strpos($word1,"*") !== false || strpos($word1,"+") !== false) {
         $words_dist_req[1][$key] = mask2regexp($word1);
         $words_dist_req['Word1_AsRegex'][$key] = 1;
      }
      if (strpos($word2,"*") !== false || strpos($word2,"+") !== false) {
         $words_dist_req[6][$key] = mask2regexp($word2);
         $words_dist_req['Word2_AsRegex'][$key] = 1;
      }
      $distances      = $words_dist_req[5][$key];
      //Дистанция - От и До
      $words_dist_req[3][$key] = $distances != null ?  $distances : $words_dist_req[3][$key];
      $words_dist_req[4][$key] = $distances != null ?  $distances : $words_dist_req[4][$key];
      //признак - ищем в пределах абзаца (0) или строки (1)
      $words_dist_req['InSentence'][$key] = strlen($words_dist_req[2][$key]) == 1 ?  0 : 1;
      //признак - ищем без учета последовательности (0) или с последовательностью (1)
      $words_dist_req['WithOrder'][$key] = ($words_dist_req[2][$key]=='/'||$words_dist_req[2][$key]=='//' ) ?  0 : 1;

   }
   //Очищаем массив от пустых значений
   foreach ($pattern_ar as $key => $pattern) {
      if ($pattern=='') unset($pattern_ar[$key]);
   }

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
            $ref_a_total = preg_match_all('/<a\shref.*?a>|<a\sclass.*?a>|<figure.*?figure>|<sup.*?sup>|<\/?em>/u', $line, $all_ref);  //Запоминаем все ссылки из текста
            for ($r = 0; $r < $ref_a_total; $r++)
               $mask[] = '<***'.$r.'>';                                          //Уникальная маска для каждой ссылки
            $line = preg_replace(['/^<h4>.*\\\\n/m','/^.*?a>/u','/<\/?b>/u'], '', $line);    //Убираем заголовки, номера абзацев, жирные буквы
            if ($ref_a_total) $line = str_replace($all_ref[0], $mask, $line);    //Убираем ссылки
            $finded_words = [];
            foreach ($pattern_ar as $pattern) {                                  //а попадают ли слова в абзац?
               if (preg_match_all('/\\b' .$pattern. '\\b/u'.$i_mode, str_i($line,$ic), $slova) == 0) continue 2; //Если слово из запроса не найдено, то ищем следующий абзац
               $finded_words = array_merge($finded_words,$slova[0]);                                             
            }
            $finded_words = array_unique($finded_words);

            //Преобразуем абзац в массив слов
            $line1 = $line;
            preg_match_all('/\w+-?\w*\b(?!>)/u', str_i($line1,$ic), $words_ar1);         //Создаем массив из слов. Массив вспомогательный. Нужен для нахождения номера по порядку слова в абзаце
            preg_match_all('/\w+-?\w*\b(?!>)/u', $line1, $words_ar, PREG_OFFSET_CAPTURE);//Создаем массив из подмассивов: слово и его начальная позиция в предложении

// Планируемые выражения дистанции между словами в поисковой строке:
// /[N,M] - в пределах абзаца (укороченно /N = /[N,N])
// //[N,M]- в пределах предложения
// @[N,M] - в пределах абзаца, с учетом последовательности
// @@[N,M]- в пределах предложения, с учетом последовательности
            //Массивы - позиции исследуемых слов word1 и word2 в абзаце
            $keys1 = [];
            $keys2 = [];
            foreach ($words_dist_req[0] as $key => $words_dist) {
               $word1              = $words_dist_req[1][$key];
               $word2              = $words_dist_req[6][$key];
               $distances_from      = $words_dist_req[3][$key];
               $distances_to        = $words_dist_req[4][$key];
               $search_inSentence  = $words_dist_req['InSentence'][$key];
               $distant_with_order = $words_dist_req['WithOrder'][$key];
               
               //Определяем, а есть ли вообще слова word1 и word2 в абзаце
               if (preg_match_all('/\\b' .$word1. '\\b/u'.$i_mode, str_i($line1,$ic), $re_words1) == 0) continue 2;
               $re_words1 = array_unique($re_words1[0]);
               if (preg_match_all('/\\b' .$word2. '\\b/u'.$i_mode, str_i($line1,$ic), $re_words2) == 0) continue 2;
               $re_words2 = array_unique($re_words2[0]);
               foreach ($re_words1 as $word1) {
                  $keys1 = array_merge($keys1, array_keys($words_ar1[0] , $word1)); //Делаем выборку номеров слов по искомому первому //или попробовать array_merge()
               }
               foreach ($re_words2 as $word2) {
                  $keys2 = array_merge($keys2, array_keys($words_ar1[0] , $word2)); //Делаем выборку номеров слов по искомому первому //или попробовать array_merge()
               }
            }
            $dist_ar = [];
            //Собственно основные условия нахождения по расстоянию - с учетом последовательности и слов и без
            foreach ($keys1 as $key1) {
               foreach ($keys2 as $key2) {
                  if ($distant_with_order == 1) {
                     if (($key2-$key1) >= $distances_from && ($key2-$key1) <= $distances_to) $dist_ar[] = [$key1, $key2];  //если не нашли по расстоянию - ищем в следующем абзаце
                  }
                  else {
                     if (abs($key2-$key1) >= $distances_from && abs($key2-$key1) <= $distances_to) $dist_ar[] = [$key1, $key2]; //если не нашли по расстоянию - ищем в следующем абзаце
                  }
               }
            }
            $word_mark = []; //Основной массив слов, которые будут помечаться маркером
            //1. Заполняем из найденных в абзаце по расстоянию
            foreach ($dist_ar as $para) {
               $word1_i = $para[0];
               $word2_i = $para[1];
               $word1   = $words_ar[0][$word1_i][0];
               $word2   = $words_ar[0][$word2_i][0];
               $word1_p = $words_ar[0][$word1_i][1];//Позиция по номеру слова
               $word2_p = $words_ar[0][$word2_i][1];
               $word_mark[$word1_p] = $word1;
               $word_mark[$word2_p] = $word2;
            }
            if (count($word_mark) == 0 && count($dist_ar) != 0) continue; //А если ничего так и не нашли - идем к следующему абзацу

            //2. Заполняем из остальных слов запроса, найденных в абзаце 
            foreach ($finded_words as $word) {
               $keys = array_keys($words_ar1[0] , $word); //Делаем выборку номеров слов по искомому первому //или попробовать array_merge()
               foreach ($keys as $key) {
                  $word_p             = $words_ar[0][$key][1];//Позиция по номеру слова
                  $word_mark[$word_p] = $words_ar[0][$key][0];//Само слово-оригинал
               }
            }
            if (count($word_mark) == 0) continue; //А если ничего так и не нашли - идем к следующему абзацу
            $p_count = 0;
            krsort($word_mark);// ВАЖНО! сортируем и маркируем в обратном порядке, т.к. если маркировать сначала, то станут неправильными индексы начала слов
            foreach ($word_mark as $position => $word) {
               $line = substr_replace($line,"<mark>".$word."</mark>",$position,strlen($word));
               $match_count += 1;
               $p_count += 1;                                               //Подсчитываем вхождение всех слов в абзаце
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
