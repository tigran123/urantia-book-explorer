var active_column = 'col1';
var colpaper_map = { 'col1': '000', 'col2': '000', 'col3': '000' };

$('#drafts').change(function() {
   document.cookie = 'drafts=' + ($(this).is(':checked') ? 1 : 0) + '; expires=Fri, 31 Dec 9999 23:59:59 GMT';
   location.reload();
});

var text_map = ['English: SRT 0.20',
                'English: British Study Edition',
                'Русский: UF 1997-1.9',
                'Български: UF 2013-1.0',
                'Deutsch: UF 2015-1'];

var info_map = ['The English text of The&nbsp;Urantia&nbsp;Book is in the Public&nbsp;Domain',
                'Ed.&nbsp;Tigran&nbsp;Aivazian,&nbsp;Bibles.org.uk',
                'Copyright&nbsp;&#169;&nbsp;Urantia&nbsp;Foundation',
                'Copyright&nbsp;&#169;&nbsp;Urantia&nbsp;Foundation',
                'Copyright&nbsp;&#169;&nbsp;Urantia&nbsp;Foundation'];

if ($('#drafts').is(':checked')) {
   text_map.push('Русский: UBSGNY 2017-1');
   info_map.push('Copyright&nbsp;&#169;&nbsp;Urantia&nbsp;Book&nbsp;Society&nbsp;of&nbsp;Greater&nbsp;New&nbsp;York');
}

var text_options = '';
$.each(text_map, function(idx, item) { text_options += '<option value=' + idx + ' title="' + info_map[idx] + '">' + text_map[idx] + '</option>'; });

$('.buttons').button();
$('#tabs').tabs();
$('#tabs').on('click', 'a', function(e) {
   switch(e.target.id) {
      case 'home':
         $('#search_text').focus();
         break;
      case 'forum':
      case 'quiz':
         location.href = e.target.href;
   }
});

$('#' + active_column + 'hdr').css('border', 'solid darkblue 2px');

$('.colsw').on('click', function() {
   var col = $(this).attr('id').replace('rad','');
   $('.' + col).toggleClass('hidden');
   if ($('#' + col + 'hdr').hasClass('hidden'))
      document.cookie = col + '=0; expires=Fri, 31 Dec 9999 23:59:59 GMT';
   else
      document.cookie = col + '=1; expires=Fri, 31 Dec 9999 23:59:59 GMT';
   $('#max_width').click();
});

$('.coltxtsw').on('click', function() {
   $('#' + active_column + 'toc').addClass('hidden');
   var col = $(this).attr('id').replace('rad','');
   $('.' + col).removeClass('hidden');
   document.cookie = col + '=1; expires=Fri, 31 Dec 9999 23:59:59 GMT';
   active_column = col;
   var mod_idx = $('#' + col + 'mod').val();
   $('#notes').load('text/' + mod_idx + '/notes.html');
   $('#' + col + 'toc').removeClass('hidden');
   $('.txthdr').css('border', 'solid darkgrey 2px');
   $('#' + col + 'hdr').css('border', 'solid darkblue 2px');
   $('#max_width').click();
});

$('.colmod').html(text_options).selectmenu({
  change: function(event, ui) {
     var col = $(this).attr('id').replace('mod', '');
     var mod_idx = ui.item.value;
     var paper = colpaper_map[col];
     var paper_num = paper.replace(/0*(..*)/,'$1'); /* strip the leading zeros */
     $('#' + col + 'txt').load('text/' + mod_idx + '/p' + paper + '.html');
     $('#' + col + 'toc').load('text/' + mod_idx + '/toc.html', function() {
        var toc = $(this).find('.toc');
        toc.bonsai();
        $('#' + col + 'title').html(toc.find('.U' + paper_num + '_0_1').html());
        document.cookie = col + 'mod=' + mod_idx + '; expires=Fri, 31 Dec 9999 23:59:59 GMT';
     });
  },
  width: 228
}).each(function() {
   var cnum = $(this).attr('id').replace(/col([0-9][0-9]*)mod/,'$1');
   var mod_idx = getCookie('col' + cnum + 'mod');
   if (mod_idx == 5 && !$('#drafts').is(':checked')) mod_idx = 0;
   if (mod_idx === undefined) {
      switch(+cnum) {
         case 1: mod_idx = 1; break;
         case 2: mod_idx = 2; break;
         case 3: mod_idx = 0; break;
         default: mod_idx = 1; break;
      }
   }
   $(this).val(mod_idx).selectmenu('refresh');
});

