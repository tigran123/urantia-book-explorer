# The Urantia Book Explorer Web Application

The Urantia Book Explorer (UBEX) has the following main features:

* Side-by-side comparison of ALL translations of the Urantia Book with the English text.

* Very powerful Text Search Engine (with regular expressions etc)

The detailed comparison with other similar application is available from the tab "CONTACT"

The application uses the following JavaScript libraries (found in jquery/ subdirectory)

* jQuery 3.1.1

* jQuery UI 1.12.1

* jQuery scrollTo 2.1.2

* jQuery Bonsai

The application is released under GPL license, however we make use of the following proprietary components:

* Various translations of the Urantia Book, Copyright (C) Urantia Foundation and used by permission

* Various translations of the Urantia Book, Copyright (C) Urantia Society of Greater New York and used by permission

The application relies on PHP version >= 5.6.27 on the server.

# How to use the Urantia Book Explorer on Android offline (without Internet)

The Urantia Book Explorer web application can be installed on an Android device, such a tablet or a smartphone.
The tablets with large screens are obviously preferred, if you want to use more than one text column comfortably.

This is how you install UBEX on Android:

## Step 1. Install Termux

The Urantia Book Explorer is a website, so it requires a webserver to run. Fortunately, any Android device can
be used as an Apache2 webserver, thanks to the wonderful application called Termux, which provides a full Linux
environment on Android. Termux can be installed from F-Droid from here:

[https://f-droid.org/en/packages/com.termux/](https://f-droid.org/en/packages/com.termux/)

Please note: NEVER install Termux (or anything else, for that matter) from Google PlayStore, because it is
unsupported and will not work properly. You should only install Android applications using the .apk files
provided by the developers or from F-Droid.

## Step 2. Install Apache2 with PHP

```
$ pkg upgrade
$ pkg install apache2 php php-apache
```

## Step 3. Make a symlink to the website's location

```
$ cd
$ ln -s /data/data/com.termux/files/usr/share/apache2/default-site/htdocs website
```

Prior to this you should make sure that the directory `/data/data/com.termux/files/usr/share/apache2/default-site/htdocs` is empty,
by removing or moving its current content elsewhere.

## Step 4. Copy the entire content of this github repository to the directory pointed to by the `website` symlink in your home directory

## Step 5. Configure Apache2

Copy the file `tools/httpd.conf` from the repository to `$PREFIX/etc/apache2/httpd.conf`

## Step 6. Start up Apache2

I normally have these two lines in my `~/.profile`:

```
alias a='apachectl start'
alias aa='apachectl stop'
```

So I can easily start and stop Apache by using `a` and `aa` command shortcuts.

## Step 7. Point your browser to UBEX

Go to the following URL in your web browser `http://127.0.0.1:8080`.
I recommend Samsung Internet Browser as it actually works without Internet access,
unlike Google Chrome, which in the past had some glitches with connecting to the locally
running web browser. But in the recent versions they seem to have fixed this problem...

That is it.
