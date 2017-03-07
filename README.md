# The Urantia Book Explorer Web Application

The Urantia Book Explorer (UBE) is expected to have the following features when completed:

* Side-by-side comparison of multiple (ultimately ALL) translations of the Urantia Book with the English text.

* Search Engine (with regular expressions etc)

* Integrated Quiz (for testing your knowledge of the Urantia Book)

* Built-in annotations (readonly)

* User-defined annotations (read write)

The application uses the following JavaScript libraries (found in jquery/ subdirectory)

* jQuery 3.1.1

* jQuery UI 1.12.1

* jQuery scrollTo 2.1.2

* jQuery Bonsai

The application is released under GPL license, however we make use of the following proprietary components:

* Various translations of the Urantia Book, Copyright (C) Urantia Foundation and used by permission

* Various translations of the Urantia Book, Copyright (C) Urantia Book Society of Greater New York and used by permission

When deploying the application you should not copy the files mentioned in `exclude.txt` to the server:

```
$ rsync --exclude-from=exclude.txt --delete -ahv . tigran123@quantuminfodynamics.com:public_html/ubex/
```
