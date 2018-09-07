<?php
/**
 * Template Name: Contact
 */
get_header();

?>
    <div class="headertitle">
        <div class="headercontent">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h1><?php the_title(); ?></h1>
                        <span class="wtnbreadcrumbs">
                        <?php if ( function_exists('yoast_breadcrumb') ) { ?>

                            <?php
                            add_filter( 'wp_seo_get_bc_title', create_function('$title, $id','return "";'), 10, 2 );
                            yoast_breadcrumb('','');
                            ?>

                        <?php } ?>
                    </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container container-boxed">


        <iframe
            frameborder="0" style="border:0;width: 100%;height: 340px"
            src="https://www.google.com/maps/embed/v1/place?key=AIzaSyDgGeO0G9x2F8jpGyEwJ47PhdOYVJIfSG0&q=<?php echo urlencode(WPEdenThemeEngine::NextGetOption('map_address')); ?>" allowfullscreen>
        </iframe>

        <br/>

        <h4 style="margin-bottom:20px;">Get in Touch</h4>


        <form method="post" action="" id="contactform">
            <div class="form">
                <div class="row">
                    <div class="col-md-6">
                        <p><input type="text" class="form-control input-lg" name="contact[name]" placeholder="Name *"></p>
                    </div>
                    <div class="col-md-6">
                        <p><input type="email" class="form-control input-lg" name="contact[email]" placeholder="E-mail Address *"></p>
                    </div>
                </div>

                <textarea name="contact[message]" rows="7" class="form-control" placeholder="Type your Message *"></textarea>
                <div id="done" style="display: none" class="pull-right text-success"><i class="tn-check"></i> Your message has been sent. Thank you!</div>
                <button type="submit" id="submit" class="btn btn-info no-radius"><i class="fa fa-send-o"></i> &nbsp;Send Message</button>

            </div>
        </form>
    </div>


<?php
get_footer();
