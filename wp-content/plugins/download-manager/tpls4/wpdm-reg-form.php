<?php if(!defined('ABSPATH')) die('!'); ?>

<div class="w3eden">
    <div class='w3eden' id='wpdmreg'>
<?php

$loginurl = wpdm_login_url();
$reg_redirect =  $loginurl;
if(isset($params['autologin']) && $params['autologin'] == 'true') $reg_redirect = wpdm_user_dashboard_url();
if(isset($params['redirect'])) $reg_redirect = esc_url($params['redirect']);
if(isset($_GET['redirect_to'])) $reg_redirect = esc_url($_GET['redirect_to']);
$force = uniqid();

$up = parse_url($reg_redirect);
if(isset($up['host']) && $up['host'] != $_SERVER['SERVER_NAME']) $reg_redirect = home_url('/');

$reg_redirect = esc_attr(esc_url($reg_redirect));

if(!isset($params['logo'])) $params['logo'] = get_site_icon_url();

if(get_option('users_can_register')){
?>
    <?php if(isset($params['logo']) && $params['logo'] != '' && !isset($nologo)){ ?>
        <div class="text-center wpdmlogin-logo">
            <img src="<?php echo $params['logo'];?>" />
        </div>
    <?php } ?>
<form method="post" action="" id="registerform" name="registerform" class="login-form">

    <input type="hidden" name="phash" value="<?php echo isset($regparams)?$regparams:''; ?>" />
    <input type="hidden" name="permalink" value="<?php echo $loginurl; ?>" />
    <!-- div class="panel panel-primary">
<div class="panel-heading"><b>Register</b></div>
<div class="panel-body" -->
<?php global $wp_query; if(isset($_SESSION['reg_error'])&&$_SESSION['reg_error']!='') {  ?>
<div class="error alert alert-danger" data-title="<?php _e( "REGISTRATION FAILED!" , "download-manager" ); ?>">
<?php echo $_SESSION['reg_error']; $_SESSION['reg_error']=''; ?>
</div>
<?php } ?>

    <div class="form-group">
        <div class="input-group input-group-lg">
            <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-male"></i></span></div>
            <input class="form-control input-lg" required="required" placeholder="<?php _e( "Full Name" , "download-manager" ); ?>" type="text" size="20" id="displayname" value="<?php echo isset($_SESSION['tmp_reg_info']['display_name'])?$_SESSION['tmp_reg_info']['display_name']:''; ?>" name="wpdm_reg[display_name]">
        </div>
    </div>
    <div class="form-group">
        <div class="input-group input-group-lg">
            <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-user"></i></span></div>
            <input class="form-control" required="required" placeholder="<?php _e( "Username" , "download-manager" ); ?>" type="text" size="20" class="required" id="user_login" value="<?php echo isset($_SESSION['tmp_reg_info']['user_login'])?$_SESSION['tmp_reg_info']['user_login']:''; ?>" name="wpdm_reg[user_login]">
        </div>
    </div>
    <div class="form-group">

        <div class="input-group input-group-lg">
            <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-envelope"></i></span></div>
            <input class="form-control input-lg" required="required" type="email" size="25" placeholder="<?php _e( "E-mail" , "download-manager" ); ?>" id="user_email" value="<?php echo isset($_SESSION['tmp_reg_info']['user_email'])?$_SESSION['tmp_reg_info']['user_email']:''; ?>" name="wpdm_reg[user_email]">
        </div>
                      
    </div>

    <?php if(!isset($params['verifyemail']) || $params['verifyemail'] == 'false'){ ?>
    <div class="form-group row">
        <div class="col-md-6">
        <div class="input-group input-group-lg">
            <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-key"></i></span></div>
            <input class="form-control" placeholder="<?php _e( "Password" , "download-manager" ); ?>" required="required" type="password" size="20" class="required" id="password" value="" name="wpdm_reg[user_pass]">
        </div>
            </div>
        <div class="col-md-6">
            <div class="input-group input-group-lg">
                <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-check-circle"></i></span></div>
                <input class="form-control input-lg" data-match="#password" data-match-error="<?php _e( "Not Matched!" , "download-manager" ); ?>" required="required" placeholder="<?php _e( "Confirm Password" , "download-manager" ); ?>" type="password" size="20" class="required" equalto="#password" id="confirm_user_pass" value="" name="confirm_user_pass">
            </div>
        </div>
    </div>
    <?php } ?>

    <?php  if(!isset($params['captcha']) || $params['captcha'] == 'true'){ ?>
    <div class="form-group row">
        <div class="col-md-12">
            <input type="hidden" id="recap" required="required" name="recap" value="">
            <script src='https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit'></script>
            <div  id="reCaptchaLock"></div>
            <script type="text/javascript">
                var ctz = new Date().getMilliseconds();
                var siteurl = "<?php echo home_url('/?nocache='); ?>"+ctz,force="<?php echo $force; ?>";
                var verifyCallback = function(response) {
                                        jQuery('#recap').val(response);
                                    };
                var widgetId2;
                var onloadCallback = function() {
                    grecaptcha.render('reCaptchaLock', {
                        'sitekey' : '<?php echo get_option('_wpdm_recaptcha_site_key'); ?>',
                        'callback' : verifyCallback,
                        'theme' : 'light'
                    });
                };
            </script>
        </div>

    </div>
    <?php } ?>




    <?php do_action("wpdm_register_form"); ?>
    <?php do_action("register_form"); ?>


    <div class="row">
        <div class="col-md-<?php echo ($loginurl != '')?8:12; ?>"><button type="submit" class="btn btn-success btn-lg btn-block" id="registerform-submit" name="wp-submit"><i class="fas fa-user-plus"></i> &nbsp;<?php _e( "Join Now!" , "download-manager" ); ?></button></div>
        <?php if($loginurl != ''){ ?>
        <div class="col-md-4"><a href="<?php echo $loginurl;?>" class="btn btn-secondary btn-lg btn-block" id="registerform-login" name="wp-submit"><?php _e( "Login" , "download-manager" ); ?></a></div>
        <?php } ?>
    </div>

    <!-- /div>
    </div -->
</form>


    <script>
        jQuery(function ($) {
            $.getScript('<?php echo WPDM_BASE_URL.'assets/js/validator.min.js'; ?>', function () {
                $('#registerform').validator();
            });
            var llbl = $('#registerform-submit').html();
            $('#registerform').submit(function () {
                <?php  if(!isset($params['captcha']) || $params['captcha'] == 'true'){ ?>
                if($('#recap').val() == '') { alert("Invalid CAPTCHA!"); return false;}
                <?php } ?>
                $('#registerform-submit').html("<i class='fa fa-spin fa-spinner'></i> <?php _e( "Please Wait..." , "download-manager" ); ?>");
                $(this).ajaxSubmit({
                    success: function (res) {
                        if (!res.match(/success/)) {
                            $('form .alert-danger').hide();
                            $('#registerform').prepend("<div class='alert alert-danger'>"+res+"</div>");
                            $('#registerform-submit').html(llbl);
                        } else {
                            $('#registerform-submit').html("<i class='fa fa-check-circle'></i> <?php _e( "Redirecting..." , "download-manager" ); ?>");
                            location.href = "<?php echo $reg_redirect; ?>";
                        }
                    }
                });
                return false;
            });
        });
    </script>

<?php } else echo "<div class='alert alert-warning'>". __( "Registration is disabled!" , "download-manager" )."</div>"; ?>
</div>
</div>
