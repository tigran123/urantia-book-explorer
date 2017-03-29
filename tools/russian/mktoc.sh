#!/bin/bash

SRC="2"
TOC=$SRC/toc.html
TITLES="orig/FM_Titles.html"

echo "<ul class='toc' id='toc2'>" > $TOC
echo -E "<li title='<i>Подготовлено Увeрсским Корпусом личностей сверхвселенной, уполномоченных От Века Древними Орвонтона.</i>'><a href=\".U0_0_1\"><b>I: ЦЕНТРАЛЬНАЯ ВСЕЛЕННАЯ И СВЕРХВСЕЛЕННЫЕ</b></a>" >> $TOC
echo -E " <ul>" >> $TOC

# papers 0-31 are processed here
for ((i = 0; i <= 31; i++));
do
   I=$(printf "%03d" $i)
   papertitle=$(sed -ne "s/^<dd>${I}. <a href=\"p...\.htm\">\(.*\)<\/a> . .*<\/dd>$/\1/p" $TITLES)
   author=$(sed -ne "s/^<dd>${I}. <a href=\"p...\.htm\">.*<\/a> . \(.*\)<\/dd>$/\1/p" $TITLES)
   if [ $i == 0 ] ; then
      echo -E "  <li title='<i>Божественный Советник</i>'><a class=\"U0_0_1\" href=\".U0_0_1\"><b>Предисловие</b></a>" >> $TOC
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

echo -E "<li title='<i>Подготовлено Небадонским Корпусом личностей локальной вселенной, уполномоченных Гавриилом Салвингтонским.</i>'><a href=\".U32_0_1\"><b>II: ЛОКАЛЬНАЯ ВСЕЛЕННАЯ</b></a>" >> $TOC
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
echo -E "<li title='<i>Настоящие документы были подготовлены Корпусом личностей локальной вселенной, уполномоченных Гавриилом Салвингтонским.</i>'><a href=\".U57_0_1\"><b>III: ИСТОРИЯ УРАНТИИ</b></a>" >> $TOC
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

echo -E "<li title='<i>Данный раздел был подготовлен комиссией из двенадцати промежуточных созданий Урантии, трудившихся под наблюдением Мелхиседека, руководителя комиссии откровения.<br>В основу повествования положена информация, предоставленная вторичным промежуточным созданием, некогда исполнявшим обязанности сверхчеловеческого хранителя апостола Андрея.</i>'><a href=\".U120_0_1\"><b>IV: ЖИЗНЬ И УЧЕНИЯ ИИСУСА</b></a>" >> $TOC
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

# fix an 'artefact' in the sources
sed -e 's/95\. T 095\./95./' < $TOC > ${TOC}.tmp
mv ${TOC}.tmp $TOC