$('.coltitle').click(function(e) { $('#' + e.currentTarget.id.replace('title','rad')).click(); });

$('.coltxt').each(function() {
   var col = $(this).attr('id').replace('txt', '');
   var toc_id = '#' + col + 'toc';
   var mod_idx = $('#' + col + 'mod').val();
   var paper = colpaper_map[col];
   var paper_num = paper.replace(/0*(..*)/,'$1'); /* strip the leading zeros */
   $(this).load('text/' + mod_idx + '/p' + paper + '.html');
   if (col == active_column) $('#notes').load('text/' + mod_idx + '/notes.html');
   $(toc_id).load('text/' + mod_idx + '/toc.html', function() {
      var toc = $(this).find('.toc');
      toc.bonsai();
      $('#' + col + 'title').html(toc.find('.U' + paper_num + '_0_1').html());
      if (active_column == col) $(toc_id).removeClass('hidden');
      /* XXX: the following code is executed three times, but if we use deferred functions we could run it only once */
      if (!$('#tooltips').is(':checked')) { $(document).tooltip('option', 'disabled', true); }
   });
});

$('.headers').each(function() {
   var col = $(this).attr('id').replace('hdr','');
   var visible = getCookie(col);
   if (+visible === 0) toggle_active_column(col);
});

$('#search_part').selectmenu({change: function() { $('#search_text').focus(); }, width: 120});
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

$('#animations').change(function() {
   document.cookie = 'animations=' + ($(this).is(':checked') ? 1 : 0) + '; expires=Fri, 31 Dec 9999 23:59:59 GMT';
});

$('#help').draggable();
$('#help_button').click(function(event) { $('#help').toggleClass('hidden'); $('#search_text').focus();});
$('#clear').click(function(event) { $('#search_text').val('').focus(); });

$('.toc_container,#search_results,#notes').on('click', 'a', function(e) {
  e.preventDefault();
  var delay = $('#animations').is(':checked') ? 606 : 0;
  var href = $(this).attr('href');
  var paper_num = href.replace(/.U([0-9][0-9]*)_.*_.*/,'$1');
  var paper = ("000" + paper_num).slice(-3);
  var $coltxt = $('#' + active_column + 'txt');
  var $marks = $(this).parent().find('mark');
  var mark_opts = {"accuracy": "exact", "separateWordSearch": false, "acrossElements": true};
  if (colpaper_map[active_column] != paper) { /* need to load a different paper */
     var mod_idx = $('#' + active_column + 'mod').val();
     $coltxt.load('text/' + mod_idx + '/p' + paper + '.html', function() {
        var title = $('#' + active_column + 'toc').find('.toc').find('.U' + paper_num + '_0_1').html();
        $('#' + active_column + 'title').html(title);
        colpaper_map[active_column] = paper;
        $coltxt.scrollTo(href, delay);
        $marks.each(function() { $coltxt.mark($(this).text(), mark_opts); });
     });
  } else {
     $coltxt.scrollTo(href, delay);
     $marks.each(function(idx, el) { $coltxt.mark($(this).text(), mark_opts); });
  }
  var colclass = $('.' + active_column);
  if (colclass.hasClass('hidden')) { /* unhide the active text column, if necessary */
      colclass.removeClass('hidden');
      $('#max_width').click();
      $('#search_results').scrollTo(href, delay);
  }
  $('#search_text').focus();
});

$('.coltxt').on('click', 'a', function(e) {
  e.preventDefault();
  var this_column = e.delegateTarget;
  var delay = $('#animations').is(':checked') ? 606 : 0;
  var href = $(this).attr('href');
  var paper_num = href.replace(/.U([0-9][0-9]*)_.*_.*/,'$1');
  var paper = ("000" + paper_num).slice(-3);
  $('.coltxt').not(this_column).each(function() {
      var col = $(this).attr('id').replace('txt','');
      var mod_idx = $('#' + col + 'mod').val();
      var $coltxt = $('#' + col + 'txt');
      if (colpaper_map[col] != paper) { /* need to load a different paper */
         $coltxt.load('text/' + mod_idx + '/p' + paper + '.html', function() {
             var title = $('#' + col + 'toc').find('.toc').find('.U' + paper_num + '_0_1').html();
             $('#' + col + 'title').html(title);
             colpaper_map[col] = paper;
             $coltxt.scrollTo(href, delay);
         });
      } else {
         $coltxt.scrollTo(href, delay);
      }
  });
  $(this_column).scrollTo(href, delay);
  $('#search_text').focus();
});

