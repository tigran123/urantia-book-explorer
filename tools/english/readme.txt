How to use the tools in this directory.
--------------------------------------

1. First download the zip file from urantia.org, in our case: UF-ENG-001-1955-18-xhtml.zip

2. To generate the text html files run mktext.sh

$ ./mktext.sh

This will create the orig subdirectory with the content of the zip file and the directory 0 with
the result of the conversion.

3. To generate the TOC run mktoc.sh

$ ./mktoc.sh

This will create the file 0/toc.html

4. Now copy the directory '0' to the text subdirectory of the websites root

$ cp -rv 0 ../../text

That is it, you are done!
