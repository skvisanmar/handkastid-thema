<?php
$data = hk_games();
if ( ! $data['games'] ) {
	return;
}
?>
<section class="ticker" aria-label="Leikjavaktin">
	<div class="wrap">
		<div class="ticker-head">
			<span class="h">Leikjavaktin</span>
			<?php hk_sponsor( 'games' ); ?>
			<?php if ( $data['sample'] ) : ?><span class="demo">Sýnidæmi</span><?php endif; ?>
		</div>
		<?php foreach ( $data['games'] as $g ) :
			$done = 'done' === $g['state'];
			$home_lost = $done && $g['hs'] < $g['as'];
			$away_lost = $done && $g['as'] < $g['hs'];
			?>
			<div class="game">
				<span class="comp"><?php echo esc_html( $g['comp'] ); ?></span>
				<div class="row<?php echo $home_lost ? ' lose' : ''; ?>"><span><?php echo esc_html( $g['home'] ); ?></span><span class="s"><?php echo null === $g['hs'] ? '–' : (int) $g['hs']; ?></span></div>
				<div class="row<?php echo $away_lost ? ' lose' : ''; ?>"><span><?php echo esc_html( $g['away'] ); ?></span><span class="s"><?php echo null === $g['as'] ? '–' : (int) $g['as']; ?></span></div>
				<?php if ( 'live' === $g['state'] ) : ?>
					<span class="st now"><span class="live label">Í beinni</span> <?php echo esc_html( $g['note'] ?? '' ); ?></span>
				<?php elseif ( $done ) : ?>
					<span class="st">Lokið<?php echo ! empty( $g['ht'] ) ? ' · (' . esc_html( $g['ht'] ) . ')' : ''; ?></span>
				<?php else : ?>
					<span class="st"><?php echo esc_html( $g['note'] ?? '' ); ?></span>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
		<a class="all" href="<?php echo esc_url( hk_page_url( 'leikir-og-urslit-dagsins' ) ); ?>">Allir leikir dagsins →</a>
	</div>
</section>
