#!/bin/bash

SRC="0"
TOC="0/toc.html"
TITLES="orig/FM_Titles.html"

echo "<ul class='toc' id='toc0'>" > $TOC
echo -E "<li title='<i>Sponsored by a Uversa Corps of Superuniverse Personalities acting by authority of the Orvonton Ancients of Days.</i>'><a href=\".U0_0_1\"><b>I: THE CENTRAL AND SUPERUNIVERSES</b></a>" >> $TOC
echo -E " <ul>" >> $TOC

# papers 0-31 are processed here
for ((i = 0; i <= 31; i++));
do
   I=$(printf "%03d" $i)
   papertitle=$(sed -ne "s/^<dd>${I}. <a href=\"p...\.htm\">\(.*\)<\/a> . .*<\/dd>$/\1/p" $TITLES)
   author=$(sed -ne "s/^<dd>${I}. <a href=\"p...\.htm\">.*<\/a> . \(.*\)<\/dd>$/\1/p" $TITLES)
   if [ $i == 0 ] ; then
      echo -E "  <li title='<i>Divine Counselor</i>'><a class=\"U0_0_1\" href=\".U0_0_1\"><b>Foreword</b></a>" >> $TOC
   else
      echo -E "  <li title='<i>$author</i>'><a class=\"U${i}_0_1\" href=\".U${i}_0_1\"><b>$i. $papertitle</b></a>" >> $TOC
   fi
   echo -E "    <ul>" >> $TOC
   grep "<h4>" $SRC/p${I}.html | sed -e 's/<h4>/      <li>/' -e 's/<\/h4>/      <\/li>/' >> $TOC
   echo -E "    </ul>" >> $TOC
   echo -E "  </li>" >> $TOC
done
echo -E " </ul>" >> $TOC

echo -E "</li>" >> $TOC

echo -E "<li title='<i>Sponsored by a Nebadon Corps of Local Universe Personalities acting by authority of Gabriel of Salvington.</i>'><a href=\".U32_0_1\"><b>II: THE LOCAL UNIVERSE</b></a>" >> $TOC
echo -E " <ul>" >> $TOC

# papers 32-56 are processed here

for ((i = 32; i <= 56; i++));
do
   I=$(printf "%03d" $i)
   papertitle=$(sed -ne "s/^<dd>${I}. <a href=\"p...\.htm\">\(.*\)<\/a> . .*<\/dd>$/\1/p" $TITLES)
   author=$(sed -ne "s/^<dd>${I}. <a href=\"p...\.htm\">.*<\/a> . \(.*\)<\/dd>$/\1/p" $TITLES)
   echo -E "  <li title='<i>$author</i>'><a class=\"U${i}_0_1\" href=\".U${i}_0_1\"><b>$i. $papertitle</b></a>" >> $TOC
   echo -E "    <ul>" >> $TOC
   grep "<h4>" $SRC/p${I}.html | sed -e 's/<h4>/      <li>/' -e 's/<\/h4>/      <\/li>/' >> $TOC
   echo -E "    </ul>" >> $TOC
   echo -E "  </li>" >> $TOC
done
echo -E " </ul>" >> $TOC

echo -E "</li>" >> $TOC
echo -E "<li title='<i>These papers were sponsored by a Corps of Local Universe Personalities acting by authority of Gabriel of Salvington.</i>'><a href=\".U57_0_1\"><b>III: THE HISTORY OF URANTIA</b></a>" >> $TOC
echo -E " <ul>" >> $TOC

# papers 57-119 are processed here
for ((i = 57; i <= 119; i++));
do
   I=$(printf "%03d" $i)
   papertitle=$(sed -ne "s/^<dd>${I}. <a href=\"p...\.htm\">\(.*\)<\/a> . .*<\/dd>$/\1/p" $TITLES)
   author=$(sed -ne "s/^<dd>${I}. <a href=\"p...\.htm\">.*<\/a> . \(.*\)<\/dd>$/\1/p" $TITLES)
   echo -E "  <li title='<i>$author</i>'><a class=\"U${i}_0_1\" href=\".U${i}_0_1\"><b>$i. $papertitle</b></a>" >> $TOC
   echo -E "    <ul>" >> $TOC
   grep "<h4>" $SRC/p${I}.html | sed -e 's/<h4>/      <li>/' -e 's/<\/h4>/      <\/li>/' >> $TOC
   echo -E "    </ul>" >> $TOC
   echo -E "  </li>" >> $TOC
done
echo -E " </ul>" >> $TOC

echo -E "</li>" >> $TOC

echo -E "<li title='<i>This group of papers was sponsored by a commission of twelve Urantia midwayers acting under the supervision of a Melchizedek revelatory director.<br>The basis of this narrative was supplied by a secondary midwayer who was onetime assigned to the superhuman watchcare of the Apostle Andrew.</i>'><a href=\".U120_0_1\"><b>IV: THE LIFE AND TEACHINGS OF JESUS</b></a>" >> $TOC
echo -E " <ul>" >> $TOC

# papers 120-196 are processed here
for ((i = 120; i <= 196; i++));
do
   I=$(printf "%03d" $i)
   papertitle=$(sed -ne "s/^<dd>${I}. <a href=\"p...\.htm\">\(.*\)<\/a> . .*<\/dd>$/\1/p" $TITLES)
   author=$(sed -ne "s/^<dd>${I}. <a href=\"p...\.htm\">.*<\/a> . \(.*\)<\/dd>$/\1/p" $TITLES)
   echo -E "  <li title='<i>$author</i>'><a class=\"U${i}_0_1\" href=\".U${i}_0_1\"><b>$i. $papertitle</b></a>" >> $TOC
   echo -E "    <ul>" >> $TOC
   grep "<h4>" $SRC/p${I}.html | sed -e 's/<h4>/      <li>/' -e 's/<\/h4>/      <\/li>/' >> $TOC
   echo -E "    </ul>" >> $TOC
   echo -E "  </li>" >> $TOC
done
echo -E " </ul>" >> $TOC

echo -E "</li>" >> $TOC
echo "</ul>" >> $TOC
