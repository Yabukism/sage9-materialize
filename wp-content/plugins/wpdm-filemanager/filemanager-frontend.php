<?php
if(!defined('ABSPATH')) die('Error!');
global $current_user;
$root = UPLOAD_DIR.$current_user->user_login.'/';
$items = scandir($root);
?><div class="w3eden">
    <?php do_action("wpdm_frontend_filemanager_top", ""); ?>
   
    <div class="row">
        <div class="col-md-12">
            <div class="well media well-sm well-file" style="background: rgba(255,255,255,0.4);border: 1px solid rgba(0,0,0,0.1) !important;margin: 0">
                <div class="pull-right">
                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#newfol"><i class="fa fa-folder-open"></i> &nbsp; New Folder</button>
                    <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#upfile"><i class="fa fa-upload"></i> &nbsp; Upload File</button>
                    <button class="btn btn-success btn-sm" id="btn-paste" disabled="disabled" title="Paste"><i class="fa fa-clipboard"></i></button>
                </div>
                <div class="media-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="progress" style="margin: 0;height: 31px;line-height: 31px;">
                            <div title="15% Used" class="progress-bar progress-bar-success progress-bar-striped" role="progressbar"
                                 aria-valuenow="<?php echo number_format((wpdm_get_dir_size($root)/wpdm_user_space_limit())*100, 2) ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo number_format((wpdm_get_dir_size($root)/wpdm_user_space_limit())*100, 2) ?>%;line-height: 31px;font-size: 13px;overflow: visible">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                         ( <?php echo wpdm_user_space_limit(); ?> MB / <?php echo wpdm_get_dir_size($root); ?> MB  )
                    </div>
                </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div id="ldn" style="float:right;font-size: 9pt;margin-top: 10px;display: none" class="text-danger"><i class="fa fa-spinner fa-spin"></i> Loading...</div>
            <div id="breadcrumb"></div>
        </div>

    </div>
    <?php do_action("wpdm_frontend_filemanager_after_breadcrumb", ""); ?>
    <div class="row" id="scandir">
    </div>
    <?php do_action("wpdm_frontend_filemanager_bottom", ""); ?>
    <div id="dirTPL" style="display: none">
        <div class="col-md-3">
            <div class="panel panel-default panel-file panel-folder">
                <div class="panel-body">
                    <div class="media media-folder" data-path="{{path}}" style="cursor: pointer">
                        <div class="pull-left"><img style="width: 48px" src="<?php echo plugins_url('wpdm-filemanager/assets/img/dir.png'); ?>" /> </div>
                        <div class="media-body"><strong class="item_label" title="{{item}}">{{item_label}}</strong><small>{{note}}</small></div>
                    </div>
                </div>
                <div class="panel-footer">
                    <button class="btn btn-xs btn-default btn-rename" type="button" title="Rename" data-oldname="{{item}}" data-path="{{path}}" data-toggle="modal" data-target="#rename"><i class="fa fa-pencil"></i></button>
                    <button class="btn btn-xs btn-primary" type="button"><i class="fa fa-folder-open"></i></button>
                    <button class="btn btn-xs btn-info" type="button"><i class="fa fa-link"></i></button>
                    <button class="btn btn-xs btn-warning btn-cut" data-item="{{item}}" type="button"><i class="fa fa-cut"></i></button>
                    <button class="btn btn-xs btn-inverse btn-copy" data-item="{{item}}" type="button"><i class="fa fa-copy"></i></button>
                    <button class="btn btn-xs btn-danger btn-delete" type="button" data-path="{{path}}"><i class="fa fa-trash-o"></i></button>
                </div>
            </div>
        </div>
    </div>
    <div id="fileTPL" style="display: none">
        <div class="col-md-3">
            <div class="panel panel-default panel-file file-tpl">
                <div class="panel-body" data-path="{{path}}">
                    <div class="media">
                        <div class="pull-left"><img style="width: 48px" src="{{icon}}" /> </div>
                        <div class="media-body"><strong class="item_label" title="{{item}}">{{item_label}}</strong><small>{{note}}</small></div>
                    </div>
                </div>
                <div class="panel-footer">
                    <button class="btn btn-xs btn-default btn-rename" type="button" title="Rename" data-oldname="{{item}}" data-path="{{path}}" data-toggle="modal" data-target="#rename"><i class="fa fa-pencil"></i></button>
                    <button class="btn btn-xs btn-primary" type="button"><i class="fa fa-download"></i></button>
                    <button class="btn btn-xs btn-info" type="button"><i class="fa fa-link"></i></button>
                    <button class="btn btn-xs btn-warning btn-cut" data-item="{{item}}" type="button"><i class="fa fa-cut"></i></button>
                    <button class="btn btn-xs btn-inverse btn-copy" data-item="{{item}}" type="button"><i class="fa fa-copy"></i></button>
                    <button class="btn btn-xs btn-danger btn-delete" type="button" data-path="{{path}}"><i class="fa fa-trash-o"></i></button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" id="upfile">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close pull-right" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
                    <strong class="modal-title" id="myModalLabel">Upload</strong>
                </div>
                <div id="upload" class="modal-body">
                    <div id="plupload-upload-ui" class="hide-if-no-js">
                        <div id="drag-drop-area">
                            <div class="drag-drop-inside">
                                <p class="drag-drop-info"><?php _e('Drop files here'); ?></p>
                                <p><?php _ex('or', 'Uploader: Drop files here - or - Select Files'); ?></p>
                                <p class="drag-drop-buttons"><input id="plupload-browse-button" type="button" value="<?php esc_attr_e('Select Files'); ?>" class="btn btn-success" /></p>
                            </div>
                        </div>
                    </div>

                    <?php
                    $slimit = get_option('__wpdm_max_upload_size',0);
                    if($slimit>0)
                        $slimit = wp_convert_hr_to_bytes($slimit.'M');
                    else
                        $slimit = wp_max_upload_size();

                    $plupload_init = array(
                        'runtimes'            => 'html5,silverlight,flash,html4',
                        'browse_button'       => 'plupload-browse-button',
                        'container'           => 'plupload-upload-ui',
                        'drop_element'        => 'drag-drop-area',
                        'file_data_name'      => 'attach_file',
                        'multiple_queues'     => true,
                        'max_file_size'       => $slimit.'b',
                        'url'                 => admin_url('admin-ajax.php'),
                        'flash_swf_url'       => includes_url('js/plupload/plupload.flash.swf'),
                        'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
                        'filters'             => array(array('title' => __('Allowed Files'), 'extensions' =>  get_option('__wpdm_allowed_file_types','*'))),
                        'multipart'           => true,
                        'urlstream_upload'    => true,

                        // additional post data to send to our ajax hook
                        'multipart_params'    => array(
                            '_ajax_nonce' => wp_create_nonce(NONCE_KEY),
                            'action'      => 'wpdm_frontend_file_upload',            // the ajax action name
                        ),
                    );

                    // we should probably not apply this filter, plugins may expect wp's media uploader...
                    $plupload_init = apply_filters('plupload_init', $plupload_init); ?>


                    <div id="filelist"></div>
                    <div  style="clear: both"></div>
                </div>


            </div>
        </div>
    </div>

    <div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" id="newfol">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close pull-right" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
                    <strong class="modal-title" id="myModalLabel">New Folder</strong>
                </div>
                <div id="upload" class="modal-body">
                    <input type="text" placeholder="Folder Name" id="folname" class="form-control input-lg" style="margin: 0">
                </div>
                <div class="modal-footer text-right">
                    <button type="button" id="createfol" class="btn btn-info">Create Folder</button>
                    <div style="float:left;display: none;" id="fcd" class="text-success"><i class="fa fa-check-circle"></i> Folder Created</div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" id="rename">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close pull-right" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <strong class="modal-title" id="myModalLabel">Rename</strong>
                </div>
                <div id="upload" class="modal-body">
                    <input type="text" placeholder="New Name" id="newname" class="form-control input-lg" style="margin: 0">
                </div>
                <div class="modal-footer text-right">
                    <button type="button" id="renamenow" class="btn btn-info">Rename</button>
                    <div style="float:left;display: none;" id="rnmn" class="text-success"><i class="fa fa-check-circle"></i> Renamed</div>
                </div>
            </div>
        </div>
    </div>

