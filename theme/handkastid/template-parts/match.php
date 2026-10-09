<?php
/**
 * Einn leikur í lista. $args['m'] = leikur, $args['show_league'] = sýna deild.
 */
$m    = $args['m'];
$done = 'done' === $m['state'];
$L    = hk_leagues()[ $m['league'] ];
?>
<div class="match is-<?php echo esc_attr( $m['state'] ); ?>" data-league="<?php echo esc_attr( $m['league'] ); ?>">
	<div class="match-when">
		<?php if ( 'live' === $m['state'] ) : ?>
			<span class="live label">Í gangi</span>
		<?php elseif ( $done ) : ?>
			<span class="label">Lokið</span>
		<?php else : ?>
			<b><?php echo esc_html( wp_date( 'H:i', $m['ts'] ) ); ?></b>
		<?php endif; ?>
	</div>
	<div class="match-teams">
		<div class="team<?php echo $done && $m['hs'] < $m['as'] ? ' lose' : ''; ?>">
			<?php echo hk_crest( $m['home'], $m['home_id'] ); // phpcs:ignore ?>
			<span class="name"><?php echo esc_html( $m['home'] ); ?></span>
			<span class="s"><?php echo null === $m['hs'] ? '' : (int) $m['hs']; ?></span>
		</div>
		<div class="team<?php echo $done && $m['as'] < $m['hs'] ? ' lose' : ''; ?>">
			<?php echo hk_crest( $m['away'], $m['away_id'] ); // phpcs:ignore ?>
			<span class="name"><?php echo esc_html( $m['away'] ); ?></span>
			<span class="s"><?php echo null === $m['as'] ? '' : (int) $m['as']; ?></span>
		</div>
	</div>
	<div class="match-info">
		<?php if ( ! empty( $args['show_league'] ) ) : ?><span class="comp"><?php echo esc_html( $L['short'] ); ?> · <?php echo (int) $m['round']; ?>. umferð</span><?php endif; ?>
		<?php if ( $done && null !== $m['hhs'] ) : ?><span>Hálfleikur <?php echo (int) $m['hhs']; ?>–<?php echo (int) $m['has']; ?></span><?php endif; ?>
		<?php if ( $m['venue'] ) : ?><span><?php echo esc_html( $m['venue'] ); ?></span><?php endif; ?>
		<?php if ( $done && $m['spectators'] ) : ?><span><?php echo esc_html( number_format_i18n( $m['spectators'] ) ); ?> áhorfendur</span><?php endif; ?>
		<?php if ( ! $done && $m['tv'] ) : ?><span class="tv"><?php echo esc_html( $m['tv'] ); ?></span><?php endif; ?>
	</div>
</div>
