<?php
ini_set('memory_limit','300M');
system("rm -rf exemplars ; mkdir -p exemplars/21");

for ($i = 0; $i <= 196; $i++) {
   $filename = sprintf("html/p%03d.html", $i);
   $ofilename = sprintf("exemplars/21/p%03d.txt", $i);
   $lines = file($filename);
   $olines = [];
   if ($lines == FALSE) continue;
   $linenum = 1;
   foreach ($lines as $line) {
      if (preg_match('/^(?:<\/span>)?(?:<span style="color: #000000;">)?((?:<i>)?[\d«а-яА-Я[].*?)(?:<span .*?)?(?:<br\/>)?(?:<\/p>)?$/u', $line, $matches)) {
         $olines[] = '['.$linenum.'] '.$matches[1].PHP_EOL;
         $linenum++;
      } elseif (preg_match('/^<h5 style="text-align: left;"><span id="[^"]*">(?:<b>|<strong>)(.*)<span class="" style="display:block;clear:both;height: 0px;padding-top: 50px;border-top-width:0px;border-bottom-width:0px;"><\/span>(<\/i>)?(?:<\/b>|<\/strong>)<\/span><\/h5>/u', $line, $matches)) {
         if (isset($matches[2]))
            $olines[] = '['.$linenum.'] '.$matches[1].$matches[2].PHP_EOL;
         else
            $olines[] = '['.$linenum.'] '.$matches[1].PHP_EOL;
         $linenum++;
      }
   }
   file_put_contents($ofilename, $olines);
}
?>
