<?php
/**
 * Leikjalisti flokkaður eftir dögum, með deildahnöppum.
 * $args: title, mode ('results' | 'fixtures' | 'today'), days.
 */
$mode = $args['mode'];
$days = $args['days'] ?? 21;
$all  = hk_all_matches();
$now  = time();

if ( 'results' === $mode ) {
	$list = array_filter( $all, fn( $m ) => 'done' === $m['state'] && $m['ts'] > $now - $days * DAY_IN_SECONDS );
	$list = array_reverse( $list );
} elseif ( 'fixtures' === $mode ) {
	$list = array_filter( $all, fn( $m ) => in_array( $m['state'], [ 'upcoming', 'live' ], true ) && $m['ts'] < $now + $days * DAY_IN_SECONDS );
} else {
	$from = strtotime( wp_date( 'Y-m-d 00:00:00', strtotime( '-1 day' ) ) . ' ' . wp_timezone_string() );
	$list = array_filter( $all, fn( $m ) => $m['ts'] >= $from && $m['ts'] < $from + 3 * DAY_IN_SECONDS );
}
$groups = hk_group_by_day( $list );
?>

<main id="efni" class="wrap">
	<div class="page-head-row">
		<h1 class="h page-title"><?php echo esc_html( $args['title'] ); ?></h1>
		<?php get_template_part( 'template-parts/league-tabs', null, [ 'all' => true ] ); ?>
	</div>

	<section class="split">
		<div class="match-days">
			<?php if ( ! $groups ) : ?>
				<p class="empty">Engir leikir á þessu tímabili.</p>
			<?php endif; ?>
			<?php $n = 0; foreach ( $groups as $day => $matches ) : ?>
				<div class="day">
					<h2 class="day-head"><?php echo esc_html( hk_day_heading( $matches[0]['ts'] ) ); ?></h2>
					<div class="matches">
						<?php foreach ( $matches as $m ) { get_template_part( 'template-parts/match', null, [ 'm' => $m, 'show_league' => true ] ); } ?>
					</div>
				</div>
				<?php if ( 1 === $n++ ) { hk_ad( 'feed-1' ); } ?>
			<?php endforeach; ?>
			<p class="note">Heimild: HSÍ. Gögnin uppfærast á 10 mínútna fresti.</p>
		</div>
		<aside class="side">
			<?php hk_ad( 'side' ); ?>
			<?php get_template_part( 'template-parts/sidebar-tables' ); ?>
			<div class="sticky"><?php hk_ad( 'sticky' ); ?></div>
		</aside>
	</section>
</main>
