<?php
require('search.php');
ini_set('memory_limit','300M');
header("Content-Type: text/html; charset=utf-8");

$pattern_ar = '';

if (isset($text)) {
   $text = trim(preg_replace(['/[\\\\<>()\[\]]/u', '/([*+])+/u'],['', '$1'], $text));         //Чистим текст запроса от лишнего
   if ($text == ''||$text == '*'||$text == '+') end_search(0, 0, '', $text);                  //Завершаем поиск, если строка поиска пустая или только * или +
   $text = preg_replace('/(?<=\s|^)[^\s\w\*\+]+(?=\s|$)\s?|[^\s\w\*\+\-\?]/u', '', $text);    //Убираем любые символы, кроме пробела, *, +, - или ? в слове
   if (trim($text) == false) end_search(0, 0, '', $text);
   //
   // В поиске реализованы спецсимволы-маски:
   //
   // * - любое количество букв (от нуля и больше)
   // + - любое количество букв (от единицы и больше)
   // ? - одна любая буква
   //
   //цифры ищутся только по тексту (номер параграфа исключается)
   //
   $pattern = '/(\\b' . preg_replace(['/\*/u', '/\+/u', '/\?/u', '/\s+/u'],
                                       ['[\\w\\-]*', '[\\w\\-]+', '\\w', '(?!\\w)|\\b'],
                                       trim($text)) . '(?!\w))/u';   //Заменяем * и + на спецстроку, пробел на ИЛИ. Задней границей слова является "(?!\\w)"
   if (is_array($pattern_ar) == false) $pattern_ar = []; $pattern_ar[] = $pattern;
   //Очищаем массив от пустых значений
   foreach ($pattern_ar as $key => $pattern) {
      if ($pattern=='') unset($pattern_ar[$key]);
   }

   make_search();
}

end_search($par_count, $match_count, $matches, $text);

?>
