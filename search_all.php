<?php
require('search.php');
ini_set('memory_limit','300M');
header("Content-Type: text/html; charset=utf-8");

$pattern_ar  = [];

function search_any($search_in_toc=0) {
   global $textdir,$matches,$i_min,$i_max,$words_dist_req,$pattern_ar,$ic,$par_count,$match_count;
   $i_mode = ($ic) ? 'i' : '';
   if ($search_in_toc == 1) {
      $i_min=1;$i_max=1;
   }
   for ($i = $i_min; $i <= $i_max; $i++) {
      if ($search_in_toc == 1) {
         $filename = $textdir."/toc.html";
      } else {
         $filename = sprintf($textdir . "/p%03d.html", $i);
      }
      $lines = file($filename);
      foreach($lines as $line) {
         if ($search_in_toc == 1) {
            preg_match('/^\s*?<li.*?(<a.*?>)/u', $line, $ref);
            if (preg_match('/^\s*?<li.*?<a.*?>(.*)<\/a>/', $line, $textInline)===1){
               $line=$textInline[1];
            }
         } else {
            preg_match('/^<p>(.*?a>)/u', $line, $ref);
         }
         $mask = [];
         $ref_a_total = preg_match_all('/<a\shref.*?a>|<a\sclass.*?a>|<figure.*?figure>|<sup.*?sup>/u', $line, $all_ref);  //Запоминаем все ссылки из текста
         for ($r = 0; $r < $ref_a_total; $r++)
            $mask[] = '<***'.$r.'>';                                          //Уникальная маска для каждой ссылки
         $line = preg_replace(['/^<h4>.*\\\\n/m','/^.*?a>/u','/<\/?b>/u'], '', $line);    //Убираем заголовки, номера абзацев, жирные буквы
         if ($ref_a_total) $line = str_replace($all_ref[0], $mask, $line);    //Убираем ссылки
         $finded_words = [];
         foreach ($pattern_ar as $pattern) {                                  //а попадают ли слова в абзац?
            if (strpos($pattern, '-') === 0) {                                //Это слово-исключение, т.к. начинается с минуса -. Т.е. его не должно быть в тексте
               if (preg_match_all('/\\b' .substr($pattern, 1). '(?!\w)/u'.$i_mode, str_i($line,$ic), $words_for_pattern) != 0) continue 2; //Если слово из запроса найдено, то ищем следующий абзац
            } else {
               $res_search = preg_match_all('/\\b' .$pattern. '(?!\w)/u'.$i_mode, str_i($line,$ic), $words_for_pattern);
               if ($res_search == 0) continue 2; //Если слово из запроса не найдено, то ищем следующий абзац
            }
            $finded_words = array_merge($finded_words,$words_for_pattern[0]);
         }
         $finded_words = array_unique($finded_words);

         //Преобразуем абзац в массив слов
         $line1 = $line;
         preg_match_all('/\w+-?\w*\b-?(?!>)/u', str_i($line1,$ic), $words_ar1);         //Создаем массив из слов. Массив вспомогательный. Нужен для нахождения номера по порядку слова в абзаце
         preg_match_all('/\w+-?\w*\b-?(?!>)/u', $line1, $words_ar, PREG_OFFSET_CAPTURE);//Создаем массив из подмассивов: слово и его начальная позиция в предложении
         //"\b-?"  - добавил возможность находить слова с "-" в конце, например, one- или two-
         //"(?!>)" - проверяем последний символ, не конец ли это тэга?

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
            if (preg_match_all('/\\b' .$word1. '(?!\w)/u'.$i_mode, str_i($line1,$ic), $re_words1) == 0) continue 2;
            $re_words1 = array_unique($re_words1[0]);
            if (preg_match_all('/\\b' .$word2. '(?!\w)/u'.$i_mode, str_i($line1,$ic), $re_words2) == 0) continue 2;
            $re_words2 = array_unique($re_words2[0]);
            foreach ($re_words1 as $word1) {
               $keys1 = array_merge($keys1, array_keys($words_ar1[0] , $word1)); //Делаем выборку номеров слов по искомому первому //или попробовать array_merge()
            }
            foreach ($re_words2 as $word2) {
               $keys2 = array_merge($keys2, array_keys($words_ar1[0] , $word2)); //Делаем выборку номеров слов по искомому второму //или попробовать array_merge()
            }
         }
         $dist_ar = [];
         //Собственно основные условия нахождения по расстоянию - с учетом последовательности слов и без
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
            $outputline = output_line($line,$search_in_toc);
            if ($ref_a_total) $outputline[1] = str_replace($mask, $all_ref[0], $outputline[1]); //Возвращаем ссылки обратно
            $matches[]   = "<p><span class='hit'>[".$p_count."/".$par_count."]&nbsp;</span>".$ref[1].$outputline[0].$outputline[1].$outputline[2];//htmlentities($outputline)."<pre>".print_r($shortline, true)."</pre>"
         }
      }
   }
}