</div>
<script>
    var current_path = '';
    jQuery(function ($) {

        function wpdm_breadcrumb() {
            var template = '<a href="#" class="media-folder" data-path="_path_">_label_ </a>';
            var parts = current_path.split('/');
            var bchtml = [], path = [];
            bchtml[0] = template.replace(/_path_/ig, '').replace(/_label_/ig, 'Home');
            for(var i = 1; i <= parts.length; i++){
                path[i-1] = parts[i-1];
                bchtml[i] = template.replace(/_path_/ig, path.join('/')).replace(/_label_/ig, parts[i-1]);
            }
            $('#breadcrumb').html("<i class='fa fa-map-marker text-info'></i> &nbsp; "+bchtml.join(' &nbsp;<i class="fa fa-angle-right"></i>&nbsp; '));
        }
        function refresh_scandir(path) {
            $('#ldn').fadeIn();
            $.get(ajax_url, {__wpdm_scandir:'<?php echo wp_create_nonce(NONCE_KEY); ?>', action: 'wpdm_scandir', path: path}, function (data) {
                $('#scandir').html('');
                $.each(data, function (index, entry) {
                    if(entry.type == 'file') {
                        var tpl = $('#fileTPL').html();
                        tpl = tpl.replace("{{icon}}", entry.icon);
                        tpl = tpl.replace("{{item_label}}", entry.item_label);
                        tpl = tpl.replace("{{note}}", entry.note);
                        tpl = tpl.replace("{{file_size}}", entry.file_size);
                        tpl = tpl.replace(/\{\{path\}\}/ig, entry.path);
                        tpl = tpl.replace(/\{\{item\}\}/ig, entry.item);
                    } else {
                        var tpl = $('#dirTPL').html();
                        tpl = tpl.replace("{{icon}}", entry.icon);
                        tpl = tpl.replace("{{item_label}}", entry.item_label);
                        tpl = tpl.replace("{{note}}", entry.note);
                        tpl = tpl.replace("{{file_size}}", entry.file_size);
                        tpl = tpl.replace(/\{\{path\}\}/ig, entry.path);
                        tpl = tpl.replace(/\{\{item\}\}/ig, entry.item);
                    }
                    $('#scandir').append(tpl);
                });
                $('#ldn').fadeOut();
            });
            wpdm_breadcrumb();
        }

        // create the uploader and pass the config from above
        var uploader = new plupload.Uploader(<?php echo json_encode($plupload_init); ?>);

        // checks if browser supports drag and drop upload, makes some css adjustments if necessary
        uploader.bind('Init', function(up){
            var uploaddiv = jQuery('#plupload-upload-ui');

            if(up.features.dragdrop){
                uploaddiv.addClass('drag-drop');
                jQuery('#drag-drop-area')
                    .bind('dragover.wp-uploader', function(){ uploaddiv.addClass('drag-over'); })
                    .bind('dragleave.wp-uploader, drop.wp-uploader', function(){ uploaddiv.removeClass('drag-over'); });

            }else{
                uploaddiv.removeClass('drag-drop');
                jQuery('#drag-drop-area').unbind('.wp-uploader');
            }
        });

        uploader.init();

        // a file was added in the queue
        uploader.bind('FilesAdded', function(up, files){
            //var hundredmb = 100 * 1024 * 1024, max = parseInt(up.settings.max_file_size, 10);

            uploader.settings.multipart_params.current_path = current_path;

            plupload.each(files, function(file){
                jQuery('#filelist').append(
                    '<div class="file" id="' + file.id + '"><b>' +

                    file.name + '</b> (<span>' + plupload.formatSize(0) + '</span>/' + plupload.formatSize(file.size) + ') ' +
                    '<div class="progress progress-success progress-striped active"><div class="bar fileprogress"></div></div></div>');
            });

            up.refresh();
            up.start();
        });

        uploader.bind('UploadProgress', function(up, file) {

            jQuery('#' + file.id + " .fileprogress").width(file.percent + "%");
            jQuery('#' + file.id + " span").html(plupload.formatSize(parseInt(file.size * file.percent / 100)));
        });


        // a file was uploaded
        uploader.bind('FileUploaded', function(up, file, response) {

            jQuery('#' + file.id ).remove();
            var d = new Date();
            var ID = d.getTime();
            response = response.response;

            refresh_scandir(current_path);

        });

        $('#createfol').on('click', function () {
            var folname = $('#folname').val();
            if(folname !=''){
                $('#createfol').html('<i class="fa fa-refresh fa-spin"></i> &nbsp; Creating...');
                $.get(ajax_url, {__wpdm_mkdir:'<?php echo wp_create_nonce(NONCE_KEY); ?>', action: 'wpdm_mkdir', path: current_path, name: folname}, function (data) {
                    $('#folname').val('');
                    $('#createfol').html('Create Folder');
                    $('#fcd').fadeIn();
                    refresh_scandir(current_path);
                });
            }
        });

        /* Delete */
        $('body').on('click', '.btn-delete', function (e) {
            e.preventDefault();
            if(!confirm('Are you sure?')) return false;
            $(this).html('<i class="fa fa-spinner fa-spin"></i>');
            var filepath = $(this).data('path');
            $.get(ajax_url, {__wpdm_unlink:'<?php echo wp_create_nonce(NONCE_KEY); ?>', action: 'wpdm_unlink', path: current_path, delete: filepath}, function (data) {
                refresh_scandir(current_path);
            });
        });


        $('body').on('click', '.media-folder', function (e) {
            e.preventDefault();
            current_path = $(this).data('path');
            refresh_scandir(current_path);
        });

        $('body').on('click', '.btn-copy', function (e) {
            e.preventDefault();
            localStorage.setItem("__wpdm_fm_copy", current_path+"/"+$(this).data('item'));
            localStorage.setItem("__wpdm_fm_move", 0);
            $('.btn-copy').html('<i class="fa fa-copy"></i>');
            $(this).html('<i class="fa fa-check-circle"></i>');
            $('#btn-paste').removeAttr('disabled').attr("data-item", localStorage.getItem("__wpdm_fm_copy"));
        });

        $('body').on('click', '.btn-cut', function (e) {
            e.preventDefault();
            localStorage.setItem("__wpdm_fm_copy", current_path+"/"+$(this).data('item'));
            localStorage.setItem("__wpdm_fm_move", 1);
            $('.btn-copy').html('<i class="fa fa-copy"></i>');
            $(this).html('<i class="fa fa-check-circle"></i>');
            $('#btn-paste').removeAttr('disabled').attr("data-item", localStorage.getItem("__wpdm_fm_copy"));
        });

        $('body').on('click', '.btn-rename', function (e) {
            $('#newname').val($(this).data('oldname'));
            $('#newname').data('oldname', $(this).data('oldname'));
        });

        /* Rename */
        $('body').on('click', '#renamenow', function (e) {
            e.preventDefault();
            $(this).html('<i class="fa fa-spinner fa-spin"></i>');
            var filepath = $(this).data('path');
            $.get(ajax_url, {__wpdm_rename:'<?php echo wp_create_nonce(NONCE_KEY); ?>', action: 'wpdm_rename', path: current_path, item: $('#newname').data('oldname'), newname: $('#newname').val()}, function (data) {
                refresh_scandir(current_path);
                $('#renamenow').html('Rename');
            });
        });

        $('body').on('click', '#btn-paste', function (e) {
            e.preventDefault();
            $(this).html('<i class="fa fa-spinner fa-spin"></i>');
            var params = {__wpdm_copypaste:'<?php echo wp_create_nonce(NONCE_KEY); ?>', action: 'wpdm_copypaste', source: localStorage.getItem("__wpdm_fm_copy"), dest: current_path};
            if(localStorage.getItem("__wpdm_fm_move") == 1)
                params = {__wpdm_cutpaste:'<?php echo wp_create_nonce(NONCE_KEY); ?>', action: 'wpdm_cutpaste', source: localStorage.getItem("__wpdm_fm_copy"), dest: current_path};
            $.get(ajax_url, params, function (data) {
                refresh_scandir(current_path);
                $('#btn-paste').html('<i class="fa fa-clipboard"></i>');
                if(localStorage.getItem("__wpdm_fm_move") == 1){
                    localStorage.setItem("__wpdm_fm_move", 0);
                    localStorage.setItem("__wpdm_fm_copy", '');
                    $('#btn-paste').attr('disabled','disabled');
                }
            });
        });


        if(localStorage.getItem("__wpdm_fm_copy") != undefined && localStorage.getItem("__wpdm_fm_copy") != ''){
            $('#btn-paste').removeAttr('disabled').attr("data-item", localStorage.getItem("__wpdm_fm_copy"));
        }

        refresh_scandir('');
    });
