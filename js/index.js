if (getCookie('expcpanel') == '0') $('#explorer_control').addClass('hidden');
var active_column = localStorage.getItem("active_column");
var current_paragraph = localStorage.getItem("current_paragraph"); // TODO: 20200815 Al
if (active_column == undefined) {
   active_column = 'col1';
   localStorage.setItem("active_column", active_column);
}
$('#' + active_column + 'hdr').css('border', 'solid darkblue 2px');
coltxtsw(active_column).css('border', 'solid darkblue 2px');

var colpaper_map = {'col1': 0, 'col2': 0, 'col3': 0, 'col4': 0};

$('.scrollable').scrollSync();

if (!$('#scrollsync').is(':checked')) $('.coltxt').removeClass('scrollable');

$('#scrollsync').change(function() {
   if ($(this).is(':checked')) {
      $('.coltxt').addClass('scrollable');
      document.cookie = 'scrollsync=1; expires=Fri, 31 Dec 9999 23:59:59 GMT';
   } else {
      $('.coltxt').removeClass('scrollable');
      document.cookie = 'scrollsync=0; expires=Fri, 31 Dec 9999 23:59:59 GMT';
   }
});

$('.buttons').button();

$('#tabs').tabs({
   activate: function(event, ui) {
      var hash = ui.newPanel.attr('id');
      var scrollTop = $(window).scrollTop(); // save current scroll position
      location.hash = hash == 'tab_home' ? '' : hash;//#tab_home
      $(window).scrollTop(scrollTop); // keep scroll at current position
   }
});

$('.cookieBubble').cookieBubble({
   cookieMaxAge:7777,
   iconColor:'#ffa500',
   buttonColor: '#000',
   buttonRadius:'3px'
});

$('.colsw').on('click', function() {
   var col = $(this).attr('id').replace('rad','');
   toggle_active_column(col);
});

function coltxtsw(name) { return $('#' + name + 'rad'); }

$('.coltxtsw').on('click', function() {
   var col = $(this).attr('id').replace('rad','');
   if (col == active_column) return;
   $('#' + active_column + 'toc').addClass('hidden');
   coltxtsw(active_column).removeAttr('style');
   $('.' + col).removeClass('hidden');
   $('.colclose').removeClass('hidden');
   localStorage.setItem(col, 1);
   active_column = col;
   localStorage.setItem("active_column", active_column);
   var mod_idx = $('#' + col + 'mod').val();
   load_notes(mod_idx);
   $('#' + col + 'toc').removeClass('hidden');
   $('.txthdr').css('border', 'solid darkgrey 2px');
   $('#' + col + 'hdr').css('border', 'solid darkblue 2px');
   $(this).css('border', 'solid darkblue 2px');
   $('#max_width').click();
});

//Параллельная подсветка заголовков колонок и кнопок управления
$("#col0hdr").hover(function(){$("#col0rad").toggleClass("col0sw");});
$("#col1hdr").hover(function(){$("#col1rad").toggleClass("col1sw");});
$("#col2hdr").hover(function(){$("#col2rad").toggleClass("col2sw");});
$("#col3hdr").hover(function(){$("#col3rad").toggleClass("col3sw");});
$("#col4hdr").hover(function(){$("#col4rad").toggleClass("col4sw");});
$("#col5hdr").hover(function(){$("#col5rad").toggleClass("col5sw");});
$("#col6hdr").hover(function(){$("#col6rad").toggleClass("col6sw");});

$("#col0rad").hover(function(){$("#col0hdr").toggleClass("col0sw");});
$("#col1rad").hover(function(){$("#col1hdr").toggleClass("col1sw");});
$("#col2rad").hover(function(){$("#col2hdr").toggleClass("col2sw");});
$("#col3rad").hover(function(){$("#col3hdr").toggleClass("col3sw");});
$("#col4rad").hover(function(){$("#col4hdr").toggleClass("col4sw");});
$("#col5rad").hover(function(){$("#col5hdr").toggleClass("col5sw");});
$("#col6rad").hover(function(){$("#col6hdr").toggleClass("col6sw");});

