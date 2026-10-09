<?php
$data = hk_games();
if ( ! $data['games'] ) {
	return;
}
$leagues = hk_leagues();
?>
<section class="ticker" aria-label="Leikjavaktin">
	<div class="wrap">
		<div class="ticker-head">
			<span class="h">Leikjavaktin</span>
			<?php hk_sponsor( 'games' ); ?>
		</div>
		<?php foreach ( $data['games'] as $g ) :
			$done = 'done' === $g['state'];
			?>
			<div class="game">
				<span class="comp"><?php echo esc_html( $leagues[ $g['league'] ]['short'] ); ?></span>
				<div class="row<?php echo $done && $g['hs'] < $g['as'] ? ' lose' : ''; ?>"><span><?php echo esc_html( $g['home'] ); ?></span><span class="s"><?php echo null === $g['hs'] ? '–' : (int) $g['hs']; ?></span></div>
				<div class="row<?php echo $done && $g['as'] < $g['hs'] ? ' lose' : ''; ?>"><span><?php echo esc_html( $g['away'] ); ?></span><span class="s"><?php echo null === $g['as'] ? '–' : (int) $g['as']; ?></span></div>
				<?php if ( 'live' === $g['state'] ) : ?>
					<span class="st now"><span class="live label">Í gangi</span></span>
				<?php elseif ( 'pending' === $g['state'] ) : ?>
					<span class="st">Úrslit ókomin</span>
				<?php elseif ( $done ) : ?>
					<span class="st"><?php echo esc_html( hk_dayname( $g['ts'], true ) ); ?> · Lokið<?php echo null !== $g['hhs'] ? ' (' . (int) $g['hhs'] . '–' . (int) $g['has'] . ')' : ''; ?></span>
				<?php else : ?>
					<span class="st"><?php echo esc_html( hk_kickoff( $g['ts'] ) ); ?></span>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
		<a class="all" href="<?php echo esc_url( hk_page_url( 'leikir-og-urslit-dagsins' ) ); ?>">Allir leikir →</a>
	</div>
</section>
