
</div>	<!-- end /.wrapcontent -->
<!-- CALL TO ACTION
    ==========================================-->
<div id="prospercalltoaction" class="actionbeforefooter text-center">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <?php echo WPEdenThemeEngine::NextGetOption('thenext_c2a_text', 'Ask your site visitor to do something here ...'); ?> <a target="_blank" class="actionbutton" href="<?php echo WPEdenThemeEngine::NextGetOption('thenext_c2a_link', '#'); ?>"><i class="fa fa-send-o"></i> <?php echo WPEdenThemeEngine::NextGetOption('thenext_c2a_link_label', 'Call To Action'); ?> </a>
            </div>
        </div>
    </div>
</div>
<!-- FOOTER
    ==========================================-->
<footer id="prosperfooter" class="themefooter section medium-padding bg-graphite">
    <div class="container">
        <div class="section-inner row">
            <div class="column column-1 col-sm-3 rightbd">
                <div class="widgets">
                    <?php dynamic_sidebar('footer1') ?>
                </div>
            </div>
            <!-- /footer-a -->
            <div class="column column-1 col-sm-3 rightbd">
                <div class="widgets">
                    <?php dynamic_sidebar('footer2') ?>
                </div>
            </div>
            <!-- /footer-b -->
            <div class="column column-1 col-sm-3">
                <div class="widgets">
                    <?php dynamic_sidebar('footer3') ?>
                </div>
            </div>
            <!-- /footer-c -->
            <div class="column column-1 col-sm-3">
                <div class="widgets">
                    <div class="widget widget_text" id="text-3">
                        <div class="widget-content">
                            <h3 class="widget-title">Follow Us</h3>
                            <div class="textwidget">
                                <p>
                                    <a href="<?php echo WPEdenThemeEngine::NextGetOption('facebook_profile_url');?>"><i class="fa fa-facebook"></i> Like us on Facebook</a>
                                </p>
                                <p>
                                    <a href="<?php echo WPEdenThemeEngine::NextGetOption('twitter_profile_url');?>"><i class="fa fa-twitter"></i> Follow us on Twitter</a>
                                </p>
                                <p>
                                    <a href="<?php echo WPEdenThemeEngine::NextGetOption('googleplus_profile_url');?>"><i class="fa fa-google-plus"></i> Visit on Google Plus</a>
                                </p>
                                <p>
                                    <a href="<?php echo WPEdenThemeEngine::NextGetOption('linkedin_profile_url');?>"><i class="fa fa-linkedin"></i> Visit on Linkedin</a>
                                </p>
                            </div>
                        </div>
                        <div class="clear">
                        </div>
                    </div>
                </div>
            </div>
            <!-- /footer-d -->
            <div class="clearfix">
            </div>
        </div>
        <!-- /section-inner -->
    </div>
    <!-- /container -->
</footer>
<!-- /footer -->
<div class="sectioncredits">
    <div class="container">
        <div class="row">
            <div class="col-md-12">

					<div class="credits-left pull-right">
					Theme by <a href="https://www.wpdownloadmanager.com">WordPress Download Manager</a></div>
                <a class="navbar-brand-middle site-logo" href="<?php echo esc_url(home_url('/')); ?>"><?php
                    $logourl = ( WPEdenThemeEngine::NextGetOption('site_logo_footer') );
                    if ($logourl)
                        echo "<img style='max-height: 40px;margin-top: -5px' class='footer-logo' src='{$logourl}' title='" . get_bloginfo('sitename') . "' alt='" . get_bloginfo('sitename') . "' />";
                    else
                        echo get_bloginfo('sitename');
                    ?></a>
            </div>
            <div class="clear">
            </div>
        </div>
    </div>
</div>
<!-- scripts -->
<?php wp_footer(); ?>
<script type='text/javascript' src='<?php echo get_template_directory_uri(); ?>/js/masonry.js'></script>
<script type='text/javascript' src='<?php echo get_template_directory_uri(); ?>/js/carousel.js'></script>
<script type='text/javascript' src='<?php echo get_template_directory_uri(); ?>/js/init.js'></script>
<!-- end scripts -->
</div>
<!-- end /.wrapall -->

</body>
</html>