$('.colmod').selectmenu({
   change: function(event, ui) {
      var col = $(this).attr('id').replace('mod', '');
      var mod_idx = ui.item.value;
      var paper = colpaper_map[col];
      var filename = 'text/' + mod_idx + '/p' + ("000" + paper).slice(-3) + '.html';
      $('#' + col + 'txt').load(filename, function(response, status, xhr) {
         if (status == 'error') location.reload(); // reload is needed to show the empty column if the load fails
      });
      if (col == active_column) load_notes(mod_idx);
      $('#' + col + 'toc').load('text/' + mod_idx + '/toc.html', function() {
         var toc = $(this).find('.toc');
         toc.bonsai();
         $('#' + col + 'title').html(toc.find('.U' + paper + '_0_1').html());
         localStorage.setItem(col + 'mod', mod_idx);
         if (!$('#tooltips').is(':checked')) { $(document).tooltip('option', 'disabled', true); }
      });
   },
   width: 228
}).each(function() {
   var cnum = $(this).attr('id').replace(/col([0-9][0-9]*)mod/,'$1');
   var mod_idx = localStorage.getItem('col' + cnum + 'mod');
   if (mod_idx == undefined) {
      switch(+cnum) {
         case 1: mod_idx = 1; break;
         case 2: mod_idx = 25; break;
         case 3: mod_idx = 23; break;
         case 4: mod_idx = 5; break;
         default: mod_idx = 1; break;
      }
   }
   $(this).val(mod_idx).selectmenu('refresh');
});

$('.coltxttitle').click(function(e) { coltxtsw(e.currentTarget.id.replace('title','')).click(); });

$('.coltxt').each(function() {
   var col = $(this).attr('id').replace('txt', '');
   var toc_id = '#' + col + 'toc';
   var mod_idx = $('#' + col + 'mod').val();
   var paper = colpaper_map[col];
   $(this).load('text/' + mod_idx + '/p' + ("000" + paper).slice(-3) + '.html');
   if (col == active_column) load_notes(mod_idx);
   $(toc_id).load('text/' + mod_idx + '/toc.html', function() {
      var toc = $(this).find('.toc');
      toc.bonsai();
      $('#' + col + 'title').html(toc.find('.U' + paper + '_0_1').html());
      if (active_column == col) $(toc_id).removeClass('hidden');
      if (!$('#tooltips').is(':checked')) { $(document).tooltip('option', 'disabled', true); }
   });
});


Storage.prototype.setObj = function(key, obj) {
   return this.setItem(key, JSON.stringify(obj))
}
Storage.prototype.getObj = function(key, default_val) {
   var JSONparce = JSON.parse(this.getItem(key));
   return typeof default_val !== 'undefined' ?  (JSONparce == null ? default_val : JSONparce) : JSONparce;
}

function getSearchHistory(){
   var availableHistory = localStorage.getObj('_searches',[]);
   availableHistory = Array.isArray(availableHistory) == true ? availableHistory : [];
   availableHistory = availableHistory.map(function(value){
      if (!Array.isArray(value)) { //для ранее сохраненных элементов истории возвращаем массив с предустановленной строкой параметров
         return [value,'?p=0&m=all&r=0&i=1&t='+encodeURIComponent(value)];
      } else {
         return value;
      }
   });
   return availableHistory;
};

function UpdateSearchHistoryCombo(){
   var availableHistory = getSearchHistory();
   availableHistory = Array.isArray(availableHistory) == true ? availableHistory.reverse() : availableHistory;
   $('#combobox').empty();
   availableHistory.map(function(value){$(new Option(value[0],value[1])).appendTo($('#combobox'));});
};

UpdateSearchHistoryCombo();

