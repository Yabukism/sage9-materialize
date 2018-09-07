<footer>
    <div class="container">
        <div class="row">
            <div class="col-md-12 footer-area">
                <div class="row">
                    <div class="col-md-3"><?php dynamic_sidebar('footer1') ?></div>
                    <div class="col-md-3"><?php dynamic_sidebar('footer2') ?></div>
                    <div class="col-md-3"><?php dynamic_sidebar('footer3') ?></div>
                    <div class="col-md-3"><?php dynamic_sidebar('footer4') ?></div>
                </div>


                <div class="row">
                    <div class="col-md-12 text-center">
                        <?php if(WPEdenThemeEngine::NextGetOption('facebook_profile_url') != '') : ?><a href="<?php echo WPEdenThemeEngine::NextGetOption('facebook_profile_url') ?>" class="btn btn-social btn-facebook btn-sm"><i class="tn-facebook"></i></a><?php endif; ?>
                        <?php if(WPEdenThemeEngine::NextGetOption('twitter_profile_url') != '') : ?><a href="<?php echo WPEdenThemeEngine::NextGetOption('twitter_profile_url') ?>" class="btn btn-social btn-twitter btn-sm"><i class="tn-twitter"></i></a><?php endif; ?>
                        <?php if(WPEdenThemeEngine::NextGetOption('google_profile_url') != '') : ?><a href="<?php echo WPEdenThemeEngine::NextGetOption('google_profile_url') ?>" class="btn btn-social btn-google-plus btn-sm"><i class="tn-google"></i></a><?php endif; ?>
                        <?php if(WPEdenThemeEngine::NextGetOption('linkedin_profile_url') != '') : ?><a href="<?php echo WPEdenThemeEngine::NextGetOption('linkedin_profile_url') ?>" class="btn btn-social btn-linkedin btn-sm"><i class="tn-linkedin"></i></a><?php endif; ?>
                        <?php if(WPEdenThemeEngine::NextGetOption('pinterest_profile_url') != '') : ?><a href="<?php echo WPEdenThemeEngine::NextGetOption('pinterest_profile_url') ?>" class="btn btn-social btn-pinterest btn-sm"><i class="tn-pinterest"></i></a><?php endif; ?>
                        <br/>
                        <br/>
                        <?php
                            echo get_bloginfo('sitename');
                        ?> &nbsp; / &nbsp;
                        Theme By <a class="footer-brand" href="http://www.wpdownloadmanager.com/">WordPress Download Manager</a>
                        <br/>
                        <br/>
                    </div>

                </div>

            </div>
        </div>
    </div>
</footer>
<?php if (defined('THENEXT_LEFT_NAV')) echo '</div> </div> </div>'; ?>

<?php do_action(THENEXT_THEME_PREFIX . "body_content_after"); ?>

<?php wp_footer(); ?>
</body>
</html>