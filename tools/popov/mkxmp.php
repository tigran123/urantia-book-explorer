<?php
ini_set('memory_limit','300M');
system("rm -rf exemplars ; mkdir -p exemplars/21");

$textp = '/^(?:<\/span>)?(?:<span style="color: #000000;">)?((?:<i>)?[\d«а-яА-Я[].*?)(?:<span .*?)?(?:<br\/>)?(?:<\/p>)?$/u';

$headp1 = '/^<h5 style="text-align: left;"><span id="[^"]*">(?:<b>|<strong>)(.*)<span class="" style="display:block;clear:both;height: 0px;padding-top: 50px;border-top-width:0px;border-bottom-width:0px;"><\/span>(<\/i>)?(?:<\/b>|<\/strong>)<\/span><\/h5>/u';

$headp2 = '/^<h5[^>]*><span[^>]*>(?:<b>|<strong>)(.*) *<\/b><\/span><\/h5>/u';

for ($i = 0; $i <= 196; $i++) {
   $filename = sprintf("html/p%03d.html", $i);
   $ofilename = sprintf("exemplars/21/p%03d.txt", $i);
   $lines = file($filename);
   $olines = [];
   if ($lines == FALSE) continue;
   $linenum = 1;
   foreach ($lines as $line) {
      if (preg_match($textp, $line, $matches) || preg_match($headp1, $line, $matches) || preg_match($headp2, $line, $matches)) {
         $clean = convert_tags($matches[1]);
         if (isset($matches[2]))
            $olines[] = '['.$linenum.'] '.$clean.$matches[2].PHP_EOL;
         else
            $olines[] = '['.$linenum.'] '.$clean.PHP_EOL;
         $linenum++;
      }
   }
   file_put_contents($ofilename, $olines);
}

function convert_tags($text) {
   $search = ['<em>', '</em>'];
   $replace = ['<i>', '</i>'];
   return str_replace($search, $replace, $text);
}
?>
