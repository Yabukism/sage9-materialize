var allps;

jQuery(function($){

    // Uploading files
    var file_frame, dfield;

    $('body').on('click', '.__wpdm_approvedr' , function( event ) {
        event.preventDefault();
        $btn = $(this);
        $btn.attr('disabled', 'disabled').html('<i class="fa fa-refresh fa-spin"></i>');
        $('.__wpdm_declinedr_'+$btn.data('rid')).remove();
        $.post(ajaxurl,{__approvedr: $(this).data('nonce'), __rid: $(this).data('rid'), action: 'approveDownloadRequest'}, function (res) {
            if(res.match(/ok/)){
                $btn.removeClass('btn-info').addClass('btn-success').html('Approved');
            }
        });
    });

    $('body').on('click', '.__wpdm_declinedr' , function( event ) {
        event.preventDefault();
        if(!confirm('Are you sure?')) return false;
        $btn = $(this);
        $btn.attr('disabled', 'disabled').html('<i class="fa fa-refresh fa-spin"></i>');
        $.post(ajaxurl,{__declinedr: $(this).data('nonce'), __rid: $(this).data('rid'), action: 'declineDownloadRequest'}, function (res) {
            if(res.match(/ok/)){
                $('#__emlrow_'+$btn.data('rid')).remove();
            }
        });
    });


    $('body').on('click', '.btn-onclick', function () {
        $(this).css('width', $(this).css('width')).attr('disabled', 'disabled');
        $(this).html($(this).data('onclick'));
    });

    $('body').on('click', '.btn-media-upload' , function( event ){
        event.preventDefault();
        dfield = $($(this).attr('rel'));

        // If the media frame already exists, reopen it.
        if ( file_frame ) {
            file_frame.open();
            return;
        }

        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
            title: $( this ).data( 'uploader_title' ),
            button: {
                text: $( this ).data( 'uploader_button_text' )
            },
            multiple: false  // Set to true to allow multiple files to be selected
        });

        // When an image is selected, run a callback.
        file_frame.on( 'select', function() {
            // We set multiple to false so only get one image from the uploader
            attachment = file_frame.state().get('selection').first().toJSON();
            dfield.val(attachment.url);

        });

        // Finally, open the modal
        file_frame.open();
    });

    allps = $('#pps_z').val();
    if(allps == undefined) allps = '';
    $('#ps').val(allps.replace(/\]\[/g,"\n").replace(/[\]|\[]+/g,''));
    shuffle = function(){
        var sl = 'abcdefghijklmnopqrstuvwxyz';
        var cl = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        var nm = '0123456789';
        var sc = '~!@#$%^&*()_';
        ps = "";
        pss = "";
        if($('#ls').attr('checked')=='checked') ps = sl;
        if($('#lc').attr('checked')=='checked') ps += cl;
        if($('#nm').attr('checked')=='checked') ps += nm;
        if($('#sc').attr('checked')=='checked') ps +=sc;
        var i=0;
        while ( i <= ps.length ) {
            $max = ps.length-1;
            $num = Math.floor(Math.random()*$max);
            $temp = ps.substr($num, 1);
            pss += $temp;
            i++;
        }

        $('#ps').val(pss);


    };
    $('#gps').click(shuffle);

    $('body').on('click', '#gpsc', function(){
        var allps = "";
        shuffle();
        for(k=0;k<$('#pcnt').val();k++){
            allps += "["+randomPassword(pss,$('#ncp').val())+"]";

        }
        vallps = allps.replace(/\]\[/g,"\n").replace(/[\]|\[]+/g,'');
        $('#ps').val(vallps);

    });

    $('body').on('click', '#pins', function(){
        var aps;
        aps = $('#ps').val();
        aps = aps.replace(/\n/g, "][");
        allps = "["+aps+"]";
        $($(this).data('target')).val(allps);
        tb_remove();
    });

});

function randomPassword(chars, size) {

    //var size = 10;
    if(parseInt(size)==Number.NaN || size == "") size = 8;
    var i = 1;
    var ret = "";
    while ( i <= size ) {
        $max = chars.length-1;
        $num = Math.floor(Math.random()*$max);
        $temp = chars.substr($num, 1);
        ret += $temp;
        i++;
    }
    return ret;
}

function __showDownloadLink(pid, fid) {
    var url;
    url = wpdmConfig.siteURL +"?wpdmdl="+pid+"&ind="+fid;
    __bootModal("File Download Link", '<textarea readonly="readonly" class="form-control" style="font-family: monospace">'+url+'</textarea>');
}

function __bootModal(heading, content) {
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
        '      <div class="modal-body text-center" style="letter-spacing: 0.5px;font-size: 9pt;font-weight: 300;padding: 25px;">\n' +
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

