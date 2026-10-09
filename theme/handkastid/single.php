<?php
/**
 * Stök frétt.
 */

get_header();
the_post();
$label = hk_label();
?>

<main id="efni" class="wrap">
	<article <?php post_class( 'article' ); ?>>
		<header class="article-head">
			<?php hk_the_tag(); ?>
			<h1 class="h"><?php the_title(); ?></h1>
			<div class="meta byline">
				<span><?php the_author(); ?></span>
				<span><?php echo hk_time(); // phpcs:ignore ?></span>
			</div>
		</header>

		<?php if ( hk_image_url( null, 'hk-hero' ) ) : ?>
			<figure class="article-img">
				<?php hk_image( null, 'hk-hero', 'img wide' ); ?>
				<?php if ( has_post_thumbnail() && get_the_post_thumbnail_caption() ) : ?>
					<figcaption><?php the_post_thumbnail_caption(); ?></figcaption>
				<?php endif; ?>
			</figure>
		<?php endif; ?>

		<div class="article-grid">
			<div class="prose">
				<?php the_content(); ?>
				<div class="share">
					<span class="label">Deila</span>
					<a href="<?php echo esc_url( 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode( get_permalink() ) ); ?>" target="_blank" rel="noopener">Facebook</a>
					<a href="<?php echo esc_url( 'https://twitter.com/intent/tweet?url=' . rawurlencode( get_permalink() ) . '&text=' . rawurlencode( get_the_title() ) ); ?>" target="_blank" rel="noopener">X</a>
					<button type="button" class="copy-link" data-url="<?php the_permalink(); ?>">Afrita tengil</button>
				</div>
			</div>
			<aside class="side">
				<?php hk_ad( 'side' ); ?>
				<?php $most = hk_most_read( 5 ); if ( $most->have_posts() ) : ?>
				<div class="mostread">
					<div class="sec-head"><h2 class="h">Mest lesið</h2></div>
					<ol>
						<?php while ( $most->have_posts() ) : $most->the_post(); ?>
							<li><a href="<?php the_permalink(); ?>"><span class="h"><?php the_title(); ?></span></a></li>
						<?php endwhile; wp_reset_postdata(); ?>
					</ol>
				</div>
				<?php endif; ?>
				<div class="sticky"><?php hk_ad( 'sticky' ); ?></div>
			</aside>
		</div>
	</article>

	<?php
	$cats    = wp_get_post_categories( get_the_ID() );
	$related = new WP_Query( [
		'category__in'        => $cats,
		'post__not_in'        => [ get_the_ID() ],
		'posts_per_page'      => 3,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	] );
	if ( $related->have_posts() ) :
		?>
		<section class="related">
			<div class="sec-head"><h2 class="h">Fleiri fréttir<?php echo $label ? ' – ' . esc_html( $label['name'] ) : ''; ?></h2></div>
			<div class="trio">
				<?php while ( $related->have_posts() ) : $related->the_post(); ?>
					<a class="card" href="<?php the_permalink(); ?>">
						<?php hk_image(); ?>
						<?php hk_the_tag(); ?>
						<h3 class="h"><?php the_title(); ?></h3>
					</a>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>
		</section>
	<?php endif; ?>

	<?php hk_ad( 'bill' ); ?>
</main>

<?php
get_footer();
