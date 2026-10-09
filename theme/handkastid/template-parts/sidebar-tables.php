<?php
$st = hk_standings();
$up = hk_upcoming();
?>
<div>
	<div class="sec-head"><h2 class="h">Staðan</h2><a href="<?php echo esc_url( hk_page_url( 'stodutoflur' ) ); ?>">Allar töflur →</a></div>
	<div class="tabs" role="group" aria-label="Deild">
		<?php $i = 0; foreach ( array_keys( $st['tables'] ) as $name ) : ?>
			<button type="button" data-tab="t<?php echo (int) $i; ?>" aria-pressed="<?php echo 0 === $i ? 'true' : 'false'; ?>"><?php echo esc_html( $name ); ?></button>
		<?php $i++; endforeach; ?>
	</div>
	<?php $i = 0; foreach ( $st['tables'] as $name => $table ) : ?>
		<div class="tablewrap" id="t<?php echo (int) $i; ?>"<?php echo $i ? ' hidden' : ''; ?>>
			<table>
				<thead><tr><th>#</th><th>Lið</th><th>L</th><th>Mörk</th><th>Stig</th></tr></thead>
				<tbody>
				<?php foreach ( $table['rows'] as $n => [ $team, $played, $diff, $pts ] ) : ?>
					<tr<?php echo ( $n + 1 ) === $table['cut'] ? ' class="cut"' : ''; ?>>
						<td><?php echo (int) $n + 1; ?></td><td><?php echo esc_html( $team ); ?></td><td><?php echo (int) $played; ?></td><td><?php echo esc_html( hk_signed( $diff ) ); ?></td><td><?php echo (int) $pts; ?></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php $i++; endforeach; ?>
	<p class="note">Rauða línan sýnir hvaða lið komast í úrslitakeppnina. Heimild: HSÍ.</p>
</div>

<div>
	<div class="sec-head"><h2 class="h">Næstu leikir</h2><a href="<?php echo esc_url( hk_page_url( 'naestu-leikir' ) ); ?>">Allir leikir →</a></div>
	<table class="upcoming">
		<tbody>
		<?php foreach ( $up['games'] as [ $match, $when ] ) : ?>
			<tr><td><?php echo esc_html( $match ); ?></td><td><?php echo esc_html( $when ); ?></td></tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>
