<?php

/**
 * Marketplace Plugin Helper Functions
 */


/**
 * Get product base price
 * @param $pid
 * @return string
 */
function wpmp_base_price($pid){
   $pinfo = get_post_meta($pid,"wpmp_list_opts",true); 
   return number_format($pinfo['base_price'],2);
}


/**
 * Get product sales price
 * @param $pid
 * @return string
 */
function wpmp_sales_price($pid){
   $pinfo = get_post_meta($pid,"wpmp_list_opts",true); 
   return number_format($pinfo['sales_price'],2);
}

/**
 * Get effective price
 * @param $pid
 * @return string
 */

function wpmp_effective_price($pid){
   $pinfo = get_post_meta($pid,"wpmp_list_opts",true); 
   $price = $pinfo['sales_price']?$pinfo['sales_price']:$pinfo['base_price'];
   return number_format($price,2); 
}

/**
 * Get default currency sign
 * @return mixed
 */
function wpmp_currency_sign(){
    return get_option('_wpmp_curr_sign','$');
}

/**
 * Generate add to cart link / button
 * @param $post
 * @return string
 */
function wpmp_waytocart($post){
    $pinfo = get_post_meta($post->ID,"wpmp_list_opts",true);
    @extract($pinfo);
     
    $cart_enable="";
        if(isset($settings['stock']['enable'])&&$settings['stock']['enable']==1){
            if($manage_stock==1){
                if($stock_qty>0)$cart_enable=""; else $cart_enable=" disabled ";
            } 
        }
    if(isset($price_variation))
        $html = "<a href='".get_permalink($post->ID)."' class='btn btn-bordered' ><i class='icon-shopping-cart'></i> Add to Cart</a>";
    else{
        $html = <<<PRICE
                        <form method="post" action="" name="cart_form">
                        <input type="hidden" name="add_to_cart" value="add">
                        <input type="hidden" name="pid" value="$post->ID">
                        <input type="hidden" name="discount" value="$discount">
                         
PRICE;
       $html.='<button '.$cart_enable.' class="btn btn-bordered" type="submit" ><i class="icon-shopping-cart"></i> Add to Cart</button></form>';
        
    }
    return $html;
}
