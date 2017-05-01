<?php
ini_set('memory_limit','300M');

$languages = ['English' => 0,
              'Russian' => 2,
              'Bulgarian' => 3,
              'German' => 4,
              'Dutch' => 6,
              'Estonian' => 7,
              'Finnish' => 8,
              'French' => 9,
              'Greek' => 10,
              'Hungarian' => 11,
              'Italian' => 12,
              'Korean' => 13,
              'Lithuanian' => 14,
              'Polish' =>  15,
              'Portuguese' => 16,
              'Romanian' => 17,
              'SpanishAmericas' => 18,
              'SpanishEuropean' => 19,
              'Swedish' => 20];

foreach ($languages as $lang => $id) {
   $tocfile = "text/".$id."/toc.html";
   $toclines = [];
   list($part1, $part1sub, $part2, $part2sub, $part3, $part3sub, $part4, $part4sub1, $part4sub2) = parse_parts($lang);
   $toclines[] = "<ul class='toc' id='toc".$id."'>".PHP_EOL;

   $toclines[] = "<li title='<i>".$part1sub."</i>'><a href=\".U0_0_1\"><b>I. ".$part1."</b></a>".PHP_EOL;
   $toclines[] = " <ul>".PHP_EOL;
   $toclines = array_merge($toclines, add_papers(0, 31, $lang));
   $toclines[] = " </ul>".PHP_EOL;
   $toclines[] = "</li>".PHP_EOL;

   $toclines[] = "<li title='<i>".$part2sub."</i>'><a href=\".U32_0_1\"><b>II. ".$part2."</b></a>".PHP_EOL;
   $toclines[] = " <ul>".PHP_EOL;
   $toclines = array_merge($toclines, add_papers(32, 56, $lang));
   $toclines[] = " </ul>".PHP_EOL;
   $toclines[] = "</li>".PHP_EOL;

   $toclines[] = "<li title='<i>".$part3sub."</i>'><a href=\".U57_0_1\"><b>III. ".$part3."</b></a>".PHP_EOL;
   $toclines[] = " <ul>".PHP_EOL;
   $toclines = array_merge($toclines, add_papers(57, 119, $lang));
   $toclines[] = " </ul>".PHP_EOL;
   $toclines[] = "</li>".PHP_EOL;

   $toclines[] = "<li title='<i>".$part4sub1."<br>".$part4sub2."</i>'><a href=\".U120_0_1\"><b>IV. ".$part4."</b></a>".PHP_EOL;
   $toclines[] = " <ul>".PHP_EOL;
   $toclines = array_merge($toclines, add_papers(120, 196, $lang));
   $toclines[] = " </ul>".PHP_EOL;
   $toclines[] = "</li>".PHP_EOL;

   $toclines[] = "</ul>".PHP_EOL;
   file_put_contents($tocfile, $toclines);
}

function add_papers($i_min, $i_max, $lang) {
   $retlines = [];
   for ($i = $i_min; $i <= $i_max; $i++) {
      list($papertitle, $author) = parse_titles($lang, $i);
      if ($i > 0) $papertitle = $i.". ".$papertitle;
      $retlines[] = "  <li title='<i>".$author."</i>'><a class=\"U".$i."_0_1\" href=\".U".$i."_0_1\"><b>".$papertitle."</b></a>".PHP_EOL;
      $retlines[] = "    <ul>".PHP_EOL;
      $retlines = array_merge($retlines, add_sections($i, $lang));
      $retlines[] = "    </ul>".PHP_EOL;
      $retlines[] = "  </li>".PHP_EOL;
   }
   return $retlines;
}

function add_sections($i, $lang) {
   global $languages;
   $paperfile = sprintf("text/".$languages[$lang]."/p%03d.html", $i);
   $lines = file($paperfile);
   $retlines = [];
   foreach($lines as $line)
      if (preg_match('/<h4>(<a class="U\d{1,3}_\d{1,2}_0" href="\.U\d{1,3}_\d{1,2}_0">.*<\/a>)<\/h4>/u', $line, $matches))
         $retlines[] = "      <li>".$matches[1]."</li>".PHP_EOL;
   return $retlines;
}

function parse_titles($lang, $i) {
   $titlesfile = "exemplars/".$lang."/FM_Titles_Exemplar.txt";
   $lines = file($titlesfile);
   $hdlines = 10;
   if ($i <= 31) $offset = $hdlines;
   elseif ($i <= 56) $offset = $hdlines + 3;
   elseif ($i <= 119) $offset = $hdlines + 6;
   else $offset = 19;
   $line = $lines[$i + $offset];
   $line = preg_replace('/{x{x{[^}]*}x}x}/u', '', rtrim($line));
   preg_match('/\[\d+\]\s(?:제 )?(?:\d{1,3})?(?: 편)?(?:\.\s)?(.*) \. (.*)/u', $line, $matches); /* Korean has a special format */
   return [$matches[1], $matches[2]];
}

function parse_parts($lang) {
   $partsfile = "exemplars/".$lang."/FM_Parts_Exemplar.txt";
   $lines = file($partsfile);
   return [extract_text($lines[8]), extract_text($lines[9]), 
           extract_text($lines[11]), extract_text($lines[12]),
           extract_text($lines[14]), extract_text($lines[15]),
           extract_text($lines[17]), extract_text($lines[18]), extract_text($lines[19])];
}

function extract_text($text) {
   $text = explode("] ", rtrim($text), 2);
   return $text[1]; 
}
?>
