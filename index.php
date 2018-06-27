<?php
require 'header.php';

$text_size_selector = "<label for='ts'>$TEXT_SIZE</label> <select id='ts'";
$text_sizes = ['10px', '12px', '13px', '14px', '15px', '16px', '17px', '18px', '19px', '20px', '21px', '22px', '24px'];
foreach($text_sizes as $key) $text_size_selector .= "<option value='$key'>$key</option>";
$text_size_selector .= "</select>";

$site_langs = ['en' => 'English', 'ru' => 'русский', 'ua' => 'український'];
$site_lang_selector = "<label for='language'>$SITE_LANGUAGE</label> <select id='language'>";
foreach($site_langs as $key => $value) {
   if ($lang == $key) $site_lang_selector .= "<option selected value='$key'>$value</option>";
   else $site_lang_selector .= "<option value='$key'>$value</option>";
}
$site_lang_selector .= "</select>";

$themes = ['ui-lightness' => 'UI Lightness', 'base' => 'Base', 'black-tie' => 'Black Tie',
           'blitzer' => 'Blitzer', 'cupertino' => 'Cupertino', 'excite-bike' => 'Excite Bike', 'flick' => 'Flick',
           'hot-sneaks' => 'Hot Sneaks', 'humanity' => 'Humanity', 'overcast' => 'Overcast',
           'pepper-grinder' => 'Pepper Grinder', 'redmond' => 'Redmond', 'smoothness' => 'Smoothness',
           'south-street' => 'South Street', 'start' => 'Start', 'sunny' => 'Sunny'];

$themes_selector = "<label for='themes'>$SITE_THEME</label> <select id='themes'>";
foreach($themes as $key => $value) {
   if ($theme == $key) $themes_selector .= "<option selected value='$key'>$value</option>";
   else $themes_selector .= "<option value='$key'>$value</option>";
}
$themes_selector .= "</select>";

$tooltips_checkbox = "<label title='$TOOLTIP_ON_TOOLTIPS' for='tooltips'>$TOOLTIPS</label>";
if ($tooltips == 1) $tooltips_checkbox .= "<input id='tooltips' type='checkbox' value='1' checked>";
else $tooltips_checkbox .= "<input id='tooltips' type='checkbox' value='1'>";

$animations_checkbox = "<label title='$ENABLE_WIDGET_ANIMATIONS' for='animations'>$ANIMATIONS</label>";
if ($animations == 1) $animations_checkbox .= "<input id='animations' type='checkbox' value='1' checked>";
else $animations_checkbox .= "<input id='animations' type='checkbox' value='1'>";

$scrollsync_checkbox = "<label title='$TOOLTIP_SCROLLSYNC' for='scrollsync'>$SCROLLSYNC</label>";
if ($scrollsync == 1) $scrollsync_checkbox .= "<input id='scrollsync' type='checkbox' value='1' checked>";
else $scrollsync_checkbox .= "<input id='scrollsync' type='checkbox' value='1'>";

$text_options = "<optgroup label='$ORIGINALS'>".
"<option value=1 title='Ed.&nbsp;Tigran&nbsp;Aivazian,&nbsp;Bibles.org.uk'>English: British Study Edition</option>".
"<option value=0 title='The English text of The&nbsp;Urantia&nbsp;Book is in the Public&nbsp;Domain'>English: SRT (American)</option>".
               "</optgroup>".
                "<optgroup label='$TRANSLATIONS'>".