function GetSearchOptionsQuerySrt(text){
   var mod_idx      = $('#' + active_column + 'mod').val();
   var encode_text  = encodeURIComponent(text);
   var search_part  = '&search_part='  + $('#search_part').val();
   var search_range = '&search_range=' + $('#search_range').val();
   var search_mode  = $('#search_mode').val();
   var ajax_search_req = "search_" + search_mode + ".php" + "?text=" + encode_text + "&mod_idx=" + mod_idx + "&ic=" + ic + search_part + search_range;
   var url_search_req  = ''
   + '?p=' + $('#search_part').val()        //в какой части ищем
   + '&m=' + search_mode                    //режим (все слова, точный, любое слово)
   + '&r=' + $('#search_range').val()       //где ищем (текст, заголовки)
   + '&l=' + mod_idx                        //номер текста (mod_idx)
   + '&i=' + ic                             //регистрозависимость
   + '&t=' + encode_text;                   //текст, который ищем
   var ret = Object();
   ret.url_search_req  = url_search_req;
   ret.ajax_search_req = ajax_search_req;
   return ret;
};

function SetSearchOptions(queryString){
   if (queryString == null) return;
   let params = new URLSearchParams(queryString);
   var p = parseInt(params.get("p")); // search_part
   var m = params.get("m");           // search_mode
   var r = parseInt(params.get("r")); // search_range
   var l = parseInt(params.get("l")); // text (mod_idx)
   ic    = parseInt(params.get("i")); // ic
   var t = decodeURIComponent(params.get("t"));// search_text

   //Валидация значений
   p = p>=0 && p<5 ? p : 0;
   m = m == 'all' || m == 'exact' || m == 'any' ? m : 'all';
   r = r>=0 && r<3 ? r : 0;
   ic = ic == 0 || ic == 1 ? ic : 1;
   l = $('#col1mod option[value=' + l + ']').length == 1 ? l : 1;     //Пусть l=23. Тогда $('#col1mod option[value=23]').length вернет 1, если в списке есть текст с таким номером. Иначе при любом другом варианте установим первый текст.

   $('#ic_lab').html(ic ? 'a = A' : 'a &#8800; A');

   var col_with_t = $('.txthdr').not('.hidden').has('.colmod').has('option[value="'+l+'"]:selected').first().attr('id'); //колонка с нужным нам текстом
   var col_hidd = $('.txthdr.hidden').first().attr('id');             //первая спрятанная
   var col = (col_with_t || col_hidd || 'col1hdr').replace('hdr',''); //если колонки с текстом нет, берем первую спрятанную, иначе - первую

   $('#' + col + 'mod').val(l).selectmenu('refresh');
   var mod_idx = l;
   var paper = colpaper_map[col];
   coltxtsw(col).click();
   $('#' + col + 'txt').load('text/' + mod_idx + '/p' + ("000" + paper).slice(-3) + '.html');
   load_notes(mod_idx);
   $('#' + col + 'toc').load('text/' + mod_idx + '/toc.html', function() {
      var toc = $(this).find('.toc');
      toc.bonsai();
      $('#' + col + 'title').html(toc.find('.U' + paper + '_0_1').html());
      localStorage.setItem(col + 'mod', mod_idx);
      if (!$('#tooltips').is(':checked')) { $(document).tooltip('option', 'disabled', true); }
   });

   $('#search_part').val(p).selectmenu('refresh');
   $('#search_mode').val(m).selectmenu('refresh');
   $('#search_range').val(r).selectmenu('refresh');
   $('#search_text').val(t);
};

$('#search_part').selectmenu({change: function() { $('#search_text').focus(); }, width: 180});
$('#search_mode').selectmenu({change: function() { $('#search_text').focus(); }, width: 140});
$('#search_range').selectmenu({change: function() { $('#search_text').focus(); }, width: 180});