</script>
<style>
    #rename, #upfile, #newfol{
        z-index: 999999999999;
        padding-top: 150px;
        overflow: hidden;
    }
    .well-file{
        font-family: Montserrat,sans-serif;
        font-size: 12px;
        line-height: 28px;
    }
    .progress:after{
        position: absolute;
        content: "<?php echo number_format((wpdm_get_dir_size($root)/wpdm_user_space_limit())*100, 2) ?>% used";
        color: rgba(0,0,0,0.3);
        width: 100%;
        text-align: center;
        left: 0;
        font-family: Montserrat,sans-serif;
        font-size: 12px;
        text-transform: uppercase;
    }
    .panel-file .media-body {
        line-height: normal;
    }
    .media-body .item_label{
        font-size: 11pt;
        line-height: 15px;
        display: block;
    }
    .media-body .file-title{
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin: 0;
        display: block;
        max-width: 70%;
    }
    .w3eden .well .btn-sm{
        padding: 8px 16px;
    }
    .panel-file .panel-footer{
        text-align: center;
    }
    .modal *{
        font-size: 10pt;
    }
    .modal-header{
        font-size: 10pt;line-height: normal;
        font-family: Montserrat,sans-serif;
    }
    #drag-drop-area{ border: 0 !important; }
    #breadcrumb{
        margin: 10px 0 10px;
        font-size: 9pt;
        color: #888888;
    }
    #breadcrumb a{
        color: #aaaaaa;
    }
    #breadcrumb .fa{
        color: #557faa;
    }
    .panel-file .panel-body{
        height: 85px;e
    }
</style>
