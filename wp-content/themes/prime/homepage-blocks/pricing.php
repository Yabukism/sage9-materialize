<?php if(WPEdenThemeEngine::NextGetOption('thenext_disable_pricing_section', 0) == 0): ?>
<section class="pricing" id="pricing">
    <div class="container">


        <div class="row">
            <div class="col-md-12">
                <div class="media">
                    <div class="pull-left">
                        <img style="width: 30px" src="https://cdn2.iconfinder.com/data/icons/circle-icons-1/64/cart-64.png" />
                    </div>
                    <div class="media-body">
                        <h2><?php echo WPEdenThemeEngine::NextGetOption('home_optimus_pricing_title', 'Pricing'); ?></h2>
                        <?php echo WPEdenThemeEngine::NextGetOption('home_optimus_pricing_desc','Get from the best offers:'); ?>
                    </div>
                </div><br/>
            </div>

            <div class="col-md-12">
                <?php

                for($i=1; $i<=4; $i++){
                    $pid = WPEdenThemeEngine::NextGetOption("home_optimus_pricing_col_".$i);
                    TheNextChild::PricingPackage(array('id'=>$pid,'echo' => 1, 'template' => 'flat'));
                }
                ?>

            </div>

        </div>
    </div>
</section>
<?php endif;