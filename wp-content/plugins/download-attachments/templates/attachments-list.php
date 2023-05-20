<?php
/**
 * The template for displaying attachments as list.
 *
 * Override this template by copying it to yourtheme/attachments-list.php
 *
 * @author Digital Factory
 * @package Download Attachments/Templates
 */

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( empty( $args ) || ! is_array( $args ) )
	exit;

// extract variable storing all the parameters and attachments
extract( $args );

// you can start editing here 
?>

<?php if ( ! ( $display_empty === 0 && $count === 0 ) ) : ?>

	<?php if ( $container !== '' ) : ?>
		<<?php echo $container . ( $container_id !== '' ? ' id="' . $container_id . '"' : '' ) . ( $container_class !== '' ? ' class="' . $container_class . '"' : '' ); ?>>
	<?php endif; ?>

	<?php if ( $title !== '' ) : ?>
		<?php echo ( $title !== '' ? '<' . $title_container . ' class="' . $title_class . '">' . $title . '</' . $title_container . '>' : '' ); ?>
	<?php endif; ?>

<?php endif; ?>

<?php echo $content_before; ?>

<?php if ( $count > 0 ) :
	$i = 1; ?>

	<ul class="da-attachments-list">

	<?php 
	// attachments loop
	foreach ( $attachments as $attachment ) : 
	?>

		<?php if ( $use_desc_for_title === 1 && $attachment_description !== '' ) :
			$attachment_title = apply_filters( 'da_display_attachment_title', $attachment['description'] );
		else :
			$attachment_title = apply_filters( 'da_display_attachment_title', $attachment['title'] );
		endif; ?>

		<li class="<?php echo $attachment['type']; ?>">

			<?php if ( $display_index === 1 ) : ?>
			<span class="attachment-index"><?php echo $i++; ?></span>
			<?php endif; ?>

			<?php if ( $display_icon === 1 ) : ?>
				<img class="attachment-icon" src="<?php echo $attachment['icon_url']; ?>" alt="<?php echo $attachment['type']; ?>" /> 
			<?php endif; ?>

			<?php if ( $link_before !== '' ) : ?>
				<span class="attachment-link-before"><?php echo $link_before; ?></span>
			<?php endif; ?>
				
			<?php da_download_attachment_link( $attachment['ID'], true, array( 'title' => $attachment_title, 'class' => 'attachment-link' ) ); ?>

			<?php if ( $link_after !== '' ) : ?>
				<span class="attachment-link-after"><?php echo $link_after; ?></span>
			<?php endif; ?>

			<br />

			<?php if ( $display_caption === 1 && $attachment['caption'] !== '' ) : ?>
				<span class="attachment-caption"><?php echo $attachment['caption']; ?></span><br />
			<?php endif; ?>

			<?php if ( $display_description === 1 && $use_desc_for_title === 0 && $attachment['description'] !== '' ) : ?>
				<span class="attachment-description"><?php echo $attachment['description']; ?></span><br />
			<?php endif; ?>

			<?php if ( $display_date === 1 ) : ?>
				<span class="attachment-date"><span class="attachment-label"><?php echo __( 'Date added', 'download-attachments' ); ?>:</span> <?php echo date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $attachment['date_added'] ) ); ?></span>
			<?php endif; ?>

			<?php if ( $display_user === 1 ) : ?>
				<span class="attachment-user"><span class="attachment-label"><?php echo __( 'Added by', 'download-attachments' ); ?>:</span> <?php echo get_the_author_meta( 'display_name', $attachment['user_added'] ); ?></span>
			<?php endif; ?>

			<?php if ( $display_size === 1 ) : ?>
				<span class="attachment-size"><span class="attachment-label"><?php echo __( 'File size', 'download-attachments' ); ?>:</span> <?php echo size_format( $attachment['size'] ); ?></span>
			<?php endif; ?>

			<?php if ( $display_count === 1 ) : ?>
				<span class="attachment-downloads"><span class="attachment-label"><?php echo __( 'Downloads', 'download-attachments' ); ?>:</span> <?php echo $attachment['downloads']; ?></span>
			<?php endif; ?>

		</li>

	<?php endforeach; ?>

	</ul>

<?php elseif ( $display_empty === 1 ) : ?>

	<?php echo $display_option_none; ?>

<?php endif; ?>

<?php echo $content_after; ?>

<?php if ( ! ( $display_empty === 0 && $count === 0 ) && $container !== '' ) : ?>
	</<?php echo $container; ?>>
<?php endif; ?>