"<option value=2 title='&#169;&nbsp;Urantia&nbsp;Foundation'>Русский: UF 4-е изд.</option>".
"<option value=23 title='&#169;&nbsp;Urantia&nbsp;Foundation'>Русский: UF 5-е изд.</option>".
"<option value=5 title='&#169;&nbsp;Urantia&nbsp;Society&nbsp;of&nbsp;Greater&nbsp;New&nbsp;York'>Русский: USGNY 2017-1</option>".
"<option value=4 title='&#169;&nbsp;Urantia&nbsp;Foundation'>Deutsch: UF 2015-1</option>".
"<option value=6 title='&#169;&nbsp;Urantia&nbsp;Foundation'>Nederlands: UF 1997-1</option>".
"<option value=7 title='&#169;&nbsp;Urantia&nbsp;Foundation'>Eesti: UF 2010-1</option>".
"<option value=8 title='&#169;&nbsp;Urantia&nbsp;Foundation'>Suomi: UF 1993-1</option>".
"<option value=9 title='&#169;&nbsp;Urantia&nbsp;Foundation'>Français: UF 2014-2</option>".
"<option value=11 title='&#169;&nbsp;Urantia&nbsp;Foundation'>Magyar: UF 2010-1</option>".
"<option value=12 title='&#169;&nbsp;Urantia&nbsp;Foundation'>Italiano: UF 2006-1</option>".
"<option value=13 title='&#169;&nbsp;Urantia&nbsp;Foundation'>한국어: UF 2016-1</option>".
"<option value=14 title='&#169;&nbsp;Urantia&nbsp;Foundation'>Lietuvių: UF 2004-1</option>".
"<option value=15 title='&#169;&nbsp;Urantia&nbsp;Foundation'>Polski: UF 2010-2</option>".
"<option value=16 title='&#169;&nbsp;Urantia&nbsp;Foundation'>Português: UF 2003-1</option>".
"<option value=17 title='&#169;&nbsp;Urantia&nbsp;Foundation'>Română: UF 2004-1</option>".
"<option value=18 title='&#169;&nbsp;Urantia&nbsp;Foundation'>Español (Américas): UF 1993-24</option>".
"<option value=19 title='&#169;&nbsp;Urantia&nbsp;Foundation'>Español (Europea): UF 2009-1</option>".
"<option value=20 title='&#169;&nbsp;Urantia&nbsp;Foundation'>Svenska: UF 2010-1</option>".
"<option value=22 title='&#169;&nbsp;Urantia&nbsp;Society&nbsp;of&nbsp;Greater&nbsp;New&nbsp;York'>Türk: USGNY 2012-1</option>".
               "</optgroup>".
                "<optgroup label='$DRAFTS'>".
"<option value=10 title='&#169;&nbsp;Urantia&nbsp;Foundation'>Ελληνική: UF 2012-1</option>".
"<option value=3 title='&#169;&nbsp;Urantia&nbsp;Foundation'>Български: UF 2013-1.0</option>".
               "</optgroup>";

echo $htmlhead;

echo "<div id='tabs'>
   <ul id='tabs_top'>
      <li><a href='#tab_home' title='$HOME_HELP' id='home'><span class='ui-icon ui-icon-home'></span> $HOME</a></li>
      <li><a href='https://github.com/tigran123/urantia-book-explorer/issues/' title='$FORUM_HELP' id='forum'><span class='ui-icon ui-icon-person'></span> $FORUM</a></li>
<!-- <li><a href='#tab_user' title='$USER_HELP'><span id='user_status' class='ui-icon ui-icon-locked'></span> $USER</a></li> -->
      <li><a href='#tab_settings' title='$SETTINGS_HELP'><span class='ui-icon ui-icon-gear'></span> $SETTINGS</a></li>
      <li><a href='#tab_contact' title='$CONTACT_HELP'><span class='ui-icon ui-icon-comment'></span> $CONTACT</a></li>
   </ul>

