<?php
/** Ein frétt í lista (forsíða, flokkar, leit). */
$has_img = (bool) hk_image_url();
?>
<a class="item<?php echo $has_img ? '' : ' noimg'; ?>" data-c="<?php echo esc_attr( hk_filter_keys() ); ?>" href="<?php the_permalink(); ?>">
	<?php hk_image(); ?>
	<div>
		<?php hk_the_tag(); ?>
		<h3 class="h"><?php the_title(); ?></h3>
		<div class="meta"><?php echo hk_time(); // phpcs:ignore ?></div>
	</div>
</a>
