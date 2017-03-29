#!/bin/bash

ZIP="uf-rus-001world-1997-1.9.xhtml_.zip"
OUT=2

rm -rf orig; mkdir orig 
cd orig
unzip -q ../$ZIP
dos2unix -q *.htm
rename 's/htm/html/' *.htm
cd - > /dev/null

rm -rf $OUT ; mkdir $OUT

FILELIST=$(echo orig/p???.html)
for file in $FILELIST
do
    base=$(basename $file)
    if [ "$base" == "p000.html" ] ; then
       titlepattern="1d"
    else
       titlepattern="1,2d"
    fi
    sed -f stage1.sed $file | sed -e "$titlepattern" > $OUT/$base
done
