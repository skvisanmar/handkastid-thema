<?php
/**
 * Síður (Um Handkastið, Stöðutöflur o.fl.).
 */

get_header();
the_post();
?>

<main id="efni" class="wrap">
	<article <?php post_class( 'article page' ); ?>>
		<header class="article-head">
			<h1 class="h"><?php the_title(); ?></h1>
		</header>
		<div class="prose"><?php the_content(); ?></div>
	</article>
</main>

<?php
get_footer();
