<?php
	add_action( 'wp_ajax_pafe_posts_list', 'pafe_posts_list' );
	add_action( 'wp_ajax_nopriv_pafe_posts_list', 'pafe_posts_list' );
	function pafe_posts_list() {
		global $wpdb;		 
			$post_type = sanitize_text_field( $_POST['post_type'] );
			$posts_per_page = sanitize_text_field( $_POST['posts_per_page'] );
			$taxonomy = sanitize_text_field( $_POST['taxonomy'] ); 
			$term_id = sanitize_text_field( $_POST['term_id'] );
			$author = sanitize_text_field( $_POST['author'] );
			$date = sanitize_text_field( $_POST['date'] );
 			$excerpt = sanitize_text_field( $_POST['excerpt'] );
 			$layout = sanitize_text_field( $_POST['layout'] );

			$args = array();
			if (!empty($post_type) && !empty($posts_per_page)) {
				$args['post_type'] = $post_type;			
			    $args['posts_per_page'] = $posts_per_page;	
			    if ( $term_id != 0 ) {
				    $args['tax_query'] = array(			    	
					    array(
						    'taxonomy' => $taxonomy,
						    'field' => 'term_id',
						    'terms' => $term_id
					    ),
					);
				}	 
			}		 
?>	
<div class="<?php if ($layout == 'layout_1') {
	echo 'pafe-posts-list__post-inner';
} elseif ($layout == 'layout_2') {
	echo 'pafe-posts-list-first-show_layout-2'; 
} elseif ($layout == 'layout_3') {
	echo 'pafe-posts-list-first-show_layout-3';
}	
?>">

<?php
	$query = new WP_Query($args);
	$index = 0;
	if ($query->have_posts()) : while($query->have_posts()) : $query->the_post();
		$index ++;
?>				
	<?php if ($index == 1): ?>
	<div class="pafe-post-list__left">
		<div class="pafe-card-left">
			<?php if ($layout !== 'layout_3'): ?>
			<div class="pafe-card-left__inner" style="background-image: url('<?php echo get_the_post_thumbnail_url();?>');">
			<?php endif; ?> 
				<?php if ($layout == 'layout_3'): ?>
				<div class="pafe-card-left__inner-layout_3" style="background-image: url('<?php echo get_the_post_thumbnail_url();?>');"></div>	
				<?php endif; ?>
			 		<a class="<?php if ($layout == 'layout_3') {
						echo 'pafe-card-left__content-layout_3';
					} else {
						echo 'pafe-card-left__content';
					}?>" href="<?php echo get_permalink(); ?>">	
					<div class="pafe-card-left__title"><?php echo get_the_title(); ?></div>
					<div class="pafe-card-left__info">
						<?php if ($author == 'yes'): ?>
						<span class="pafe-card-left__author">
							<i class="fa fa-user" aria-hidden="true"></i> <?php echo get_the_author(); ?>
						</span>
						<?php endif; ?>	
						<?php if ($date == 'yes'): ?>
						<span class="pafe-card-left__tag" style="padding-left: 10px;">
							<i class="far fa-clock"></i> <?php echo get_the_date(); ?>
						</span>
						<?php endif; ?>
					</div>	
				</a>
			<?php if ($layout !== 'layout_3'): ?>	
			</div> 
			<?php endif; ?>
		</div>	
	</div>  
<?php endif; ?>	 
<?php if ($index == 2): ?>	 
	<div class="pafe-post-list__right"> 
<?php endif; ?>
	<?php if ( $index >=2 ) : ?>	
		<div class="pafe-card-right">
			<div class="pafe-card-right__inner">
				<a class="pafe-card-right__thumbnail" href="<?php echo get_permalink(); ?>" style="background-image: url('<?php echo get_the_post_thumbnail_url();?>');">
				</a>
				<div class="pafe-card-right__content">			
					<a href="<?php echo get_permalink(); ?>"><div class="pafe-card-right__title"><?php echo get_the_title(); ?></div></a>
					<?php if ($excerpt == 'yes'): ?>
					<div class="pafe-card-right__description" >
						<?php echo get_the_excerpt(); ?>
					</div>
					<?php endif; ?>
					<div class="pafe-card-right__info"> 
						<?php if ($date == 'yes'): ?>
							<span class="pafe-card-right__tag" ><i class="far fa-clock"></i><?php echo get_the_date(); ?></span>
						<?php endif; ?>
					</div>
				</div>
			</div> 
		</div>
	<?php endif; ?>	
<?php
	endwhile; endif;           
	wp_reset_postdata();	
?>
</div> 
<?php wp_die(); } ?>
