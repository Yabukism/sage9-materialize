<?php

get_header();

?>
    <div class="headertitle">
        <div class="headercontent">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h1>404</h1>
                        <span class="wtnbreadcrumbs">
                        Page Not Found!
                    </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container container-boxed">
        <div class="row">


            <div class="col-md-12">
                <div class="single-page text-center">

                  <p class="lead">Go to <a href="<?php echo home_url('/'); ?>">Homepage</a> or search again</p>
                    <form action="<?php echo home_url('/'); ?>">
                        <input name="s" id="s" type="text" class="form-control input-lg">
                    </form>

                </div>
            </div>


        </div>
    </div>


<?php
get_footer();
