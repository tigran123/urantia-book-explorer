<?php
require('search.php');
ini_set('memory_limit','300M');
header("Content-Type: text/html; charset=utf-8");

if (isset($text)) {

   $sign_before   = '[.,;“"«„!?(—–-]?\s?';    //здесь —, – и - это разные тире (emdash, endash, hyphen)
   $link_mask     = '~~##~~';                 //Это строка-подмена вместо следующего регулярного выражения
   $link_mask_re  = '(?:<\*{3}\d+?>)?';       //Шаблон замены линков
   $sign_after    = '\s?'.$link_mask.'[.,;’”"»“!?)—–-]*?\s?';
   $_i           = '(?:<\/i>)?'.$link_mask;

   $text = trim(preg_replace(['/[<>()\[\]]|\\\\(?!d)/u', '/([*+])+/u'],['', '$1'], $text)); //Чистим текст запроса от лишнего
   if ($text == ''||$text == '*'||$text == '+') end_search(0, 0, '', $text);                //Завершаем поиск, если строка поиска пустая или только * или +
   //end_search(0,0,htmlentities($text));
   //в поиске реализованы спецсимволы-маски:
   //* - любое количество букв (от нуля и больше)
   //+ - любое количество букв (от единицы и больше)
   //цифры ищутся только по тексту (номер параграфа исключается)
   $search = ["/([.?])/u",                     //Экранируем точку и вопрос, и возможно обрамленные тегом
            "/([,;!—–-]|[^?]:)/u",             //Заменяем знаки препинания на возможно обрамленные тегом. Отдельно выделил : с предпроверкой НЕ впередистоящего ?
            "/([^\w])-/u",                     //Заменяем минус на символ длинного тире
            "/\'/u",                           //Заменяем одиночную кавычку на символ апострофа
            "/\\\\d\*/u",                      //Заменяем \d* на спецстроку
            "/\\\\d\+/u",                      //Заменяем \d+ на спецстроку
            "/\\\\d/u",                        //Заменяем \d на спецстроку
            "/\*/u",                           //Заменяем символ * на спецстроку
            "/\+/u",                           //Заменяем символ + на спецстроку
            "/([*+]?\b(?!\w+>)\w+\b[*+]?)/u",  //Заменяем все слова в строке поиска, в том числе с маской *, на обрамленные в тэг <i> и возможные знаки препинания
            "/(?<![<\\\\])\//u"];              //Заменяем слеш на экранированный с условием, что это не закрывающий тэг

   $replace = ["\\\\$1".$_i,
               "$1".$_i,
               "$1(?:[—–])",
               "’?".$_i,
               "ssddzzss",
               "ssddppss",
               "ssdddss",
               "zzddzz",
               "ppddpp",
               "(?:<i>)?".$sign_before."(?<!<)\b$1\b(?!>)".$sign_after.$_i,
               "(?<![<\\\\\\\\])\\\\/"];
   $pattern = '(' . preg_replace($search, $replace, $text) . ')';

   //убираем первое вхождение знаков, чтобы не выделялись предшествующие пробелы, запятые и пр. символы...
   $pattern = preg_replace('/\[\.,;“"«„!\?\(—–-\]\?\\\\s\?/u', '', $pattern, 1);
   $search = ['zzddzz',                                   //Заменяем спецстроку на любое количество словесных символов
               'ppddpp',                                  //Заменяем спецстроку на любое количество словесных символов, как минимум один
               'ssddzzss',                                //Заменяем спецстроку на \d*
               'ssddppss',                                //Заменяем спецстроку на \d+
               'ssdddss',                                 //Заменяем спецстроку на \d
               $sign_after.$_i.')',                       //Переставляем скобку левее

               $link_mask];                               //Заменяем маску ссылки на регулярное выражение
   $replace = ['\\w*',
               '\\w+',
               '\\d*',
               '\\d+',
               '\\d',
               ')('.$sign_after.$_i.')',
               $link_mask_re];
   $pattern = '/' . str_replace($search, $replace, $pattern) . '/u';
   $pattern_ar[] = $pattern;

   make_search();
}

end_search($par_count, $match_count, $matches, $text);

?>
