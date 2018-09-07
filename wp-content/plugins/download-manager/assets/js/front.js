var allps;
jQuery(function ($) {

    $('body').on('click', '.wpdm-download-link.wpdm-download-locked', function (e) {
        e.preventDefault();
        hideLockFrame();

        var parentWindow = document.createElement("a");
        parentWindow.href = document.referrer.toString();

        if(parentWindow.hostname === window.location.hostname)
            $(window.parent.document.body).append("<iframe id='wpdm-lock-frame' style='left:0;top:0;width: 100%;height: 100%;z-index: 999999999;position: fixed;background: rgba(255,255,255,0.3) url("+wpdm_home_url+"wp-content/plugins/download-manager/assets/images/loader.svg) center center no-repeat;background-size: 100px 100px;border: 0;' src='"+wpdm_home_url+"?__wpdmlo="+$(this).data('package')+"'></iframe>");
        else
            window.parent.postMessage({
                'task': 'showiframe',
                'iframe': "<iframe id='wpdm-lock-frame' style='left:0;top:0;width: 100%;height: 100%;z-index: 999999999;position: fixed;background: rgba(255,255,255,0.3) url("+wpdm_home_url+"wp-content/plugins/download-manager/assets/images/loader.svg) center center no-repeat;background-size: 100px 100px;border: 0;' src='"+wpdm_home_url+"?__wpdmlo="+$(this).data('package')+"'></iframe>"
            }, "*");

    });


    $('body').on('click', '.__wpdm_playvideo', function (e) {
        e.preventDefault();
        $('#__wpdm_videoplayer').children('source').attr('src', $(this).data('video'));
        console.log('loading...');
        var vid = document.getElementById("__wpdm_videoplayer");
        vid.onloadeddata = function() {
            console.log('loaded....');
        };
        $("#__wpdm_videoplayer").get(0).load();

        //$('#__wpdm_videoplayer').get(0).play();
    });

    $('body').on('click', function (e) {

        if ($(e.target).data('toggle') !== 'popover'
            && $(e.target).parents('.popover.in').length === 0) {
            try {

                $('.wpdm-download-locked.pop-over').each(function(){
                    if($(this).data('ready') == 'show'){
                        $(this).popover('hide');
                        $(this).data('ready', 'hide');
                    }
                });
            }catch(e){}
        }
    });

    $('body').on('change', '.terms_checkbox', function (e) {
        if($(this).is(':checked'))
            $('#wpdm-filelist-'+$(this).data('pid')+' .btn.inddl, #xfilelist .btn.inddl').removeAttr('disabled');
        else
            $('#wpdm-filelist-'+$(this).data('pid')+' .btn.inddl, #xfilelist .btn.inddl').attr('disabled', 'disabled');
    });

    $('body').on('click', '.wpdm-social-lock', function (e) {

            try {


                _PopupCenter($(this).data('url'), 'Social Lock', 600, 400);

            }catch(e){}

    });

    $('body').on('click', '.po-close', function (e) {

            try {

                $('.wpdm-download-locked.pop-over').each(function(){
                    if($(this).data('ready') == 'show'){
                        $(this).popover('hide');
                        $(this).data('ready', 'hide');
                    }
                });
            }catch(e){}

    });

    $('body').on('click', '#wpdm-dashboard-sidebar a.list-group-item', function (e) {

           location.href = this.href;

    });

    $('.input-group input').on('focus', function () {
        $(this).parent().find('.input-group-addon').addClass('input-group-addon-active');
    });
    $('.input-group input').on('blur', function () {
        $(this).parent().find('.input-group-addon').removeClass('input-group-addon-active');
    });

    //$('.inddl').unbind('click');
    $('body').on('click', 'button.btn.inddl', function (e) {
        e.preventDefault();
        var tis = this;
        if($(this).data('dlurl') != undefined){
            location.href = $(this).data('dlurl');
            return;
        }
        $.post( wpdm_ajax_url, {
            action: 'wpdm_verify_file_pass',
            wpdmfileid: $(tis).data('pid'),
            wpdmfile: $(tis).data('file'),
            actioninddlpvr: 1,
            filepass: $($(tis).data('pass')).val()
        }, function (res) {
            var _res = res;
            res = res.split('|');
            if (_res.match(/error/)) $($(tis).data('pass')).addClass('input-error');
            if (_res.match(/ok/)) {
                var dlurl = wpdm_home_url + '?wpdmdl=' + $(tis).data('pid') + '&ind=' + $(tis).data('file') + '&_wpdmkey=' + res[2];
                $(tis).data('dlurl', dlurl);
                wpdm_boot_popup("Password Verified!", "<div style='padding: 50px;'>Please click following button to start download.<br/><br/><a href='"+dlurl+"' class='btn btn-lg btn-success' target='_blank'>Start Download</a></div>",
                    [{
                            label: 'Close',
                            class: 'btn btn-default',
                            callback: function () {
                                this.modal('hide');
                                return false;
                            }
                        }]
                    );
            }
        });
    });

    $('body').on('click', '.wpdm-download-locked.pop-over', function () {
        var $dc = $($(this).attr('href'));
        var prts = $(this).attr('href').split('_');
        var pid = prts[1];

        if ($(this).attr('data-ready') == undefined || $(this).attr('data-ready') == 'hide') {

            $(this).popover({
                placement: 'bottom',
                html: true,
                content: function () {

                    if(wpdm_ajax_popup == 1)
                    return "<div id='popcnt_"+pid+"'><i class='fa fa-refresh fa-spin'></i> Loading...</div>"+'<div style="padding-top: 10px;border-top: 1px solid #eeeeee;text-align: right"><button class="btn btn-danger btn-xs po-close" s><i class="fa fa-times-circle"></i> Close</button></div>';

                    else
                        return $dc.html()+'<div style="padding-top: 10px;border-top: 1px solid #eeeeee;text-align: right"><button class="btn btn-danger btn-xs po-close" s><i class="fa fa-times-circle"></i> Close</button></div>';


                }
            });
            $(this).popover('show');

            if(wpdm_ajax_popup == 1)
                $("#popcnt_"+pid).load(ajax_url,{action:'showLockOptions',id:pid});

            $(this).data('ready', 'show');
        } else {
            $(this).popover('hide');
            $(this).date('ready', 'hide');
        }

        return false;
    });



    $('body').on('click', '.wpdm-indir', function (e) {
        e.preventDefault();
        $('#xfilelist').load(location.href, {
            action: 'wpdmfilelistcd',
            pid: $(this).data('pid'),
            cd: $(this).data('dir')
        });
    });


    $('body').on('click', '.role-tabs a', function (e) {
        $('.role-tabs a').removeClass('active');
        $(this).addClass('active');
    });



    $('body').on('click', '.btn-wpdm-a2f', function (e) {
            var a2fbtn = $(this);
            $.post(ajax_url, {action: 'wpdm_addtofav', pid: $(this).data('package')}, function (res) {
                if(a2fbtn.hasClass('btn-inverse'))
                    a2fbtn.removeClass('btn-inverse').addClass('btn-danger').html(a2fbtn.data('rlabel'));
                else
                    a2fbtn.removeClass('btn-danger').addClass('btn-inverse').html(a2fbtn.data('alabel'));
            });
    });

    $('body').on('click', '.wpdm-btn-play', function (e) {
        e.preventDefault();
        var player = $('#' + $(this).data('player'));
        var btn = $(this);

        if (btn.data('state') == 'playing') {
            $(this).data('state', 'paused');
            player.trigger('pause');
            $(this).html("<i class='fa fa-play'></i>");
            return false;
        }

        if (btn.data('state') == 'paused') {
            $(this).data('state', 'playing');
            player.trigger('play');
            $('.wpdm-btn-play').html("<i class='fa fa-play'></i>");
            $(this).html("<i class='fa fa-pause'></i>");
            return false;
        }


        player.attr('src', $(this).data('song') + "&play=song.mp3");
        player.slideDown();
        $('.wpdm-btn-play').data("state", "stopped");
        $('.wpdm-btn-play').html("<i class='fa fa-play'></i>");
        btn.html("<i class='fas fa-sun  fa-spin'></i>");
        player.unbind('loadedmetadata');
        player.on('loadedmetadata', function () {
            console.log("Playing " + this.src + ", for: " + this.duration + "seconds.");
            btn.html("<i class='fa fa-pause'></i>");
            btn.data('state', 'playing');

        });
    });

    $('.wpdm_remove_empty').remove();

    /* Uploading files */
    var file_frame, dfield;

    $('body').on('click', '.wpdm-media-upload', function (event) {
        event.preventDefault();
        dfield = $($(this).attr('rel'));

        /* If the media frame already exists, reopen it. */
        if (file_frame) {
            file_frame.open();
            return;
        }

        /* Create the media frame. */
        file_frame = wp.media.frames.file_frame = wp.media({
            title: $(this).data('uploader_title'),
            button: {
                text: $(this).data('uploader_button_text')
            },
            multiple: false  /* Set to true to allow multiple files to be selected */
        });

        /* When an image is selected, run a callback. */
        file_frame.on('select', function () {
            /* We set multiple to false so only get one image from the uploader */
            attachment = file_frame.state().get('selection').first().toJSON();
            dfield.val(attachment.url);

        });

        /* Finally, open the modal */
        file_frame.open();
    });

    $('body').on('click', '.pagination.async a', function (e) {
        e.preventDefault();
        var _cont = $(this).data('container');
        console.log(_cont);
        $(_cont).append("<div style='z-index: 9999999999;position: absolute;width: 100%;height: 100%;display: table;background: rgba(0,0,0,0.6);left:0;top:0;border-radius: 5px;'><div style='display: table-cell;width: 100%;height: 100%;vertical-align: middle;color: #ffffff;text-align: center;font-weight: 800;font-size: 13pt;letter-spacing: 2px;'><i class='fa fa-refresh fa-spin'></i> &nbsp;Loading...</div> </div>");
        $.get(this.href, function (res) {
            $(_cont).html($(res).find(_cont).html());
        });
    });



    /* Bootstrap 4 *
    $('.panel').addClass('card').removeClass('panel');
    $('.panel-body').addClass('card-body').removeClass('panel-body');
    $('.panel-heading').addClass('card-header').removeClass('panel-heading');
    $('.panel-footer').addClass('card-footer').removeClass('panel-footer');
    $('.panel-danger').addClass('bg-danger text-white').removeClass('panel-danger');
    $('.dashboard-panel').removeClass('.dashboard-panel');
    //*/



});

