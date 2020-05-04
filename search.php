<?php
$matches     = [];
$par_count   = 0;
$match_count = 0;
$text        = $_GET['text'];

if (isset($text)) {
   $mod_idx      = isset($_GET['mod_idx']) ?  $_GET['mod_idx'] : 0;
   $textdir      = "text/" . $mod_idx;
   $ic           = isset($_GET['ic']) ?  $_GET['ic'] : 1;
   $text         = str_i($text,$ic);//было только в режиме search_all
   $search_part  = isset($_GET['search_part'])  ?  $_GET['search_part']  : 0;
   $search_range = isset($_GET['search_range']) ?  $_GET['search_range'] : 0;
   $time_start   = microtime(true);
   list($i_min, $i_max) = init_vars($search_part);
}

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

function search($search_in_toc=0) {
   global $textdir,$matches,$i_min,$i_max,$pattern_ar,$ic,$par_count,$match_count;
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
         $line = preg_replace(['/^<h4>.*\\\\n/m','/^.*?a>/u', '/<\/?b>/u'], '', $line);    //Убираем заголовки, номера абзацев
         if ($ref_a_total) $line = str_replace($all_ref[0], $mask, $line);    //Убираем ссылки
         $p_count = 0;
         foreach ($pattern_ar as $pattern) {
            $line = preg_replace_callback($pattern.$i_mode, 'text_replace', $line, -1, $count);
            $match_count += $count;
            $p_count += $count;                                               //Подсчитываем вхождение всех слов в абзаце
         }
         if ($p_count > 0) {
            $par_count++;
            $outputline = output_line($line,$search_in_toc);
            if ($ref_a_total) $outputline[1] = str_replace($mask, $all_ref[0], $outputline[1]); //Возвращаем ссылки обратно
            $matches[] = "<p><span class='hit'>[".$p_count."/".$par_count."]&nbsp;</span>".$ref[1].$outputline[0].$outputline[1].$outputline[2];
         }
      }
   }
}