$(document).tooltip({ content: function () { return this.getAttribute("title"); }, }); /* this enables html in tooltips */
$('#tooltips').change(function() {
   if ($(this).is(':checked')) {
      $(document).tooltip('option', 'disabled', false);
      document.cookie = 'tooltips=1; expires=Fri, 31 Dec 9999 23:59:59 GMT';
   } else {
      $(document).tooltip('option', 'disabled', true);
      document.cookie = 'tooltips=0; expires=Fri, 31 Dec 9999 23:59:59 GMT';
   }
});

$("#shortcontext_lenght").focusout(function(){
   var value = parseInt($(this).val());
   if (value > 100) $(this).val(100);
   if (value < 0)   $(this).val(0);
});

$('#expcpanel').change(function() {
   document.cookie = 'expcpanel=' + ($(this).is(':checked') ? 1 : 0) + '; expires=Fri, 31 Dec 9999 23:59:59 GMT';
   $('#explorer_control').toggleClass('hidden');
   $('#max_height').click();
});

$('#animations').change(function() {
   document.cookie = 'animations=' + ($(this).is(':checked') ? 1 : 0) + '; expires=Fri, 31 Dec 9999 23:59:59 GMT';
});

$('#shortcontext_lenght').change(function() {
   document.cookie = 'shortcontext_lenght=' + parseInt($(this).val()) + '; expires=Fri, 31 Dec 9999 23:59:59 GMT';
});

$('input[type=radio][name="shortcontext"]').change(function() {
   document.cookie = 'shortcontext=' + parseInt($(this).val()) + '; expires=Fri, 31 Dec 9999 23:59:59 GMT';
});

$('#help').draggable();
$('#help_button').click(function(event) { $('#help').toggleClass('hidden'); });
$('#clear').click(function(event) { $('#search_text').val('').focus(); });

$('.toc_container,#search_results,#notes').on('click', 'a', function(e) {
   e.preventDefault();
   var href = $(this).attr('href');
   current_paragraph = href;
   localStorage.setItem("current_paragraph", current_paragraph);
   var fnpat = /U\d{1,3}_\d{1,2}_\d{1,3}_\d+/;
   if (fnpat.exec(href)) $('#notes').scrollTo(href, get_delay());
   else {
      var paper = href.replace(/.U([0-9][0-9]*)_.*_.*/,'$1');
      var $marks = $(this).parent().find('mark');
      var mark_opts = {"accuracy": "exact", "separateWordSearch": false, "acrossElements": true};

      $('.coltxt').each(function() {
         var сol = $(this).attr('id').replace('txt','');
         var $coltxt = $('#' + сol + 'txt');//active_column
         if ((!$('#scrollsync').is(':checked')) & (active_column != сol)) return;
         if (colpaper_map[сol] != paper) { /* need to load a different paper */
            var mod_idx = $('#' + сol + 'mod').val();
            $coltxt.load('text/' + mod_idx + '/p' + ("000" + paper).slice(-3) + '.html', function() {
               var title = $('#' + сol + 'toc').find('.toc').find('.U' + paper + '_0_1').html();
               $('#' + сol + 'title').html(title);
               colpaper_map[сol] = paper;
               $coltxt.scrollTo(href, get_delay());
               $marks.each(function() { $coltxt.mark($(this).text(), mark_opts); });
            });
         } else {
            $coltxt.scrollTo(href, get_delay());
            $marks.each(function(idx, el) { $coltxt.mark($(this).text(), mark_opts); });
         }
      });
      var colclass = $('.' + active_column);
      if (colclass.hasClass('hidden')) { /* unhide the active text column, if necessary */
         colclass.removeClass('hidden');
         $('#max_width').click();
         $('#search_results').scrollTo(href, get_delay());
      }
   }
});

function col_scrollTo_paragraph(col,href){
   var paper = href.replace(/.U([0-9][0-9]*)_.*_.*/,'$1');
   var mod_idx = $('#' + col + 'mod').val();
   var $coltxt = $('#' + col + 'txt');
   if (colpaper_map[col] != paper) { /* need to load a different paper */
      $coltxt.load('text/' + mod_idx + '/p' + ("000" + paper).slice(-3) + '.html', function() {
         var title = $('#' + col + 'toc').find('.toc').find('.U' + paper + '_0_1').html();
         $('#' + col + 'title').html(title);
         colpaper_map[col] = paper;
         $coltxt.scrollTo(href, get_delay());
      });
   } else {
      $coltxt.scrollTo(href, get_delay());
   }
};

