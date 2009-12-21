$(function() { 
    // Menu Bar, and other buttons which show/hide a dialog
    $("a.toggleDialog").live('click', function() {
        $(this).toggleDialog();
    });
    // Vary the font-size
    $("select#config_webui").live('change', function() {
        var f = $(this).val();
        $.cookie('twFontSize', f);
        switch (f) {
        case 'Small':
            $("body").css('font-size', '75%');
            break;
        case 'Medium':
            $("body").css('font-size', '85%');
            break;
        case 'Large':
            $("body").css('font-size', '100%');
            break;
        }
    });
    // Filter Bar - Buttons
    $("ul#filterbar_container li:not(#filter_bytext)").click(function() {
        if($(this).is('.selected'))
            return;
        $(this).addClass('selected').siblings().removeClass("selected");
        var filter = this.id;
        $("div#torrentlist_container").show(function() {
            var tor = $("li.torrent").fadeIn("slow");
            switch (filter) {
            case 'filter_all':
	 	if($('.transmission').is(":visible")) {
			$('.transmission').fadeOut("normal", function() {
				$('.feed').fadeIn("normal");
			})
		}
		break;
            case 'filter_matching':
	 	if($('.transmission').is(":visible")) {
			$('.transmission').fadeOut("normal", function() {
				$('.feed').fadeIn("normal");
			})
		}
                tor.filter(".match_nomatch").hide();
                break;
            case 'filter_downloading':
	 	if($('.transmission').is(":visible")) {
			$('.transmission').fadeOut("normal", function() {
				$('.feed').fadeIn("normal");
			})
		}
                tor.not('.match_downloading').hide();
                break;
            case 'filter_downloaded':
	 	if($('.transmission').is(":visible")) {
			$('.transmission').fadeOut("normal", function() {
				$('.feed').fadeIn("normal");
			})
		}
                tor.not('.match_cachehit, .match_match, .match_downloaded').hide();
                break;
	    case 'filter_transmission':
		if($('.feed').is(":visible")) { 
			$('.feed').hide() 
			$('.transmission').fadeIn("normal");
		}
		break;
            }
            tor.markAlt().closest("#torrentlist_container").fadeIn("normal");
        });
    });
    // Filter Bar -- By Text
    $("input#filter_text_input").keyup(function() {
        var filter = $(this).val().toLowerCase();
        $("li.torrent").addClass('hidden_bytext').each(function() {
            if ($(this).find("span.torrent_name").text().toLowerCase().match(filter)) {
                $(this).removeClass('hidden_bytext');
            }
        }).markAlt(); 
    });
    // Switching visible items for different clients
    $(function(){
    $("select#client").live('change', function() {
        $(".favorite_seedratio, #config_folderclient").css("display", "none");
        $("#torrent_settings").css("display", "block");
        var target = 'http://'+location.hostname;
        switch ($(this).val()) {
        case 'folder':
            $("#config_watchdir, #config_savetorrent, #config_deepdir, #torrent_settings, div.favorite_savein, #config_tr_user, #config_tr_password, #config_tr_host, #config_tr_port").css("display", "none");
            $("#config_folderclient, #config_downloaddir").css("display", "block");
            $("form.favinfo, ul.favorite");
            target = '#';
            break;
        case 'Transmission':
            $("#config_tr_user, #config_tr_password, #config_tr_host, #config_tr_port, #config_downloaddir, #config_watchdir, #config_savetorrent, #config_deepdir, div.favorite_seedratio, div.favorite_savein").css("display", "block");
            $("ul.favorite").css("height", 245);
            target += ':9091/transmission/web/';
            break;
        }
        $("#webui a").text($(this).val())[0].href = target;
    })});
    // Perform the first load of the dynamic information
    $.get('torrentwatch.php', '', $.loadDynamicData, 'html');

    // Configuration, wizard, and update/delete favorite ajax submit
    $("a.submitForm").live('click', function(e) {
        e.stopImmediatePropagation();
        $.submitForm(this);
    });
    // Clear History ajax submit
    $("a#clearhistory").live('click', function() {
      $.get(this.href, '', function(html) {
          // $(html).html() is used to strip the outer tag(<div#history></div>) and get the children
          $("div#history").html($(html).html());
      }, 'html');
      return false;
    });
    // Clear Cache ajax submit
    $("#clear_cache a:not(.toggleDialog)").click(function() {
      $.get(this.href, '', $.loadDynamicData, 'html');
      return false;
    });

      Math.formatBytes = function(bytes) {
          var size;
          var unit;

          // Terabytes (TB).
          if ( bytes >= 1099511627776 ) {
              size = bytes / 1099511627776;
                      unit = ' TB';

          // Gigabytes (GB).
         } else if ( bytes >= 1073741824 ) {
             size = bytes / 1073741824;
                     unit = ' GB';

         // Megabytes (MB).
         } else if ( bytes >= 1048576 ) {
             size = bytes / 1048576;
                     unit = ' MB';

         // Kilobytes (KB).
         } else if ( bytes >= 1024 ) {
             size = bytes / 1024;
                     unit = ' KB';

         // The file is less than one KB
         } else {
             size = bytes;
                     unit = ' bytes';
         }

              // Single-digit numbers have greater precision
              var precision = 2;
             size = Math.roundWithPrecision(size, precision);

             // Add the decimal if this is an integer
             if ((size % 1) == 0 && unit != ' bytes') {
                     size = size + '.0';
             }

         return size + unit;
     };
     Math.roundWithPrecision = function(floatnum, precision) {
         return Math.round ( floatnum * Math.pow ( 10, precision ) ) / Math.pow ( 10, precision );
     };

     $(document).ready(function() {
                setInterval(function() {
		window.torInfo = 1;
		  $.getJSON('/torrentwatch.php', {'getClientData': 1, 'recent': 1}, function(json) {
		    $.each(json.arguments.torrents, function(i, item){
			var Ratio = Math.roundWithPrecision(item.uploadedEver/item.downloadedEver,2);
			var Percentage = Math.roundWithPrecision(((item.totalSize-item.leftUntilDone)/item.totalSize)*100,2)
			if(!(Ratio > 0)) var Ratio = 0;
			if(!(Percentage > 0)) var Percentage = 0;
		    	var clientData = "DL:&nbsp;" + Math.formatBytes(item.totalSize-item.leftUntilDone) + "&nbsp;of&nbsp;"
			    + Math.formatBytes(item.totalSize) + "&nbsp;(" + Percentage + "%)&nbsp;&nbsp;-&nbsp;&nbsp;Ratio:&nbsp;" + Ratio ;
			$('.tor_' + item.hashString).html(clientData);
                        if(item.leftUntilDone == 0) $('.' + item.hashString + '.match_downloading').removeClass('match_downloading').addClass('match_cachehit');
		    })
		    $.each(json.arguments.removed, function(i, item){
			if($('#tr_id_' + item).length != 0) {
				$('div#tr_id_' + item).remove();
				$('div.torInfo.tr_id_' + item).remove();
				$('p.tr_id_' + item).remove();
				$('td.buttons.tr_id_' + item).removeClass('match_downloading match_downloaded match_cachehit').addClass('match_old_download');
			}
		    })
		  })
		  $('.torrent').removeClass('active');
                  window.torInfo = null;
		},10000)
    });

    // Ajax progress bar
    $("#progressbar").ajaxStart(function() {
    if(!(window.torInfo)) {
      $(this).show();
    }
    }).ajaxStop(function() {
      $(this).hide();
    });
});