<div id='tab_home'>
<div id='help' class='hidden ui-widget ui-front ui-widget-content ui-corner-all ui-widget-shadow'>
<table>
<th colspan=4 title='$HELP_TIP'>$HELP<hr></th>
<tr><td class='key'>F1</td><td>$CALL_HELP</td><td class='key'>Ctrl + 0</td><td>$SHOW_HIDE_TOC</td></tr>
<tr><td class='key'>Ctrl + X</td><td>$CLEAR_SEARCH_STRING_HELP</td><td class='key'>Ctrl + 1/2/3/4</td><td>$SHOW_HIDE_COL1234</td></tr>
<tr><td class='key'>Ctrl + S</td><td>$SHOW_HIDE_SEARCH_PANEL</td><td class='key'>Ctrl + Shift 1/2/3/4</td><td>$SELECT_TEXT1234</td></tr>
<tr><td class='key'>Ctrl + O</td><td>$EXPAND_COLLAPSE</td><td class='key'>Ctrl + 5</td><td>$SHOW_HIDE_SEARCH_RESULTS</td></tr>
<tr><td class='key'>Ctrl + H</td><td>$SHOW_HIDE_TABS</td><td class='key'>Ctrl + 6</td><td>$SHOW_HIDE_NOTES</td></tr>
<tr><td class='key'>Ctrl + M</td><td>$MAX_HEIGHT_HELP</td><td class='key'>Ctrl + A</td><td>$IGNORE_CASE_HELP</td></tr>
<tr><td class='key'>Ctrl + B</td><td>$MAX_WIDTH_HELP</td><td class='key'>Ctrl + P</td><td>$TOGGLE_TOOLTIPS</td></tr>
<tr><td class='key'>Ctrl + E</td><td>$SHOW_HIDE_EXPLORER_PANEL</td></tr>
<th colspan=4 title='$HELP_TIP'><hr>$HELP_SEARCH</th>
<tr><td class='key'>N:M.L</td><td colspan=3>$HELP_GOTO</td></tr>
<tr><td class='key'>*</td><td colspan=3>$ANY_SYMBOLS0</td></tr>
<tr><td class='key'>+</td><td colspan=3>$ANY_SYMBOLS1</td></tr>
<tr><td class='key'>?</td><td colspan=3>$ANY_SYMBOL</td></tr>
<tr><td class='key'>-$WORD</td><td colspan=3>$MINUS_SYMBOL</td></tr>
<tr><td class='key'>$WORD1 /[N,M] $WORD2</td><td colspan=3>$DIST_WO_ORDER</td></tr>
<tr><td class='key'>/N</td><td colspan=3>$DIST_WO_SHORT</td></tr>
<tr><td class='key'>$WORD1 @[N,M] $WORD2</td><td colspan=3>$DIST_W_ORDER</td></tr>
<tr><td class='key'>@N</td><td colspan=3>$DIST_W_SHORT</td></tr>
</table>
</div>

<div class='container' id='controls'>
<div id='explorer_control'>
<fieldset>
<legend>$EXPLORER_PANEL</legend>
<button class='buttons colsw' type='button' id='col0rad' title='$SHOW_HIDE_TOC_HELP'><span class='ui-icon ui-icon-bookmark'></span></button>
<button class='buttons coltxtsw' type='button' id='col1rad' title='$SELECT_TEXT1'><span class='uxtra'>1</span></button>
<button class='buttons coltxtsw' type='button' id='col2rad' title='$SELECT_TEXT2'><span class='uxtra'>2</span></button>
<button class='buttons coltxtsw' type='button' id='col3rad' title='$SELECT_TEXT3'><span class='uxtra'>3</span></button>
<button class='buttons coltxtsw' type='button' id='col4rad' title='$SELECT_TEXT4'><span class='uxtra'>4</span></button>
<button class='buttons colsw' type='button' id='col5rad' title='$SHOW_HIDE_SEARCH_RESULTS_HELP'><span class='ui-icon ui-icon-search'></span></button>
<button class='buttons colsw' type='button' id='col6rad' title='$SHOW_HIDE_NOTES_HELP'><span class='ui-icon ui-icon-flag'></span></button>
<button class='buttons colsize_controls' type='button' id='max_height' title='$MAX_HEIGHT'><span class='ui-icon ui-icon-arrowthick-2-n-s'></span></button>
<button class='buttons colsize_controls' type='button' id='max_width' title='$MAX_WIDTH'><span class='ui-icon ui-icon-arrowthick-2-e-w'></span></button>
<button class='buttons' type='button' id='help_button' title='$HELP_TOOLTIP'><span class='ui-icon ui-icon-help'></span></button>
</fieldset>
</div>
<div id='search_control'>
<fieldset>
<legend>$SEARCH_PANEL</legend>
<select id='search_part' title='$SELECT_PARTS'>
  <option value=0>$ALL_PARTS</option>
  <option value=1>$PART_I</option>
  <option value=2>$PART_II</option>
  <option value=3>$PART_III</option>
  <option value=4>$PART_IV</option>
