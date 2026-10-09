<?php
/**
 * Stöðutöflur – allar fjórar deildirnar, gögn frá HSÍ.
 */

get_header();
$leagues = hk_leagues();
?>

<main id="efni" class="wrap">
	<div class="page-head-row">
		<h1 class="h page-title">Stöðutöflur</h1>
		<?php get_template_part( 'template-parts/league-tabs' ); ?>
	</div>

	<?php $i = 0; foreach ( $leagues as $key => $L ) :
		$rows    = hk_table( $key );
		$count   = count( $rows );
		$matches = hk_league( $key );
		$played  = array_values( array_filter( $matches, fn( $m ) => 'done' === $m['state'] ) );
		$coming  = array_values( array_filter( $matches, fn( $m ) => in_array( $m['state'], [ 'upcoming', 'live' ], true ) ) );
		$last_r  = $played ? end( $played )['round'] : 0;
		$next_r  = $coming ? $coming[0]['round'] : 0;
		?>
		<section class="league-panel split" data-panel="<?php echo esc_attr( $key ); ?>"<?php echo $i++ ? ' hidden' : ''; ?>>
			<div>
				<div class="sec-head">
					<h2 class="h"><?php echo esc_html( $L['name'] ); ?></h2>
					<span class="meta"><?php echo $last_r ? esc_html( $last_r . '. umferð af ' . ( 2 * ( $count - 1 ) ) ) : ''; ?></span>
				</div>

				<?php if ( $rows ) : ?>
				<div class="table-scroll">
					<table class="standings">
						<thead>
							<tr>
								<th>#</th><th>Lið</th><th title="Leikir">L</th><th title="Sigrar">S</th><th title="Jafntefli">J</th><th title="Töp">T</th>
								<th class="hide-s">Mörk</th><th>+/−</th><th>Stig</th><th class="hide-s form-h">Síðustu 5</th>
							</tr>
						</thead>
						<tbody>
						<?php foreach ( $rows as $n => $r ) : $zone = hk_zone( $key, $n + 1, $count ); ?>
							<tr class="<?php echo esc_attr( $zone ? 'z-' . $zone : '' ); ?>">
								<td><?php echo (int) $n + 1; ?></td>
								<td class="t"><?php echo hk_crest( $r['team'], $r['id'] ); // phpcs:ignore ?><span><?php echo esc_html( $r['team'] ); ?></span></td>
								<td><?php echo (int) $r['p']; ?></td>
								<td><?php echo (int) $r['w']; ?></td>
								<td><?php echo (int) $r['d']; ?></td>
								<td><?php echo (int) $r['l']; ?></td>
								<td class="hide-s"><?php echo (int) $r['gf']; ?>:<?php echo (int) $r['ga']; ?></td>
								<td><?php echo esc_html( hk_signed( $r['gf'] - $r['ga'] ) ); ?></td>
								<td class="pts"><?php echo (int) $r['pts']; ?></td>
								<td class="hide-s form">
									<?php foreach ( $r['form'] as $f ) : ?><i class="f-<?php echo esc_attr( $f ); ?>" title="<?php echo esc_attr( [ 'S' => 'Sigur', 'J' => 'Jafntefli', 'T' => 'Tap' ][ $f ] ); ?>"><?php echo esc_html( $f ); ?></i><?php endforeach; ?>
								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<ul class="legend">
					<?php foreach ( hk_zone_labels( $key ) as $z => $label ) : ?>
						<li><i class="z-<?php echo esc_attr( $z ); ?>"></i><?php echo esc_html( $label ); ?></li>
					<?php endforeach; ?>
					<li class="legend-src">Heimild: HSÍ</li>
				</ul>
				<?php else : ?>
					<p class="empty">Ekki tókst að sækja stöðuna frá HSÍ. Reyndu aftur eftir smá stund.</p>
				<?php endif; ?>

				<?php hk_ad( 'feed-1' ); ?>

				<?php if ( $last_r ) : ?>
				<div class="sec-head"><h2 class="h"><?php echo (int) $last_r; ?>. umferð – úrslit</h2></div>
				<div class="matches">
					<?php foreach ( array_filter( $played, fn( $m ) => $m['round'] === $last_r ) as $m ) { get_template_part( 'template-parts/match', null, [ 'm' => $m ] ); } ?>
				</div>
				<?php endif; ?>
			</div>

			<aside class="side">
				<?php if ( $next_r ) : ?>
				<div>
					<div class="sec-head"><h2 class="h"><?php echo (int) $next_r; ?>. umferð</h2></div>
					<ul class="fixtures">
						<?php foreach ( array_filter( $coming, fn( $m ) => $m['round'] === $next_r ) as $m ) : ?>
							<li>
								<span class="fx-when"><?php echo esc_html( hk_kickoff( $m['ts'] ) ); ?></span>
								<span class="fx-teams"><?php echo esc_html( $m['home'] . ' – ' . $m['away'] ); ?></span>
								<span class="fx-venue"><?php echo esc_html( $m['venue'] ); ?><?php echo $m['tv'] ? ' · ' . esc_html( $m['tv'] ) : ''; ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
				<?php endif; ?>
				<?php hk_ad( 'side' ); ?>
				<div class="sticky"><?php hk_ad( 'sticky' ); ?></div>
			</aside>
		</section>
	<?php endforeach; ?>
</main>

<?php
get_footer();