$('.colupdown').click(function() {
  var coltxt = '#' + $(this).parent().attr('id').replace('hdr','txt');
  var delay = $('#animations').is(':checked') ? 606 : 0;
  offset = $(this).attr('name') == 'up' ? 0 : $(coltxt)[0].scrollHeight;
  $(coltxt).scrollTo(offset, delay);
});

$('.colclose').click(function() { toggle_active_column($(this).attr('id').replace('close','')); });

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
  width: 200
});

$('#toc_expand_collapse').click(function(event) {
    var toc_id = $('#' + active_column + 'toc').find('.toc');
    $(toc_id).find('li.expanded').length != 0 ? $(toc_id).bonsai('collapseAll') : $(toc_id).bonsai('expandAll');
    $('#search_text').focus();
});

var ic = 1;

$('#ic').click(function(event) {
   $('#ic_lab').html(ic ? 'a &#8800; A' : 'a = A');
   ic = 1 - ic;
   $('#search_text').focus();
});

$('#max_height').click(function(event) {
    var ctl_height = $('#panels td').offset().top + 27;
    var data_height = $(window).height() - ctl_height;
    $('.data').height(data_height);
    $('#search_text').focus();
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
    $('#search_text').focus();
});

$('.colsize_controls').click();

$('#search').click(function(event) {
    var html = $('#search_text').val().trim(); /* may contain html tags */
    var text = $('<div/>').html(html).text(); /* strip html tags, if any */
    if (text) {
       var mod_idx = '&mod_idx=' + $('#' + active_column + 'mod').val();
       var search_part = '&search_part=' + $('#search_part').val();
       var search_range = '&search_range=' + $('#search_range').val();
       var search_req = "search.php" + "?text=" + encodeURIComponent(text) + mod_idx + "&ic=" + ic + search_part + search_range;
       $('#search_text').addClass('loading').prop('disabled', true);
       $('#search').button('disable');
       $.ajax({url: search_req, dataType: 'json', success: function(data) {
          var json = JSON.parse(data);
          $('#search_results').html(json.matches);
          $('#search_total').html('&nbsp;(' + json.match_count + '/' + json.par_count + ')');
          $('#search_text').removeClass('loading').prop('disabled', false).focus();
          $('#search').button('enable');
          $('#' + active_column + 'txt').unmark();
       }, dataType: "html"});
    } else
       $('#clear').click();
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
   } else if (ctrl && key == 52) { /* Ctrl + 4 */
      event.preventDefault();
      toggle_active_column('col4');
   } else if (ctrl && key == 53) { /* Ctrl + 5 */
      event.preventDefault();
      toggle_active_column('col5');
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
   } else if (ctrl && key == 83) { /* Ctrl + S */
      event.preventDefault();
      $('#controls').toggleClass('hidden');
      $('#max_height').click();
   } else if (ctrl && key == 88) { /* Ctrl + X */
      event.preventDefault();
      $('#clear').click();
   } else if (ctrl && key == 79) { /* Ctrl + O */
      event.preventDefault();
      $('#toc_expand_collapse').click();
   } else if (ctrl && key == 65) { /* Ctrl + A */
      event.preventDefault();
      $('#ic').click();
   } else if (ctrl && key == 80) { /* Ctrl + P */
      event.preventDefault();
      $('#tooltips').click();
   } else return;
});

function getCookie(name) {
   var value = "; " + document.cookie;
   var parts = value.split("; " + name + "=");
   if (parts.length == 2) return parts.pop().split(";").shift();
}

function toggle_active_column(col) {
   $('.' + col).toggleClass('hidden');
   if ($('#' + col + 'hdr').hasClass('hidden')) {
      document.cookie = col + '=0; expires=Fri, 31 Dec 9999 23:59:59 GMT';
      if (active_column == col) {
         var newcol = $('.txthdr').not('.hidden').first().attr('id');
         if (newcol != undefined) $('#' + newcol.replace('hdr','rad')).click();
      }
   } else
      document.cookie = col + '=1; expires=Fri, 31 Dec 9999 23:59:59 GMT';
   $('#max_width').click();
}