</select>
<select id='search_mode' title='$SEARCH_MODE'>
  <option value='all'>$ALL_WORDS</option>
  <option value='exact'>$EXACT</option>
  <option value='any'>$ANY_WORD</option>
</select>
<select id='search_range' title='$SEARCH_RANGE'>
  <option value=0>$TEXT_ONLY</option>
  <option value=1>$TEXT_PLUS_TITLES</option>
  <option value=2>$TITLES_ONLY</option>
</select>
<button class='buttons' type='button' id='clear' title='$CLEAR_SEARCH_STRING'><span class='ui-icon ui-icon-close'></span></button>
<input placeholder='$INPUT_SEARCH_STRING' type='text' autofocus id='search_text'>
<button class='buttons' type='button' id='search' title='$START_SEARCH'><span class='ui-icon ui-icon-arrowrefresh-1-n' id='search_status'></span></button>
<button class='buttons' type='button' id='ic' title='$IGNORE_CASE'><span id='ic_lab'>a = A</span></button>
</fieldset>
</div>
</div>

<table id='panels' width=100%>
  <th class='col0 headers' id='col0hdr' title='$PRESS_CTRL_0' valign='top'>
    <button class='buttons' type='button' id='toc_expand_collapse' title='$EXPAND_COLLAPSE'>
      <span class='ui-icon ui-icon-arrow-4-diag'></span>
    </button>
    <span class='coltitle' id='col0title'>$CONTENTS</span>
    <a href='javascript:void(0)' class='colclose' id='col0close' title='$CLOSE_WINDOW'>
       <span class='ui-icon ui-icon-closethick'></span>
    </a>
  </th>
  <th class='col1 headers txthdr' id='col1hdr' title='$PRESS_CTRL_1'>
    <span class='uxtra'>1</span>
    <a href='javascript:void(0)' class='colupdown' name='down' title='$SCROLL_DOWN'>
       <span class='ui-icon ui-icon-circle-arrow-s'></span>
    </a>
    <select class='colmod' id='col1mod' title='$SELECT_TEXT'>$text_options</select>
    <a href='javascript:void(0)' class='colupdown' name='up' title='$SCROLL_UP'>
       <span class='ui-icon ui-icon-circle-arrow-n'></span>
    </a>
    <a href='javascript:void(0)' class='colclose' id='col1close' title='$CLOSE_WINDOW'>
       <span class='ui-icon ui-icon-closethick'></span>
    </a>
    <div class='coltitle coltxttitle' id='col1title'></div>
  </th>
  <th class='col2 headers txthdr' id='col2hdr' title='$PRESS_CTRL_2'>
    <span class='uxtra'>2</span>
    <a href='javascript:void(0)' class='colupdown' name='down' title='$SCROLL_DOWN'>
       <span class='ui-icon ui-icon-circle-arrow-s'></span>
    </a>
    <select class='colmod' id='col2mod' title='$SELECT_TEXT'>$text_options</select>
    <a href='javascript:void(0)' class='colupdown' name='up' title='$SCROLL_UP'>
       <span class='ui-icon ui-icon-circle-arrow-n'></span>
    </a>
    <a href='javascript:void(0)' class='colclose' id='col2close' title='$CLOSE_WINDOW'>
       <span class='ui-icon ui-icon-closethick'></span>
    </a>
    <div class='coltitle coltxttitle' id='col2title'></div>
  </th>
  <th class='col3 headers txthdr' id='col3hdr' title='$PRESS_CTRL_3'>
    <span class='uxtra'>3</span>
    <a href='javascript:void(0)' class='colupdown' name='down' title='$SCROLL_DOWN'>
       <span class='ui-icon ui-icon-circle-arrow-s'></span>
    </a>
    <select class='colmod' id='col3mod' title='$SELECT_TEXT'>$text_options</select>
    <a href='javascript:void(0)' class='colupdown' name='up' title='$SCROLL_UP'>
       <span class='ui-icon ui-icon-circle-arrow-n'></span>
    <a>
    <a href='javascript:void(0)' class='colclose' id='col3close' title='$CLOSE_WINDOW'>
       <span class='ui-icon ui-icon-closethick'></span>
    </a>
    <div class='coltitle coltxttitle' id='col3title'></div>
  </th>
  <th class='col4 headers txthdr' id='col4hdr' title='$PRESS_CTRL_4'>
    <span class='uxtra'>4</span>
    <a href='javascript:void(0)' class='colupdown' name='down' title='$SCROLL_DOWN'>
       <span class='ui-icon ui-icon-circle-arrow-s'></span>
    </a>
    <select class='colmod' id='col4mod' title='$SELECT_TEXT'>$text_options</select>
    <a href='javascript:void(0)' class='colupdown' name='up' title='$SCROLL_UP'>
       <span class='ui-icon ui-icon-circle-arrow-n'></span>
    <a>
    <a href='javascript:void(0)' class='colclose' id='col4close' title='$CLOSE_WINDOW'>
       <span class='ui-icon ui-icon-closethick'></span>
    </a>
    <div class='coltitle coltxttitle' id='col4title'></div>
  </th>
  <th class='col5 headers' id='col5hdr' title='$PRESS_CTRL_5'>
    <span class='coltitle' id='col5title'>$SEARCH_RESULTS <span id='search_total'></span></span>
    <a href='javascript:void(0)' class='colclose' id='col5close' title='$CLOSE_WINDOW'>
       <span class='ui-icon ui-icon-closethick'></span>
    </a>
  </th>
  <th class='col6 headers' id='col6hdr' title='$PRESS_CTRL_6'>
    <span class='coltitle' id='col6title'>$NOTES <span id='notes_total'></span></span>
    <a href='javascript:void(0)' class='colclose' id='col6close' title='$CLOSE_WINDOW'>
       <span class='ui-icon ui-icon-closethick'></span>
    </a>
  </th>