$('.coltxt').on('click', 'a', function(e) {
   e.preventDefault();
   var href = $(this).attr('href');
   current_paragraph = href;
   localStorage.setItem("current_paragraph", current_paragraph);
   var fnpat = /U\d{1,3}_\d{1,2}_\d{1,3}_\d+/;
   if (fnpat.exec(href)) $('#notes').scrollTo(href, get_delay());
   else {
      var this_column = e.delegateTarget;
      $('.coltxt').not(this_column).each(function() {
         var col = $(this).attr('id').replace('txt','');
         col_scrollTo_paragraph(col,href);
      });
      $(this_column).scrollTo(href, get_delay());
   }
});

var selection_column = '';

$('.coltxt').bind('mouseup', function(e) {
   selection_column = $(e.delegateTarget).attr('id').replace('txt','');
});

$('.colupdown').click(function() {
   var coltxt = '#' + $(this).parent().attr('id').replace('hdr','txt').replace('col5txt','search_results').replace('col6txt','notes');
   offset = $(this).attr('name') == 'up' ? 0 : $(coltxt)[0].scrollHeight;
   $(coltxt).scrollTo(offset, get_delay());
});

$('.colclose').click(function() { toggle_active_column($(this).attr('id').replace('close','')); });

var ts = localStorage.getItem("ts");
if (ts == undefined) {
   ts = '16px';
   localStorage.setItem("ts", ts);
}
$('.coltxt,#search_results,#notes').css('font-size', ts);

$('#ts').selectmenu({
   change: function(event, ui) {
      var ts = ui.item.value;
      localStorage.setItem("ts", ts);
      $('.coltxt,#search_results,#notes').css('font-size', ts);
   },
   width: 90
});

$('#ts').val(ts).selectmenu('refresh');

$('#language').selectmenu({
   change: function(event, ui) {
      document.cookie = 'lang=' + ui.item.value + '; expires=Fri, 31 Dec 9999 23:59:59 GMT';
      location.reload();
   },
   width: 140
});

$('#themes').selectmenu({
   change: function(event, ui) {
      document.cookie = 'theme=' + ui.item.value + '; expires=Fri, 31 Dec 9999 23:59:59 GMT';
      location.reload();
   },
   width: 150
});

$('#toc_expand_collapse').click(function(event) {
   var toc_id = $('#' + active_column + 'toc').find('.toc');
   $(toc_id).find('li.expanded').length != 0 ? $(toc_id).bonsai('collapseAll') : $(toc_id).bonsai('expandAll');
});

var ic = 1;

$('#ic').click(function(event) {
   ic = 1 - ic;
   $('#ic_lab').html(ic ? 'a = A' : 'a &#8800; A');
   $('#search_text').focus();
});

$('#max_height').click(function(event) {
   var ctl_height = $('#panels td').offset().top + 27;
   var data_height = $(window).height() - ctl_height;
   $('.data').height(data_height);
});

$('#max_width').click(function(event) {
   var $visible = $('.headers').not('.hidden');
   var ncolumns = $visible.length;
   if (ncolumns > 0) {
      var toc_max_width = 280; /* pixels. Increase this for a wider TOC column */
      var total_width = $(window).width();
      var column_width = total_width/ncolumns;
      var toc_column_width = column_width;
      if (!$('#col0hdr').hasClass('hidden') && column_width > toc_max_width) {
         toc_column_width = toc_max_width;
         if (ncolumns > 1) column_width = (total_width - toc_max_width)/(ncolumns - 1);
      }
      $visible.width(column_width);
      $('.toc_container,#col0hdr').width(toc_column_width);
   }
});

$('.colsize_controls').click();

