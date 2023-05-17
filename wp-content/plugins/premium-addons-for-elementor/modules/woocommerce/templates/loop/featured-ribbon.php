<?php
/**
 * PA WooCommerce Products - Featured Flash.
 *
 * @package PA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

global $post, $product;

$featured_text = __( 'New', 'premium-addons-for-elementor' );

if ( '' !== self::$settings['featured_string'] ) {
	$featured_text = self::$settings['featured_string'];
}

?>
<?php if ( $product->is_featured() ) : ?>
	<div class="premium-woo-product-featured-wrap">
		<span class="premium-woo-product-featured"><?php echo esc_html( $featured_text ); ?></span>
	</div>
	<?php
endif;

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