<tr>
  <td class='col0' id='col0toc' valign='top'>
    <div class='toc_container hidden data' id='col1toc'></div>
    <div class='toc_container hidden data' id='col2toc'></div>
    <div class='toc_container hidden data' id='col3toc'></div>
    <div class='toc_container hidden data' id='col4toc'></div>
  </td>
  <td class='col1' valign='top'><div class='coltxt data scrollable' id='col1txt'></div></td>
  <td class='col2' valign='top'><div class='coltxt data scrollable' id='col2txt'></div></td>
  <td class='col3' valign='top'><div class='coltxt data scrollable' id='col3txt'></div></td>
  <td class='col4' valign='top'><div class='coltxt data scrollable' id='col4txt'></div></td>
  <td class='col5' valign='top'><div class='data' id='search_results'></div></td>
  <td class='col6' valign='top'><div class='data' id='notes'></div></td>
</tr>
</table>
</div> <!-- tab_home -->

<!--
<div id='tab_user'>
<fieldset><legend>$USER_CONTROLS</legend>
<label for='login'>$LOGIN</label> <input id='login' width=100px> <label for='password'>$PASSWORD</label> <input id='password'>
</fieldset>
</div>
-->

<div id='tab_settings'>
<fieldset>
<legend>$SITE_SETTINGS</legend>
<div>$text_size_selector $site_lang_selector $tooltips_checkbox $animations_checkbox $scrollsync_checkbox $themes_selector</div>
</fieldset>
</div>

<div class='container' id='tab_contact'>
<table>
<th><h2 style='text-transform:uppercase;'>$SOURCE_CODE</h2></th>
<tr><td>$SOURCE_CODE_TEXT</td></tr>
</table>

<table>
<th colspan=2><h2 style='text-transform:uppercase;'>$CONTACT_DETAILS</h2></th>
<tr>
<td><img src='img/contact.png' height='100'></img></td>
<td>$CONTACT_TEXT</td>
</tr>
</table>
</div>

</div>"; /* tabs div */

//include 'statcounter.html';
echo $htmlfoot;
?>
