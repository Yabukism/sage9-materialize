
		<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri();?>/modules/slitslider/css/demo.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri();?>/modules/slitslider/css/style.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri();?>/modules/slitslider/css/custom.css" />
		<script type="text/javascript" src="<?php echo get_template_directory_uri();?>/modules/slitslider/js/modernizr.custom.79639.js"></script>
		<noscript>
			<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri();?>/modules/slitslider/css/styleNoJS.css" />
		</noscript>

    <div class="container"><div class="row"><div class="col-md-12">
        <div class="demo-2">
		   

            <div id="slider" class="sl-slider-wrapper">

				<div class="sl-slider">
                
                    <?php
                    query_posts(array(
                            'post_type' => 'wpdmpro',
                            'showposts' => 4,
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'wpdmcategory',
                                    'terms' => wpeden_get_theme_opts('slider_category'),
                                    'field' => 'term_id',
                                )
                            ),
                            'orderby' => 'title',
                            'order' => 'ASC' )
                    );
                    $tp = 0; $dataor = 'horizontal';
                    while(have_posts()){ the_post();
                                
                            $tp++;
                            $dataor = $dataor=='horizontal'?'vertical':'horizontal';
                            $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'full');
                            $large_image_url = $large_image_url[0];
                           ?>
                           
                           <div class="sl-slide" data-orientation="<?php echo $dataor; ?>" data-slice1-rotation="-25" data-slice2-rotation="-25" data-slice1-scale="2" data-slice2-scale="2">
                                <div class="sl-slide-inner">
                                    <div class="bg-img" style="background:  rgba(0,0,0,0.4) url('<?php echo $large_image_url; ?>');"></div>
                                    <div class="container"><div class="row"><div class="col-md-12">
                                    <h2><a href='<?php the_permalink(); ?>'><?php the_title(); ?></a></h2>
                                    <div class="capt">
                                    <p><?php echo centrino_get_excerpt($post, 100); ?></p>
                                    <a class="btn btn-theme btn-sm" href='<?php the_permalink(); ?>'>READ MORE <i class="fa fa-long-arrow-right"></i></a></div>
                                            </div></div> </div>
                                </div>
                            </div>
                           
                           <?php        
                   
                        } 
                    ?>
				
					 
                    
				</div><!-- /sl-slider -->

				<nav id="nav-dots" class="nav-dots">
					<span class="nav-dot-current"></span>
                    <?php for($i=1; $i<$tp; $i++) echo "<span></span>"; ?>

				</nav>

			</div><!-- /slider-wrapper -->

			 
        </div>
        </div> </div> </div>
		<script type="text/javascript" src="<?php echo get_template_directory_uri();?>/modules/slitslider/js/jquery.ba-cond.min.js"></script>
		<script type="text/javascript" src="<?php echo get_template_directory_uri();?>/modules/slitslider/js/jquery.slitslider.js"></script>
		<script type="text/javascript">	
			jQuery(function() {
			
				var Page = (function() {

					var $nav = jQuery( '#nav-dots > span' ),
						slitslider = jQuery( '#slider' ).slitslider( {
							onBeforeChange : function( slide, pos ) {

								$nav.removeClass( 'nav-dot-current' );
								$nav.eq( pos ).addClass( 'nav-dot-current' );

							}
						} ),

						init = function() {

							initEvents();
							
						},
						initEvents = function() {

							$nav.each( function( i ) {
							
								jQuery( this ).on( 'click', function( event ) {
									
									var $dot = jQuery( this );
									
									if( !slitslider.isActive() ) {

										$nav.removeClass( 'nav-dot-current' );
										$dot.addClass( 'nav-dot-current' );
									
									}
									
									slitslider.jump( i + 1 );
									return false;
								
								} );
								
							} );

						};

						return { init : init };

				})();

				Page.init();

				/**
				 * Notes: 
				 * 
				 * example how to add items:
				 */

				/*
				
				var $items  = jQuery('<div class="sl-slide sl-slide-color-2" data-orientation="horizontal" data-slice1-rotation="-5" data-slice2-rotation="10" data-slice1-scale="2" data-slice2-scale="1"><div class="sl-slide-inner bg-1"><div class="sl-deco" data-icon="t"></div><h2>some text</h2><blockquote><p>bla bla</p><cite>Margi Clarke</cite></blockquote></div></div>');
				
				// call the plugin's add method
				ss.add($items);

				*/
			
			});
		</script>
	 