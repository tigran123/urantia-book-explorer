#!/bin/bash

ZIP="UF-ENG-001-1955-18-xhtml.zip"

rm -rf orig; mkdir orig 
cd orig
unzip -q ../$ZIP
dos2unix -q *.htm
rename 's/htm/html/' *.htm
cd - > /dev/null

rm -rf 0 ; mkdir 0

FILELIST=$(echo orig/p???.html)
for file in $FILELIST
do
    base=$(basename $file)
    if [ "$base" == "p000.html" ] ; then
       titlepattern="1d"
    else
       titlepattern="1,2d"
    fi
    sed -f stage1.sed $file | sed -e "$titlepattern" > 0/$base
done
