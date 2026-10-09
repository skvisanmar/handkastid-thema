<?php
/**
 * Fréttayfirlit, flokkar, leit og 404.
 */

get_header();

if ( is_search() ) {
	$heading = sprintf( 'Leit: %s', get_search_query() );
} elseif ( is_category() ) {
	$heading = single_cat_title( '', false );
} elseif ( is_archive() ) {
	$heading = wp_strip_all_tags( get_the_archive_title() );
} elseif ( is_404() ) {
	$heading = 'Síðan fannst ekki';
} else {
	$heading = 'Fréttir';
}
?>

<main id="efni" class="wrap">
	<section class="split">
		<div>
			<div class="sec-head page-head"><h1 class="h"><?php echo esc_html( $heading ); ?></h1></div>
			<?php if ( is_category() && category_description() ) : ?>
				<div class="intro"><?php echo wp_kses_post( category_description() ); ?></div>
			<?php endif; ?>

			<?php if ( have_posts() ) : ?>
				<div class="feed">
					<?php
					$i = 0;
					while ( have_posts() ) {
						the_post();
						get_template_part( 'template-parts/item' );
						if ( 4 === $i++ ) { hk_ad( 'feed-1' ); }
					}
					?>
				</div>
				<nav class="pager" aria-label="Síður">
					<?php
					echo paginate_links( [ // phpcs:ignore
						'prev_text' => '← Nýrri',
						'next_text' => 'Eldri →',
						'mid_size'  => 1,
					] );
					?>
				</nav>
			<?php else : ?>
				<p class="empty"><?php echo is_search() ? 'Ekkert fannst. Prófaðu annað leitarorð.' : 'Hér er ekkert að sjá. Farðu á forsíðuna til að sjá nýjustu fréttirnar.'; ?></p>
				<?php get_search_form(); ?>
			<?php endif; ?>
		</div>

		<aside class="side">
			<?php hk_ad( 'side' ); ?>
			<?php get_template_part( 'template-parts/sidebar-tables' ); ?>
			<div class="sticky"><?php hk_ad( 'sticky' ); ?></div>
		</aside>
	</section>
</main>

<?php
get_footer();
