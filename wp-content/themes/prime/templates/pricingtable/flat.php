<?php
$meta = get_post_meta($post->ID, 'wpeden_post_meta', true);
$fets = explode("\n", $meta['package_features']);
?><div class="col-md-3 pricing-table-block pricing-table-flat">
    <div class="pt-header text-center">
        <h5 class="text-<?php echo $meta['package_color']; ?>"><?php echo $post->post_title; ?></h5>
        <h3><?php echo $meta['package_price']; ?></h3>
        <p>
            <?php for($i = 0; $i< $meta['package_rating']; $i++ ): ?>
            <i class="tn-star  text-<?php echo $meta['package_color']; ?>"></i>
            <?php endfor; ?>
            <?php for($i = $meta['package_rating']; $i<5; $i++ ): ?>
                <i class="tn-star"></i>
            <?php endfor; ?>
           </p>
        <p class="pac-info"><?php echo $meta['package_desc']; ?></p>
        <a href="<?php echo $meta['package_btnurl']; ?>" class="btn btn-flat btn-lg btn-<?php echo $meta['package_color']; ?>"><?php echo $meta['package_btnlbl']; ?></a>
    </div>

    <ul class="list-group list-group-custom">
        <?php foreach($fets as $fet){ ?>
            <li class="list-group-item "><?php echo $fet; ?></li>
        <?php } ?>

    </ul>

</div>