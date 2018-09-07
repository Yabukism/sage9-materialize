<?php 
 
get_header(); ?>    
    
     
            
<div class="container">
    <div class="row">
        <div class="col-md-<?php echo apply_filters("homepage_content_grid",8); ?>">
            <?php        get_template_part('loop'); ?>
        </div>
        <?php
        $homepage_sidebar_grid = apply_filters("homepage_sidebar_grid",4);

        if($homepage_sidebar_grid>0){
            ?>
            <div class="col-md-<?php echo $homepage_sidebar_grid; ?>">

                <?php  dynamic_sidebar("homepage_sidebar_right"); ?>

            </div>

        <?php
        }
        ?>
    </div>
</div>
      
<?php get_footer(); ?>