$('.headers').each(function() {
   var col = $(this).attr('id').replace('hdr','');
   var visible = localStorage.getItem(col);
   if (visible == undefined) {
      visible = 1;
      localStorage.setItem(col, visible);
   }
   if (+visible === 0) toggle_active_column(col);
});

$('#search').click(function(event) {
   var html = $('#search_text').val().trim(); /* may contain html tags */
   var text = $('<div/>').html(html).text(); /* strip html tags, if any */
   if (!text) return;
   var availableHistory = getSearchHistory();
   var myMap = new Map(availableHistory);
   var o = GetSearchOptionsQuerySrt(text);
   var url_search_req = o.url_search_req;
   if (!myMap.has(text)) {
      myMap.set(text,url_search_req);
   } else {
      myMap.delete(text);
      myMap.set(text,url_search_req);
   }
   if (myMap.size>40) { //Ограничиваю 40-ка последними значениями
      var mapIter = myMap.keys();
      myMap.delete(mapIter.next().value);
   }
   localStorage.setObj('_searches',Array.from(myMap));
   UpdateSearchHistoryCombo();
   var mod_idx = $('#' + active_column + 'mod').val();
   var srt = /(\d{1,3}):(\d{1,2}).?(\d{1,3})?/; /* SRT ref 'Paper:Section.Paragraph' */
   var ref = srt.exec(text);
   if (ref) {
      var paper = ref[1];
      if (ref[3] == undefined) {
         if (ref[2] == 0)
            ref[3] = 1; /* beginning of the whole paper */
         else
             ref[3] = 0; /* section title */
      }
      var href = '.U' + paper + '_' + ref[2] + '_' + ref[3];
      col_scrollTo_paragraph(active_column,href);
   } else {
      if ($('.col5').hasClass('hidden')) toggle_active_column('col5');   //включаем колонку результатов, если была выключена

      $('#search_text').addClass('loading').prop('disabled', true);
      $('#search').button('disable');

      var search_req = o.ajax_search_req;
      $.ajax({url: search_req, dataType: 'json', success: function(data) {
         var json = JSON.parse(data);
         var _link = '' + location.pathname + url_search_req;
         $('#search_results').html(json.matches);
         $('#search_total').html('(' + json.match_count + '/' + json.par_count + ')<span class="ui-icon ui-icon-extlink"></span>');
         $('#col5title').wrap('<a href="'+_link+'"></a>');
         //$('#search_text').removeClass('loading').prop('disabled', false).val(json.text).focus();
         $('#search_text').removeClass('loading').prop('disabled', false);
         $('#search').button('enable');
         $('#' + active_column + 'txt').unmark();
      }, dataType: "html"});
   }
});

