$(function() {
    // Menu Bar, and other buttons which show/hide a dialog
    $("a.toggleDialog").live('click',
    function() {
        $(this).toggleDialog();
    });
    // Vary the font-size
    changeFontSize = function(fontSize) {
        var f = fontSize;
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
    };

    displayFilter = function(filter) {
        $.cookie('TWFILTER', filter);
        var tor = $("li.torrent").show();
        if (filter == 'all') {
            if ($('.transmission').is(":visible")) {
                $('.feed').show();
                $('.transmission').hide();
            }
            tor.markAlt().closest(".feed div.feed");
        } else if (filter == 'matching') {
            if ($('.transmission').is(":visible")) {
                $('.feed').show();
                $('.transmission').hide();
            }
            tor.filter(".match_nomatch").hide();
            tor.markAlt().closest(".feed div.feed");
        } else if (filter == 'downloading') {
            if ($('.transmission').is(":visible")) {
                $('.feed').show();
                $('.transmission').hide();
            }
            tor.not('.match_downloading').hide();
            tor.markAlt().closest(".feed div.feed");
        } else if (filter == 'downloaded') {
            if ($('.transmission').is(":visible")) {
                $('.feed').show();
                $('.transmission').hide();
            }
            tor.not('.match_cachehit, .match_match, .match_downloaded').hide();
            tor.markAlt().closest(".feed div.feed");
        } else if (filter == 'transmission') {
            if ($('.feed').is(':visible')) {
                $('.transmission').show();
                $('.feed').hide();
            }
        }
        $('#filter_' + filter).addClass('selected').siblings().removeClass("selected");
    };
    // Filter Bar - Buttons
    $("ul#filterbar_container li:not(#filter_bytext)").click(function() {
        if ($(this).is('.selected')) {
            return;
        }
        var filter = this.id;
        $("div#torrentlist_container").show(function() {
            switch (filter) {
            case 'filter_all':
                displayFilter('all');
                break;
            case 'filter_matching':
                displayFilter('matching');
                break;
            case 'filter_downloading':
                displayFilter('downloading');
                break;
            case 'filter_downloaded':
                displayFilter('downloaded');
                break;
            case 'filter_transmission':
                displayFilter('transmission');
                break;
            }
        });
    });


    // Filter Bar -- By Text
    $("input#filter_text_input").keyup(function() {
        var filterText = $(this).val().toLowerCase();
        $("li.torrent").addClass('hidden_bytext').each(function() {
            if ($(this).find("span.torrent_name").text().toLowerCase().match(filterText)) {
                $(this).removeClass('hidden_bytext');
            }
        }).markAlt();
    });
    
    // Switching visible items for different clients    
    changeClient = function(client) {
        $(".favorite_seedratio, #config_folderclient").css("display", "none");
        $("#torrent_settings").css("display", "block");
        var target = 'http://' + location.hostname;
        switch (client) {
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
        if(client != 'folder') { 
            alert(target);
            $("#webui a").text(client)[0].href = target;
            $('li#webui').show();
        } else {
            $('li#webui').hide();
        }
    };
    
    // Perform the first load of the dynamic information
    $.get('torrentwatch.php', '', $.loadDynamicData, 'html');

    // Configuration, wizard, and update/delete favorite ajax submit
    $("a.submitForm").live('click',
    function(e) {
        e.stopImmediatePropagation();
        $.submitForm(this);
        $('div#' + this.parentNode.id).hide();
    });
    // Clear History ajax submit
    $("a#clearhistory").live('click',
    function() {
        $.get(this.href, '',
        function(html) {
            // $(html).html() is used to strip the outer tag(<div#history></div>) and get the children
            $("div#history").html($(html).html());
        },
        'html');
        return false;
    });
    // Clear Cache ajax submit
    //$("a.clear_cache a:not(.toggleDialog)").click(function() {
    $('a.clear_cache').live('click',
    function(e) {
        $.get(this.href, '', $.loadDynamicData, 'html');
        return false;
    });

    Math.formatBytes = function(bytes) {
        var size;
        var unit;

        // Terabytes (TB).
        if (bytes >= 1099511627776) {
            size = bytes / 1099511627776;
            unit = ' TB';

            // Gigabytes (GB).
        } else if (bytes >= 1073741824) {
            size = bytes / 1073741824;
            unit = ' GB';

            // Megabytes (MB).
        } else if (bytes >= 1048576) {
            size = bytes / 1048576;
            unit = ' MB';

            // Kilobytes (KB).
        } else if (bytes >= 1024) {
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
        if ((size % 1) === 0 && unit != ' bytes') {
            size = size + '.0';
        }

        return size + unit;
    };

    Math.roundWithPrecision = function(floatnum, precision) {
        return Math.round(floatnum * Math.pow(10, precision)) / Math.pow(10, precision);
    };

    torStartStopToggle = function(torHash) {
        var curObject = $('li.' + torHash + ' p.torStart');
        if (curObject.is(":visible")) {
            curObject.hide();
        } else {
            curObject.show();
        }
        curObject = $('li.' + torHash + ' p.torStop');
        if (curObject.is(":visible")) {
            curObject.hide();
        } else {
            curObject.show();
        }
        curObject = null;
    };

    getAllClientData = function() {
        $.getJSON('torrentwatch.php', {
            'getClientData': 1,
            'recent': 0
        },
        function(json) {
            setTimeout(function() {
                processClientData(json, 0);
            },
            50);
        });
    };

    getRecentClientData = function() {
        window.torInfo = 1;
        $.getJSON('torrentwatch.php', {
            'getClientData': 1,
            'recent': 1
        },
        function(json) {
            setTimeout(function() {
                processClientData(json, 1);
                $.each(json['arguments']['removed'],
                function(i, item) {
                    if ($('li.clientId_' + item).length !== 0) {
                        $('li.clientId_' + item + ' div.torInfo').remove();
                        $('li.clientId_' + item + ' p.activeTorrent').hide();
                        $('li.clientId_' + item + ' p.dlTorrent').show();
                        $('li.clientId_' + item + ' td.buttons').removeClass('match_downloading match_downloaded match_cachehit')
                        .addClass('match_old_download');
                        $('li.clientId_' + item).removeClass('clientId_' + item);
                    }
                    if ($('#transmission_data li#clientId_' + item).length !== 0) {
                        $('#transmission_data li#clientId_' + item).remove();
                    }
                });
            },
            50);
        });
        window.torInfo = null;
    };

    toggleTorMove = function(id) {
        var curObject = $('div#move_' + id);
        if (curObject.is(":visible")) {
            curObject.hide();
        } else {
            curObject.show();
        }
        curObject = null;
    };

    getTransmissionList = function(item, clientData) {
        var transmissionList =
        '<li id="clientId_' + item.id + '" class="torrent match_transmission ' + item.hashString + '">' +
        '<table width="100%" cellspacing="0"><tr><td class="buttons left match_transmission">' +
        '<p title="Resume download" class="button torStart hidden"><img height=10 src="images/tor_start.png" /></p>' +
        '<p title="Pause download" class="button torStop"><img height=10 src="images/tor_pause.png" /></p>' +
        '<p title="Delete torrent but keep data" class="button torDel"><img height=10 src="images/tor_stop.png" /></p>' +
        '</td><td class="buttons right match_transmission">' +
        '<p title="Set location or move torrent data.&#13;Current loaction: ' + item.downloadDir + '" class="button torMove">' +
        '<img height=10 src="images/tor_move.png" /></p>' +
        '<p title="Delete torrent and its data" class="button torTrash"><img height=10 src="images/tor_trash.png" /></p>' +
        '</td><td class="torrent_name"><span class="torrent_name">' + item.name + '</span>' +
        '<span class="dateAdded hidden">' + item.addedDate + '</span>' +
        '<div id=tor_' + item.id + ' class="torInfo tor_' + item.hashString + '">' + clientData + '</div>' +
        '<div id="move_' + item.id + '" class="move_data hidden">' +
        '<input id="moveTo' + item.id + '" type="text" class="text" name="moveTo" value="' + item.downloadDir + '" />' +
        '<a class="move" id="Move" href="#">Move</a>' +
        '<a class="close" href="#">-</a>' +
        '</div></td></tr></table></li>';

        if ($('#transmission_list li#clientId_' + item.id).length === 0) {
            $('#transmission_list').append(transmissionList);

            $('li#clientId_' + item.id + ' .torStart').click(function() {
                $.stopStartTorrent('start', item.hashString);
            });
            $('li#clientId_' + item.id + ' .torStop').click(function() {
                $.stopStartTorrent('stop', item.hashString);
            });
            $('li#clientId_' + item.id + ' .torDel').click(function() {
                $.delTorrent(item.hashString, 'false');
            });
            $('li#clientId_' + item.id + ' .torTrash').click(function() {
                $.delTorrent(item.hashString, 'true');
            });
            $('li#clientId_' + item.id + ' a.close').click(function() {
                toggleTorMove(item.id);
            });
            $('li#clientId_' + item.id + ' a.move').click(function() {
                $.moveTorrent(item.id, $('input#moveTo' + item.id)[0].value, item);
            });
        }
    };

    processClientData = function(json, recent) {
        if (json === null) {
            return;
        }
        $.each(json['arguments']['torrents'],
        function(i, item) {
            var Ratio = Math.roundWithPrecision(item.uploadedEver / item.downloadedEver, 2);
            var Percentage = Math.roundWithPrecision(((item.totalSize - item.leftUntilDone) / item.totalSize) * 100, 2);

            if (! (Ratio > 0)) {
                Ratio = 0;
            }
            if (! (Percentage > 0)) {
                Percentage = 0;
            }

            var liClass;
            var clientData;
            if (item.status == 1) {
                clientData = 'Waiting for peers';
                liClass = 'waiting';
            } else if (item.status == 2) {
                clientData = 'Verifying files (' + Percentage + '%)';
                liClass = 'verifying';
            } else if (item.status == 4) {
                clientData = 'Downloading from ' + item.peersSendingToUs + ' of ' +
                item.peersConnected + ' peers: ' +
                Math.formatBytes(item.totalSize - item.leftUntilDone) + ' of ' +
                Math.formatBytes(item.totalSize) +
                ' (' + Percentage + '%)  -  Ratio: ' + Ratio;
                liClass = "downloading";
            } else if (item.status == 8) {
                clientData = 'Seeding to ' + item.peersGettingFromUs + ' of ' +
                item.peersConnected + ' peers  -  Ratio: ' + Ratio;
                liClass = 'alt';
            } else if (item.status == 16) {
                clientData = "Paused";
                liClass = 'paused';
            }

            getTransmissionList(item, clientData);

            $('li#clientId_' + item.id + ' .torMove').click(function() {
                toggleTorMove(item.id);
            });

            $('li.' + item.hashString + ' div.torInfo').html(clientData);
            $('li.' + item.hashString).addClass('clientId_' + item.id);

            if (item.status == 16) {
                $('li.' + item.hashString + ' p.torStop').hide();
                $('li.' + item.hashString + ' p.torStart').show();
            } else if (recent == 1) {
                var curTorrent = $('li.' + item.hashString + ' p.torStart');
                setTimeout(function() {
                    if (curTorrent.is(":visible")) {
                        torStartStopToggle(item.hashString);
                    }
                }, 100);
            }

            $('#transmission_list li#clientId_' + item.id)
                .removeClass('paused downloading verifying waiting alt').addClass(liClass)

            if (item.leftUntilDone === 0) {
                $('.' + item.hashString + '.match_downloading')
                .removeClass('match_downloading').addClass('match_cachehit');
            }
        });
        $('#transmission_list>li').tsort('span.dateAdded', {
            order: 'desc'
        });
    };

    $(document).ready(function() {
        setInterval(function() {
            getRecentClientData();
        },
        6000);
    });

    // Ajax progress bar
    $("#progressbar").ajaxStart(function() {
        if (! (window.torInfo)) {
            $(this).show();
        }
    }).ajaxStop(function() {
        $(this).hide();
    });
});

 (function($) {
    var current_favorite,
    current_dialog;
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
                if (container.length === 0) {
                    current_dialog = '#welcome1';
                    $(current_dialog).show();
                } else {
                    container.slideDown(400,
                    function() {
                        $('#torrentlist_container').height($(window).height() - $('#torrentlist_container').attr('offsetTop'));
                    });
                }
            },
            50);
            var filter = $.cookie('TWFILTER');
            if (! (filter)) {
                filter = 'all';
            }
            getAllClientData();
            setTimeout(function() {
                if ($('#transmission_data').length > 0) {
                    $('a#torClient ').show().html('Transmission');
                } else {
                    $('a#torClient').hide();
                    $('p.activeTorrent.delete').hide();
                    $('p.activeTorrent.trash').hide();
                    if(filter == 'transmission') {
                        filter = 'all';
                    }
                }
                
                displayFilter(filter);
            },
            100);
        },
        100);
    };
    $(window).resize(function() {
        $('#torrentlist_container').height($(window).height() - $('#torrentlist_container').attr('offsetTop'));
    });
    $.submitForm = function(button) {
        var form;
        if ($(button).is('form')) {
            // User pressed enter
            form = $(button);
            button = form.find('a')[0];
        } else {
            form = $(button).closest("form");
        }
        $.get(form.get(0).action, form.buildDataString(button), $.loadDynamicData, 'html');
    };

    $.fn.toggleDialog = function() {
        this.each(function() {
            var last = current_dialog === '#' ? '': current_dialog;
            var target = this.hash === '#' ? '#' + $(this).closest('.dialog_window').id: this.hash;
            current_dialog = last === target ? '': this.hash;
            if (last) {
                $(last).fadeOut("normal");
            }
            if (current_dialog && this.hash != '#') {
                $(current_dialog).fadeIn("normal");
            }
        });
        return this;
    };
    $.fn.initFavorites = function() {
        var selector = this.selector;
        setTimeout(function() {
            $(selector + ":first a").toggleFavorite();
        },
        300);
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
        if (f) {
            this.find("#config_webui").val(f).change();
        }
        return this;
    };
    $.fn.toggleFavorite = function() {
        this.each(function() {
            var last = current_favorite;
            current_favorite = this.hash;
            if (!last) {
                $(current_favorite).show();
            } else {
                $(last).fadeOut(400,
                function() {
                    $(current_favorite).fadeIn(400);
                });
            }
        });
        return this;
    };
    $.fn.initConfigDialog = function() {
        setTimeout(function() {
            $('select#client').change();
        },
        500);
        return this;
    };
    $.fn.buildDataString = function(buttonElement) {
        var dataString = $(this).filter('form').serialize();
        if (buttonElement) {
            dataString += (dataString.length === 0 ? '': '&') + 'button=' + buttonElement.id;
        }
        return dataString;
    };
    $.fn.markAlt = function() {
        return this.filter(":visible").removeClass('alt').filter(":visible:even").addClass('alt');
    };

    $.addFavorite = function(url) {
        $.get(url, '', $.loadDynamicData, 'html');
    };

    $.dlTorrent = function(url, id) {
        $.get(url,
        function(torHash) {
            $('li#' + id).removeClass('match_nomatch').addClass('match_downloading');
            $('li#' + id + ' td.buttons').removeClass('match_nomatch').addClass('match_downloading');
            if ($('li#' + id + ' div.torInfo').length === 0) {
                $('li#' + id + ' td.torrent_name')
                .append('<div id=tor_' + id + ' class="torInfo tor_' + torHash.match(/\w+/) + '"></div>');
            }

            $('li#' + id + ' p.dlTorrent').hide();
            $('li#' + id + ' p.trash').show();
            $('li#' + id + ' p.delete').show();
            $('li#' + id + ' p.torStop').show();

            $('li#' + id).removeClass('###torHash###').addClass(torHash);

            var p = $('li#' + id + ' p.delete');
            p.html(p.html().replace(/###torHash###/g, torHash.match(/\w+/)));
            p = $('li#' + id + ' p.trash');
            p.html(p.html().replace(/###torHash###/g, torHash.match(/\w+/)));
            p = $('li#' + id + ' p.torStart');
            p.html(p.html().replace(/###torHash###/g, torHash.match(/\w+/)));
            p = $('li#' + id + ' p.torStop');
            p.html(p.html().replace(/###torHash###/g, torHash.match(/\w+/)));
            getRecentClientData();
        });
    };

    $.delTorrent = function(torHash, trash) {
        $.getJSON('torrentwatch.php', {
            'delTorrent': torHash,
            'trash': trash
        },
        function(json) {
            if (json.result == "success") {
                getRecentClientData();
            } else {
                alert('Request failed');
            }
        });
    };

    $.stopStartTorrent = function(stopStart, torHash) {
        var param;
        if (stopStart == 'stop') {
            param = {
                'stopTorrent': torHash
            };
        } else if (stopStart == 'start') {
            param = {
                'startTorrent': torHash
            };
        }
        $.getJSON('torrentwatch.php', param,
        function(json) {
            if (json.result == "success") {
                $('li.' + torHash + ' p.dlTorrent').hide();
                torStartStopToggle(torHash);
                getRecentClientData();
            } else {
                alert('Request failed');
            }
        });
    };

    $.moveTorrent = function(torId, path, item) {
        var move;
        if (item.totalSize - item.leftUntilDone === 0) {
            move = 'false';
        } else {
            move = 'true';
        }
        $.getJSON('torrentwatch.php', {
            'moveTo': path,
            'torId': torId,
            'move': move
        },
        function(json) {
            if (json.result == "success") {
                $('div#move_' + torId).hide();
                getRecentClientData();
            } else {
                alert('Request failed');
            }
        });
    };
})(jQuery);