(function($) {
    var current_favorite, current_dialog;
    // Remove old dynamic content, replace it with passed html(ajax success function)
    $.loadDynamicData = function(html) {
        $("#dynamicdata").remove();
        setTimeout(function() {
            var dynamic = $("<div id='dynamicdata' class='dyndata'/>");
            // Use innerHTML because some browsers choke with $(html) when html is many KB
            dynamic[0].innerHTML = html;
            dynamic.find("ul.favorite > li").initFavorites().end()
                    .find("form").initForm().end().initConfigDialog().appendTo("body");
            setTimeout(function() {
                var container = $("#torrentlist_container");
                if(container.length == 0) {
                    current_dialog = '#welcome1';
                    $(current_dialog).show();
                } else {
                    container.slideDown(400, function() {
	    	    $('#torrentlist_container').height($(window).height() - $('#torrentlist_container').attr('offsetTop'));
                    });
                }
            }, 50);
        }, 100);
    };
    $(window).resize(function() {
	    $('#torrentlist_container').height($(window).height() - $('#torrentlist_container').attr('offsetTop'));
    })
    $.submitForm = function(button) {
        var form;
        if($(button).is('form')) { // User pressed enter
            form = $(button);
            button = form.find('a')[0];
        } else
            form = $(button).closest("form");
        $.get(form.get(0).action, form.buildDataString(button), $.loadDynamicData, 'html');
    }; 
    $.fn.toggleDialog = function() {
        this.each(function() {
            var last = current_dialog === '#' ? '' : current_dialog;
            var target = this.hash === '#' ? '#'+$(this).closest('.dialog_window').id : this.hash;
            current_dialog = last === target ? '' : this.hash;
            if (last) {
                $(last).fadeOut("slow");
            }
            if (current_dialog && this.hash != '#') {
                $(current_dialog).fadeIn("slow");
            }
        });
        return this;
    };
    $.fn.initFavorites = function() {
        var selector = this.selector;
        setTimeout(function() {
            $(selector + ":first a").toggleFavorite();
        }, 300);
        return this.not(":first").tsort("a").end().click(function() {
            $(this).find("a").toggleFavorite();
        });
    };
    $.fn.initForm = function() {
        this.submit(function(e) {
            e.stopImmediatePropogation();
            $.submitForm(this);
            return false;
        });
        var f = $.cookie('twFontSize');
        if(f)
            this.find("#config_webui").val(f).change();
        return this;
    };
    $.fn.toggleFavorite = function() {
        this.each(function() {
            var last = current_favorite;
            current_favorite = this.hash;
            if (!last) {
                $(current_favorite).show();
            } else {
                $(last).fadeOut(400, function() {
                    $(current_favorite).fadeIn(400);
                });
            }
        });
        return this;
    };
    $.fn.initConfigDialog = function() {
        setTimeout(function() {
            $('select#client').change();
        }, 500);
        return this;
    };
    $.fn.buildDataString = function(buttonElement) {
        var dataString = $(this).filter('form').serialize();
        if(buttonElement) {
            dataString += (dataString.length == 0 ? '' : '&' ) + 'button=' + buttonElement.id;
        }
        return dataString;
    };
    $.fn.markAlt = function() {
      return this.removeClass('alt').filter(":visible:even").addClass('alt');
    };

    $.addFavorite = function(url) {
	$.get(url, '', $.loadDynamicData, 'html');
    }
    
    $.dlTorrent = function(url) {
	$.get(url);
	setTimeout(function() {
	    location.reload()
	},1700);
     }
})(jQuery);
setTimeout(function() {
    location.reload()
},300000);
