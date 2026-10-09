<?php
/** Deildahnappar. $args['all'] = bæta við „Allar deildir“. */
?>
<div class="league-tabs" role="group" aria-label="Deild">
	<?php if ( ! empty( $args['all'] ) ) : ?>
		<button type="button" data-league="all" aria-pressed="true">Allar deildir</button>
	<?php endif; ?>
	<?php $first = empty( $args['all'] ); foreach ( hk_leagues() as $key => $L ) : ?>
		<button type="button" data-league="<?php echo esc_attr( $key ); ?>" aria-pressed="<?php echo $first ? 'true' : 'false'; ?>"><?php echo esc_html( $L['short'] ); ?></button>
	<?php $first = false; endforeach; ?>
</div>