$(document).keydown(function(event) {
   var key = event.which, ctrl = event.ctrlKey, shift = event.shiftKey;
   //console.log("key=" + key);
   if (key == 112) { /* F1 */
      event.preventDefault();
      $('#help_button').click();
   } if (key == 13 && event.target.id == 'search_text') { /* ENTER in a search input box */
      event.preventDefault();
      $('#search').click();
   } else if (ctrl && key == 48) { /* Ctrl + 0 */
      event.preventDefault();
      toggle_active_column('col0');
   } else if (ctrl && shift && key == 49) { /* Ctrl + Shift + 1 */
      event.preventDefault();
      $('#col1rad').click();
   } else if (ctrl && key == 49) { /* Ctrl + 1 */
      event.preventDefault();
      toggle_active_column('col1');
   } else if (ctrl && shift && key == 50) { /* Ctrl + Shift + 2 */
      event.preventDefault();
      $('#col2rad').click();
   } else if (ctrl && key == 50) { /* Ctrl + 2 */
      event.preventDefault();
      toggle_active_column('col2');
   } else if (ctrl && shift && key == 51) { /* Ctrl + Shift + 3 */
      event.preventDefault();
      $('#col3rad').click();
   } else if (ctrl && key == 51) { /* Ctrl + 3 */
      event.preventDefault();
      toggle_active_column('col3');
   } else if (ctrl && shift && key == 52) { /* Ctrl + Shift + 4 */
      event.preventDefault();
      $('#col4rad').click();
   } else if (ctrl && key == 52) { /* Ctrl + 4 */
      event.preventDefault();
      toggle_active_column('col4');
   } else if (ctrl && key == 53) { /* Ctrl + 5 */
      event.preventDefault();
      toggle_active_column('col5');
   } else if (ctrl && key == 54) { /* Ctrl + 6 */
      event.preventDefault();
      toggle_active_column('col6');
   } else if (ctrl && key == 72) { /* Ctrl + H */
      event.preventDefault();
      $('#tabs_top').toggleClass('hidden');
      $('#max_height').click();
   } else if (ctrl && key == 66) { /* Ctrl + B */
      event.preventDefault();
      $('#max_width').click();
   } else if (ctrl && key == 77) { /* Ctrl + M */
      event.preventDefault();
      $('#max_height').click();
   } else if (ctrl && key == 69) { /* Ctrl + E */
      event.preventDefault();
      $('#expcpanel').click();
   } else if (ctrl && key == 83) { /* Ctrl + S */
      event.preventDefault();
      $('#search_control').toggleClass('hidden');
      $('#max_height').click();
   } else if (ctrl && key == 88) { /* Ctrl + X */
      event.preventDefault();
      seltext = window.getSelection().toString();
      if (seltext === '')
         $('#clear').click();
      else {
         $('#search_text').val(seltext);
         var saved_active_column = active_column;
         active_column = selection_column;
         var saved_search_mode = $('#search_mode').val();
         $('#search_mode').val('exact').selectmenu('refresh');
         $('#search').click();
         active_column = saved_active_column;
         $('#search_mode').val(saved_search_mode).selectmenu('refresh');
      }
   } else if (ctrl && key == 79) { /* Ctrl + O */
      event.preventDefault();
      $('#toc_expand_collapse').click();
   } else if (ctrl && key == 65) { /* Ctrl + A */
      event.preventDefault();
      $('#ic').click();
   } else if (ctrl && key == 80) { /* Ctrl + P */
      event.preventDefault();
      $('#tooltips').click();
   } else if (ctrl && shift && key == 75) {
      $('figure.private').show();
   } else if (ctrl && shift && key == 76) {
      $('figure.private').hide();
   } else return;
});

function getCookie(name) {
   var value = '; ' + document.cookie;
   var parts = value.split('; ' + name + '=');
   if (parts.length == 2) return parts.pop().split(';').shift();
   return "";
}

function toggle_active_column(col) {
   if (($('.headers').not('.hidden').length == 1) & (col == $('.headers').not('.hidden').attr('id').replace('hdr',''))) return;  // 20191029 Al Не даем закрыться последней колонке
   $('.' + col).toggleClass('hidden');
   if ($('#' + col + 'hdr').hasClass('hidden')) {
      localStorage.setItem(col, 0);
      if (active_column == col) {
         var newcol = $('.txthdr').not('.hidden').first().attr('id');
         if (newcol != undefined) {
            coltxtsw(newcol.replace('hdr','')).click();
         } else {
            coltxtsw(active_column).removeAttr('style');
            active_column = undefined;
         };
      }
   } else
      localStorage.setItem(col, 1);
   if ($('.headers').not('.hidden').length == 1)
      $('.colclose').addClass('hidden');
   else
      $('.colclose').removeClass('hidden');
   $('#max_width').click();
}

function load_notes(mod_idx) {
   $('#notes').load('text/' + mod_idx + '/notes.html', function(text, status) {
      if (status === 'success') {
         var i, n=0;
         for (i=0; i < text.length; i++) if (text[i] == '\n') n++;
         $('#notes_total').html('(' + n + ')');
      }
   });
}

function get_delay() {
   return $('#animations').is(':checked') ? 606 : 0;
}