function make_search($func_name="search") {
   global $search_range, $time_start, $matches;

   switch ($search_range) {
      case "0":
         $func_name();   // поиск по тексту
         break;
      case "1":
         $func_name();   // без break выполняется и следующий case
      case "2":
         $func_name(1);  // поиск по содержанию
   }

   $matches[] = sprintf("%.4f s", microtime(true) - $time_start);
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
function output_line($line, $full_context) {
   $shortcontext = 0;
   if (isset($_COOKIE['shortcontext']))
      $shortcontext = $_COOKIE['shortcontext'];

   $l = 40; //$shortcontext_lenght
   if (isset($_COOKIE['shortcontext_lenght']))
      $l = $_COOKIE['shortcontext_lenght'];

   if ($shortcontext != 0 && $full_context == 0) {
      $startdots  = " ... ";
      $enddots    = " ... ";
      if ($shortcontext == 1) {
         //Ограничиваем контекст результата одним предложением
         $startdots_  = "";
         $enddots_    = "";
         $res_line    = "";
         $cont_count  = preg_match_all('/([\.?!]?[”)]?(?:<[^>]*?>)?)([^\.\n!?]*?)(<mark>.*?<\/mark>)([^\.\n!?]*(?:[\.:!?]|$)[^\s\n]*)(.?)/u',$line,$shortline,PREG_SET_ORDER);
         $last_count  = $cont_count-1;
         for ($r = 0; $r < $cont_count; $r++) {
            $start_symb = $shortline[$r][1];
            $start_line = $shortline[$r][2];
            $mark_line  = $shortline[$r][3];
            $end_line   = $shortline[$r][4];
            $finish_symb= $shortline[$r][5];
            $startdots  = " ... ";
            $enddots    = " ... ... ... ";
            if ($start_symb == null ) $startdots="";
            if ($finish_symb == null ) $enddots="";
            if ($r === 0 && $startdots != null) {
               $startdots_= " ... ";
            }
            if ($r === $last_count && $enddots != null) {
               $enddots_= " ... "; $enddots = "";
            }
            $m_line = $start_line.$mark_line.$end_line;
            $res_line = $res_line.$m_line.$enddots.'&nbsp;';
         }
         $outputline[] = $startdots_;
         $outputline[] = $res_line;
         $outputline[] = $enddots_;
      } else {
         //Ограничиваем контекст результата L символами до и после найденного, так, чтобы начало и конец были красивыми (не знаки препинания, часть тэга и пр..)
         $startdots_  = " ... ";
         $enddots_    = " ... ";
         $res_line    = "";
         $cont_count  = preg_match_all('/(.?)([^\s\n]*?)([^>\/\.,–:;\s].{0,'.$l.'})?(<mark>.*?<\/mark>)(.{0,'.$l.'}[^<\/\.,–:;\s])?([^\s\n]*?)(?:[\s,:;]|$)(.?)/u',$line,$shortline,PREG_SET_ORDER);
         //                              (1_)(____2____)(____________3____________) (_______4________)(____________5____________) (____6____)             (7_)
         $last_count = $cont_count-1;
         for ($r = 0; $r < $cont_count; $r++) {
            $start_symb = $shortline[$r][1];//                                   |                      |                    |                  |
            $start_line = $shortline[$r][2].$shortline[$r][3];//                 |                      |                    |                  |
            $mark_line  = $shortline[$r][4];//___________________________________|                      |                    |                  |
            $end_line   = $shortline[$r][5].$shortline[$r][6];//________________________________________|____________________|                  |
            $finish_symb= $shortline[$r][7];//__________________________________________________________________________________________________|
         // $start_symb = $shortline[0][1];//                                   |                      |                    |                  |
         // $start_line = $shortline[0][2].$shortline[0][3];//                  |                      |                    |                  |
         // $mark_line  = $shortline[0][4];//___________________________________|                      |                    |                  |
         // $end_line   = $shortline[0][5].$shortline[0][6];//_________________________________________|____________________|                  |
         // $finish_symb= $shortline[0][7];//__________________________________________________________________________________________________|

            if ($finish_symb == null ) $enddots="";
            if (trim($start_line) == "") {
               $startdots = (strpos($line,$mark_line) == 1 ) ? " ": " ... ";
            }else{
               $startdots = (strpos($line,$start_line) == 1 ) ? " ": " ... ";
            }
            if ($finish_symb == null ) $enddots="";
            if ($r === 0) {
               $startdots_= $startdots; $startdots = "";
            }
            if ($r === $last_count ) {
               $enddots_= $enddots; $enddots = "";
            }
            $m_line = $start_line.$mark_line.$end_line;
            if (strpos($m_line,'<i>') != 0 ) {
               if (strpos($m_line,'</i>') == 0 ) {
                  $m_line = $m_line.'</i>';
               }
            }
            $res_line = $res_line.$startdots.$m_line.$enddots.'&nbsp;';
        }
        $outputline[] = $startdots_;
        $outputline[] = $res_line;
        $outputline[] = $enddots_;

      }

   } else {
      $outputline[] = "";
      $outputline[] = $line;
      $outputline[] = "";
   }
   return $outputline;
}

//формируем разную строку замены в зависимости от того, попал ли в результат тэг </?i>
function text_replace($matches) {
   $m1 = $matches[1];
   $m2 = isset($matches[2]) ?  $matches[2] : '';
   if (stristr($m1,'</i>') != false && stristr($m1,'<i>') == false)
      return '</i><mark><i>'.$m1.'</mark>'.$m2;
   else
      return '<mark>'.$m1.'</mark>'.$m2;
}

//заменяем символы маски на регэкс # TODO: 20200224 Al - перенести в search_all?
function mask2regexp($text) {
   $search = ["*",                                   //Заменяем символ * (любые символы) на спецстроку
              "+",                                   //Заменяем символ + (любые символы, как минимум один) на спецстроку
              "?"];                                  //Заменяем символ ? (любой один символ) на спецстроку;
   $replace = ['[\w’]*',
               '[\w’]+',
               '[\w’]'];
   return str_replace($search, $replace, $text);
}

//изменяем строку если нужно в нижний регистр # TODO: 20200228 Al - можно убрать
function str_i($text, $ic) {
   return ($ic) ? strtolower($text) : $text;
}
?>