function _PopupCenter(url, title, w, h) {
    /* Fixes dual-screen position                         Most browsers      Firefox */
    var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
    var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

    var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
    var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

    var left = ((width / 2) - (w / 2)) + dualScreenLeft;
    var top = ((height / 2) - (h / 2)) + dualScreenTop;
    var newWindow = window.open(url, title, 'scrollbars=yes, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);

    /* Puts focus on the newWindow */
    if (window.focus) {
        newWindow.focus();
    }

    return false;
}

function generatepass(id){
    tb_show('Generate Password',ajaxurl+'?action=wpdm_generate_password&id='+id);
}

function hideLockFrame() {
    jQuery('#wpdm-lock-frame').remove();
}

function wpdm_bootModal(heading, content) {
    var html;
    jQuery("#w3eden__bootModal").remove();
    html = '<div class="w3eden" id="w3eden__bootModal"><div id="__bootModal" class="modal fade" tabindex="-1" role="dialog">\n' +
        '  <div class="modal-dialog" role="document">\n' +
        '    <div class="modal-content">\n' +
        '      <div class="modal-header">\n' +
        '        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>\n' +
        '        <h4 class="modal-title">'+heading+'</h4>\n' +
        '      </div>\n' +
        '      <div class="modal-body">\n' +
        '        <p>'+content+'</p>\n' +
        '      </div>\n' +
        '      <div class="modal-footer">\n' +
        '        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>\n' +
        '      </div>\n' +
        '    </div>\n' +
        '  </div>\n' +
        '</div></div>';
    jQuery('body').append(html);
    jQuery("#__bootModal").modal('show');
}

function wpdm_boot_popup(heading, content, buttons) {
    var html, $ = jQuery;
    $("#w3eden__boot_popup").remove();
    var _buttons = '<div class="modal-footer" style="padding: 8px 15px;">\n';
    $.each(buttons, function (i, button) {
        var id = 'btx_'+i;
        _buttons += "<button id='"+id+"' class='"+button.class+" btn-xs' style='font-size: 10px;padding: 3px 20px;'>"+button.label+"</button> ";
    });
    _buttons += '</div>\n';

    html = '<div class="w3eden" id="w3eden__boot_popup"><div id="__boot_popup" style="z-index: 9999999 !important;" class="modal fade" tabindex="-1" role="dialog">\n' +
        '  <div class="modal-dialog" role="document" style="max-width: 100%;width: 350px">\n' +
        '    <div class="modal-content" style="border-radius: 3px;overflow: hidden">\n' +
        '      <div class="modal-header" style="padding: 12px 15px;background: #f5f5f5;">\n' +
        '        <h4 class="modal-title" style="font-size: 9pt;font-weight: 500;padding: 0;margin: 0;font-family:Montserrat, san-serif;letter-spacing: 0.5px">'+heading+'</h4>\n' +
        '      </div>\n' +
        '      <div class="modal-body text-center" style="letter-spacing: 0.5px;font-size: 10pt;font-weight: 300;padding: 25px;line-height: 1.5">\n' +
        '        '+content+'\n' +
        '      </div>\n' + _buttons +
        '    </div>\n' +
        '  </div>\n' +
        '</div></div>';
    $('body').append(html);
    $("#__boot_popup").modal('show');
    $.each(buttons, function (i, button) {
        var id = 'btx_'+i;
        $('#'+id).unbind('click');
        $( '#'+id).bind('click' , function () {
            button.callback.call($("#__boot_popup"));
            return false;
        });
    });
    return $("#__boot_popup");
}