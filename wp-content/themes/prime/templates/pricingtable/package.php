<?php
$meta = get_post_meta($post->ID, 'wpeden_post_meta', true);
$fets = explode("\n", $meta['package_features']);
?><div class="col-md-6 pricing-table-block wow fadeInUp" data-wow-delay="100ms">
    <div class="col-md-6 tb-left text-center">
        <h5 class="text-<?php echo $meta['package_color']; ?>"><?php echo $post->post_title; ?></h5>
        <h3><?php echo $meta['package_price']; ?></h3>
        <p><i class="tn-star  text-<?php echo $meta['package_color']; ?>"></i><i class="tn-star  text-<?php echo $meta['package_color']; ?>"></i><i class="tn-star"></i><i class="tn-star"></i><i class="tn-star"></i></p>
        <p class="pac-info"><?php echo $meta['package_desc']; ?></p>
        <a href="<?php echo $meta['package_btnurl']; ?>" class="btn btn-<?php echo $meta['package_color']; ?>"><?php echo $meta['package_btnlbl']; ?></a>
    </div>
    <div class="col-md-6 tb-right">
        <ul class="list-group">
            <?php foreach($fets as $fet){ ?>
                <li class="list-group-item"><?php echo $fet; ?></li>
            <?php } ?>

        </ul>
    </div>
</div>