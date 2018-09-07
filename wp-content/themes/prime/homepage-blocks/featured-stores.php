<?php if(WPEdenThemeEngine::NextGetOption('thenext_disable_stores_section', 0) == 0): ?>
<section class="store-area" id="store-area">
    <?php
    $featured_store = WPEdenThemeEngine::NextGetOption('home_featured_store', 1);
    ?>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="media">
                    <div class="pull-left">
                        <img style="width: 30px" src="https://cdn0.iconfinder.com/data/icons/kameleon-free-pack-rounded/110/Shop-32.png" />
                    </div>
                    <div class="media-body">
                        <h2><?php echo WPEdenThemeEngine::NextGetOption('thenext_stores_section_heading', 'Stores'); ?></h2>
                    </div>
                </div><br/>
            </div>
            <div class="col-md-5">
                <div class="panel panel-featured panel-category" id="new-stores">
                    <div class="panel-heading"><?php echo WPEdenThemeEngine::NextGetOption('thenext_new_stores_heading', 'New Stores'); ?></div>

                    <?php
                    $params = array('number' => -1, 'meta_key'=> '__wpdm_public_profile');
                    $users = get_users($params);
                    $featured_store = WPEdenThemeEngine::NextGetOption('thenext_new_store_id', 1);
                    $fets = get_user_meta( $featured_store, '__wpdm_public_profile', true);
                    $s = 0;
                    foreach($users as $user){
                        $profile = get_user_meta($user->ID, '__wpdm_public_profile', true);

                        $logo = $profile['logo'] != ''?$profile['logo']:get_avatar_url($user->ID);
                        ?>
                        <div class="col-md-4 featured-item-block">
                            <div class="single-item">
                                <a href="<?php echo get_author_posts_url( $user->ID, $user->user_nicename ); ?>" class="title">
                                    <img alt="Logo" src="<?php echo $logo; ?>" />
                                </a>
                            </div>
                        </div>
                    <?php if($s++ >=6 ) break;} ?>
                    <div class="clear"></div>
                    <div class="panel-footer">
                        <a href="<?php echo WPEdenThemeEngine::NextGetOption('thenext_all_stores_page_url', ''); ?>" class="ui button mini blue no-radius pull-right">Explore</a>
                        <span class="ui button mini yellow no-radius"><?php echo count($users); ?> Stores</span>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <div class="panel panel-featured panel-category featured-store">
                    <div class="panel-heading"><?php echo WPEdenThemeEngine::NextGetOption('thenext_featured_stores_heading', 'Featured Store'); ?></div>
                    <div class="featured-store-content">
                        <div class="col-md-4">
                            <div class="shop-box">
                                <img class="featured-shop-logo" src="<?php
                                $logo = $fets['logo'] != ''?$fets['logo']:get_avatar_url($featured_store);
                                echo $logo ?>" />
                            </div>
                        </div>
                        <div class="col-md-8">
                            <?php
                            $params = array('posts_per_page' => 6, 'post_type'=> 'wpdmpro', 'author' => $featured_store);
                            $q = new WP_Query($params);
                            while($q->have_posts()){ $q->the_post();
                                ?>
                                <div class="col-md-4 featured-item-block">
                                    <div class="single-item">
                                        <a href="<?php the_permalink(); ?>" class="title">
                                            <?php wpdm_post_thumb(array(400,280)); ?>
                                        </a>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="clear"></div>
                    <div class="panel-footer">
                        <a href="#" class="ui button mini blue no-radius pull-right">Explore</a>
                        <span class="ui button mini yellow no-radius"><?php echo $q->found_posts; ?> Items</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
<?php endif; ?>