if (isset($text)) {
   $re = '/\(?([\w*+]+-?[\w*+]*)\s+(\|?(?:\<|\>))(?:(\d+),(\d+)|(\d+))\>\|?\s+([\w*+]+-?[\w*+]*)\)?/u';//Выделение запроса о расстоянии между словами
      //      (_______1________)   (______2_____)   (_3_) (_4_) (_5_)         (________6_______)
      //Слово1________|                   |           |     |     |                    |
      //Тип расстояния____________________|           |     |     |                    |
      //расстояние 1__________________________________|     |     |                    |
      //расстояние 2________________________________________|     |                    |
      //расстояние сокращенное____________________________________|                    |
      //Слово2_________________________________________________________________________|
   preg_match_all($re, $text, $words_dist_req, PREG_PATTERN_ORDER, 0);                                 //Запоминаем запросы о расстоянии
   $text = str_replace($words_dist_req[0], '', trim($text));                                           //Очищаем их из основного запроса

   $text = trim(preg_replace(['/[\\\\<>()\[\]]/u', '/([*+])+/u'],['', '$1'], $text));                  //Чистим текст запроса от лишнего
   if (($text == ''||$text == '*'||$text == '+') && empty($words_dist_req[0]) == true ) end_search(0, 0, '', $text); //Завершаем поиск, если строка поиска пустая или только * или +
   $text = preg_replace('/(?<=\s|^)[^\s\w\*\+]+(?=\s|$)\s?|[^\s\w\*\+\-\?]/u', '', $text);             //Убираем любые символы, кроме пробела, *, +, - или ? в слове
   if (trim($text) == false && empty($words_dist_req) == true ) end_search(0, 0, '', $text);
   //
   // В поиске реализованы спецсимволы-маски:
   //
   // * - любое количество букв (от нуля и больше)
   // + - любое количество букв (от единицы и больше)
   // ? - одна любая буква
   // - - минус в начале слова означает, что это слово не должно быть в тексте
   //
   // цифры ищутся только по тексту (номер параграфа исключается)
   //
   // В поисковой строке действуют операторы указания расстояния от N до M между словами:
   // Слово1 <N,M> Слово2 - в пределах абзаца (укороченно <N> = <1,N>)
   // Слово1 >N,M> Слово2 - в пределах абзаца, с учетом последовательности
   //
   // Планируются:
   // Слово1 |<N,M>| Слово2 - в пределах предложения
   // Слово1 |>N,M>| Слово2 - в пределах предложения, с учетом последовательности
   //
   $pat = mask2regexp(trim($text));
   $pattern_ar = preg_split('/\s+/',trim($pat));  //Создаем из строки поиска массив из слов
   if (is_array($pattern_ar) == false) $pattern_ar = [];

   //Подготавливаем массив слов из запроса по расстоянию
   foreach ($words_dist_req[0] as $key => $words_dist) {
      $word1 = $words_dist_req[1][$key];
      $word2 = $words_dist_req[6][$key];
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
      $distances = $words_dist_req[5][$key];
      //Дистанция - От и До
      $words_dist_req[3][$key] = $distances != null ?  1          : $words_dist_req[3][$key];
      $words_dist_req[4][$key] = $distances != null ?  $distances : $words_dist_req[4][$key];
      //признак - ищем в пределах абзаца (0) или строки (1)
      $words_dist_req['InSentence'][$key] = strlen($words_dist_req[2][$key]) == 1 ?  0 : 1;
      //признак - ищем без учета последовательности (0) или с последовательностью (1)
      $words_dist_req['WithOrder'][$key] = ($words_dist_req[2][$key]=='<'||$words_dist_req[2][$key]=='|<' ) ?  0 : 1;

   }
   //Очищаем массив от пустых значений
   foreach ($pattern_ar as $key => $pattern) {
      if ($pattern=='') unset($pattern_ar[$key]);
   }

   make_search("search_any");
}

end_search($par_count, $match_count, $matches, $text);

?>
