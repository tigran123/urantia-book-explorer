<?php
ini_set('memory_limit','300M');
system("rm -rf html ; mkdir html");

for ($i = 0; $i <= 196; $i++) {
   switch ($i) {
      case 5:
      case 19:
         $url = sprintf("http://urantia.me/%03d-3", $i);
         break;
      case 88:
         $url = sprintf("http://urantia.me/%03d-2-2", $i);
         break;
      default:
         $url = sprintf("http://urantia.me/%03d-2", $i);
         break;
   }
   printf("Retrieving $url: ");
   $text = file_get_contents($url);
   if ($text == FALSE) {
      printf("ERROR: failed to retrieve the page: ".$url);
      exit(1);
   } else {
       file_put_contents(sprintf("html/p%03d.html", $i), $text);
       printf("Done\n");
   }
}
?>
