<?php
/**
 * Forsíða.
 *
 * Aðalfrétt: nýjasta „fasta“ fréttin (Sticky / „Festa efst“ í ritlinum),
 * annars nýjasta fréttin. Þar á eftir koma þrjár fréttir og svo straumurinn.
 */

get_header();
get_template_part( 'template-parts/ticker' );

$shown  = [];
$sticky = get_option( 'sticky_posts' );
$hero   = null;
if ( $sticky ) {
	$hero = ( new WP_Query( [ 'post__in' => $sticky, 'posts_per_page' => 1, 'ignore_sticky_posts' => true, 'no_found_rows' => true ] ) )->posts[0] ?? null;
}
$latest = new WP_Query( [
	'posts_per_page'      => $hero ? 12 : 13,
	'post__not_in'        => $hero ? [ $hero->ID ] : [],
	'ignore_sticky_posts' => true,
	'no_found_rows'       => true,
] );
$posts = $latest->posts;
if ( ! $hero ) {
	$hero = array_shift( $posts );
}
$trio = array_splice( $posts, 0, 3 );
$feed = $posts;
?>

<main id="efni" class="wrap front">

	<?php if ( $hero ) : $post = $hero; setup_postdata( $post ); ?>
	<section class="lead">
		<div>
			<a class="hero" href="<?php the_permalink(); ?>">
				<?php hk_image( null, 'hk-hero' ); ?>
				<?php hk_the_tag(); ?>
				<h1 class="h"><?php the_title(); ?></h1>
				<?php if ( has_excerpt() || get_the_content() ) : ?><p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 32 ) ); ?></p><?php endif; ?>
				<span class="meta"><?php echo hk_time(); // phpcs:ignore ?></span>
			</a>

			<?php if ( $trio ) : ?>
			<div class="trio">
				<?php foreach ( $trio as $post ) : setup_postdata( $post ); ?>
					<a class="card" href="<?php the_permalink(); ?>">
						<?php hk_image(); ?>
						<?php hk_the_tag(); ?>
						<h2 class="h"><?php the_title(); ?></h2>
					</a>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>
		</div>

		<aside class="side">
			<?php $most = hk_most_read( 5 ); if ( $most->have_posts() ) : ?>
			<div class="mostread">
				<div class="sec-head"><h2 class="h">Mest lesið</h2></div>
				<ol>
					<?php while ( $most->have_posts() ) : $most->the_post(); ?>
						<li><a href="<?php the_permalink(); ?>"><span class="h"><?php the_title(); ?></span></a></li>
					<?php endwhile; ?>
				</ol>
			</div>
			<?php endif; ?>
			<?php hk_ad( 'side' ); ?>
		</aside>
	</section>
	<?php wp_reset_postdata(); endif; ?>

	<section class="split">
		<div>
			<div class="sec-head"><h2 class="h">Nýjustu fréttir</h2><a href="<?php echo esc_url( hk_news_url() ); ?>">Allar fréttir →</a></div>
			<div class="filters" role="group" aria-label="Sía fréttir">
				<button type="button" aria-pressed="true" data-f="all">Allt</button>
				<button type="button" aria-pressed="false" data-f="is">Ísland</button>
				<button type="button" aria-pressed="false" data-f="er">Erlent</button>
				<button type="button" aria-pressed="false" data-f="kv">Konur</button>
				<button type="button" aria-pressed="false" data-f="ka">Karlar</button>
			</div>
			<div class="feed" id="feed">
				<?php
				foreach ( $feed as $i => $post ) {
					setup_postdata( $post );
					get_template_part( 'template-parts/item' );
					if ( 2 === $i ) { hk_ad( 'feed-1' ); }
					if ( 5 === $i ) { hk_ad( 'feed-2' ); }
				}
				wp_reset_postdata();
				?>
			</div>
			<a class="more" href="<?php echo esc_url( hk_news_url() ); ?>">Sýna fleiri fréttir</a>
		</div>

		<aside class="side">
			<?php get_template_part( 'template-parts/sidebar-tables' ); ?>
			<div class="sticky"><?php hk_ad( 'sticky' ); ?></div>
		</aside>
	</section>

	<?php hk_ad( 'bill' ); ?>

	<?php $bak = new WP_Query( [ 'category_name' => 'bakhlidin', 'posts_per_page' => 6, 'ignore_sticky_posts' => true, 'no_found_rows' => true ] ); ?>
	<?php if ( $bak->have_posts() ) : ?>
	<section>
		<div class="sec-head">
			<h2 class="h">Bakhliðin <?php hk_sponsor( 'bak' ); ?></h2>
			<a href="<?php echo esc_url( hk_cat_url( 'bakhlidin' ) ); ?>">Allir viðmælendur →</a>
		</div>
		<div class="backs">
			<?php while ( $bak->have_posts() ) : $bak->the_post();
				$name  = hk_bak_name();
				$facts = hk_bak_facts();
				$img   = hk_image_url();
				?>
				<a class="back" href="<?php the_permalink(); ?>">
					<?php if ( $img ) : ?>
						<img class="face" src="<?php echo esc_url( $img ); ?>" alt="" loading="lazy">
					<?php else : ?>
						<span class="mono"><?php echo esc_html( hk_initials( $name ) ); ?></span>
					<?php endif; ?>
					<h3 class="h"><?php echo esc_html( $name ); ?></h3>
					<?php if ( $facts ) : ?>
						<dl>
							<?php foreach ( $facts as $k => $v ) : ?><div><dt><?php echo esc_html( $k ); ?></dt><dd><?php echo esc_html( $v ); ?></dd></div><?php endforeach; ?>
						</dl>
					<?php endif; ?>
				</a>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>
	</section>
	<?php endif; ?>

	<?php $pod = new WP_Query( [ 'category_name' => 'handkastid', 'posts_per_page' => 1, 'ignore_sticky_posts' => true, 'no_found_rows' => true ] ); ?>
	<?php if ( $pod->have_posts() ) : $pod->the_post();
		$episode = hk_spotify_url();
		?>
	<section class="pod" id="hladvarp">
		<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/logo.jpg' ); ?>" alt="">
		<div>
			<span class="label pod-kicker">Nýjasti þátturinn · Hlaðvarpið síðan 2017</span>
			<h2 class="h"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 30 ) ); ?></p>
			<?php hk_sponsor( 'pod' ); ?>
		</div>
		<div class="btns">
			<a class="btn" href="<?php echo esc_url( $episode ?: get_permalink() ); ?>"<?php echo $episode ? ' target="_blank" rel="noopener"' : ''; ?>>▶ Hlusta</a>
			<?php if ( get_theme_mod( 'hk_spotify' ) ) : ?><a class="btn ghost" href="<?php echo esc_url( get_theme_mod( 'hk_spotify' ) ); ?>" target="_blank" rel="noopener">Spotify</a><?php endif; ?>
			<?php if ( get_theme_mod( 'hk_apple' ) ) : ?><a class="btn ghost" href="<?php echo esc_url( get_theme_mod( 'hk_apple' ) ); ?>" target="_blank" rel="noopener">Apple</a><?php endif; ?>
			<a class="btn ghost" href="<?php echo esc_url( hk_cat_url( 'handkastid' ) ); ?>">Allir þættir</a>
		</div>
	</section>
	<?php wp_reset_postdata(); endif; ?>

</main>

<?php
get_footer();
