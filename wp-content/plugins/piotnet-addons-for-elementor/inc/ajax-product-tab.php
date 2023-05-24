<?php
	add_action( 'wp_ajax_pafe_product_tabs', 'pafe_product_tabs' );
	add_action( 'wp_ajax_nopriv_pafe_product_tabs', 'pafe_product_tabs' );
	function pafe_product_tabs() {
		global $wpdb;		 
		$posts_per_page = sanitize_text_field( $_POST['posts_per_page'] );
		$term_id = sanitize_text_field( $_POST['term_id'] );
		$args = array();
			if ( !empty($posts_per_page)) {
				$args['posts_per_page'] = $posts_per_page;	
			}
				$args['post_type'] = 'product';			
			    
		    if ( $term_id != 0 ) {
			    $args['tax_query'] = array(			    	
				    array(
					    'taxonomy' => 'product_cat',
					    'field' => 'term_id', 
					    'terms' => $term_id
				    ),
				);
			}	 
						 
	global $product;	
	$query = new WP_Query( $args );
	$product = new WC_Product( );
	if ( $query->have_posts() ) : while( $query->have_posts() ) : $query->the_post();
?>				 
	<div class="pafe-product-tabs__result-inner">
		<div class="pafe-product-tabs__inner">  
			<a class="pafe-product-tabs__thumbnail" href="<?php echo get_permalink(); ?>">
				<div class="pafe-product-tabs__thumbnail-img" style="background-image: url('<?php echo get_the_post_thumbnail_url(); ?>');"> </div>
			</a>
			<div class="pafe-product-tabs__content">  
				<a href="<?php echo get_permalink(); ?>" >
					<span class="pafe-product-tabs__title"><?php echo get_the_title(); ?></span>
				</a>
				
				<div class="pafe-product-tabs__button"> 
					<a href="?add-to-cart=<?php echo get_the_ID(); ?>" class="qn_btn">Mua ngay</a>
				</div>
			</div> 
		</div>
	</div> 	
<?php
	endwhile; endif;             
	wp_reset_postdata();	
?>
	

<?php wp_die(); } ?>