function ContentLoaded() {
   var href = window.location.pathname.replace(/\//,'');
   var regex = /U(\d{1,3})_(\d{1,2})_(\d{1,3})_?\d*/;
   var ref = regex.exec(href);
   if (ref) {
      var paper = ref[1];
      if (ref[3] == undefined) {
         if (ref[2] == 0)
            ref[3] = 1; /* beginning of the whole paper */
         else
            ref[3] = 0; /* section title */
      }
      var href = '.U' + paper + '_' + ref[2] + '_' + ref[3];
      $('.txthdr').not('.hidden').each(function() {
         var col = $(this).attr('id').replace('hdr','');
         col_scrollTo_paragraph(col,href);
      });
   }
   else{
      var queryString = window.location.search;
      if (queryString != '') {
         SetSearchOptions(queryString);
         $('#search').click();
      }else{ //Пустая строка параметров URL
         //SetSearchOptions($('#combobox').children(':selected').val());
         if (current_paragraph != '') { // TODO: 20200816 Al Переделать текущий параграф на массив: Колонка:ТекПарагр.
            $('.txthdr').not('.hidden').each(function() {
               var col = $(this).attr('id').replace('hdr','');
               col_scrollTo_paragraph(col,current_paragraph);
            });
         }
      }
   }
}

$.widget('custom.combobox', {
   _create: function() {
      this.wrapper = $('<span>')
      .addClass('custom-combobox')
      .insertAfter(this.element);

      this.element.hide();
      this._createAutocomplete();
      this._createShowAllButton();
   },

   _createAutocomplete: function() {
      var selected = this.element.children(':selected'),
      value = selected.val() ? selected.text() : '';

      this.input = $('<input>')
      .appendTo(this.wrapper)
      .val(value)
      .attr('title','')
      .attr('id','search_text')
      .attr('placeholder',INPUT_SEARCH_STRING)
      //.addClass("custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left")
      .autocomplete({
         delay: 0,
         minLength: 0,
         source: $.proxy(this,'_source')
      })
      .tooltip({
         classes: {
            'ui-tooltip': 'ui-state-highlight'
         }
      });

      this._on(this.input, {
      autocompleteselect: function(event,ui) {
         ui.item.option.selected = true;
         SetSearchOptions(ui.item.option.value);
         this._trigger('select',event, {
            item: ui.item.option
         });
      },

      });
   },

   _createShowAllButton: function() {
      var input = this.input,
      wasOpen = false;
      var lang = getCookie('lang');
      if (lang == 'ru') showall = 'Огласить весь список';
      else if (lang == 'ua') showall = 'Оголосити весь перелік';
      else if (lang == 'en') showall = 'Show all items';
      else if (lang == 'fr') showall = 'Afficher toutes les recherches précédentes';

      $('<a>')
      .attr('tabIndex', -1 )
      .attr('title', showall)
      .tooltip()
      .appendTo(this.wrapper)
      .button({
         icons: {
            primary: 'ui-icon-triangle-1-s'
         },
         text: false
      })
      .removeClass('ui-corner-all')
      .addClass('custom-combobox-toggle ui-corner-right')
      .on('mousedown', function() {
         wasOpen = input.autocomplete('widget').is(':visible');
      })
      .on('click', function() {
         input.trigger('focus');

         // Close if already visible
         if (wasOpen) {
            return;
         }

         // Pass empty string as value to search for, displaying all results
         input.autocomplete('search','');
      });
   },

   _source: function(request,response) {
      var matcher = new RegExp($.ui.autocomplete.escapeRegex(request.term),'i');
      response(this.element.children('option').map(function() {
      var text = $(this).text();
      if (this.value && (!request.term || matcher.test(text)) )
         return {
            label: text,
            value: text,
            option: this
         };
      }) );
   },

   _destroy: function() {
      this.wrapper.remove();
      this.element.show();
   }
});

$('#combobox').combobox();

setTimeout(ContentLoaded,1500);
