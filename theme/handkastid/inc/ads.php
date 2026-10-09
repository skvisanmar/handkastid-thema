<?php
/**
 * Auglýsingapláss.
 *
 * Hvert pláss heitir t.d. `hk-top`. Ef Advanced Ads er virkt og til er
 * staðsetning (placement) með sama auðkenni birtist auglýsingin þaðan.
 * Annars birtist merktur staðgengill, en aðeins ef kveikt er á
 * „Sýna auð auglýsingapláss“ í Útlit → Sérsníða → Handkastið.
 */

defined( 'ABSPATH' ) || exit;

function hk_ad_slots() {
	return [
		'top'      => [ 'Topp-borði', '970×250 · sími 320×100', 'ad-wide' ],
		'side'     => [ 'Kassi', '300×250', 'ad-mpu' ],
		'feed-1'   => [ 'Auglýsing í fréttastraumi', '728×90 · sími 300×250', 'ad-feed' ],
		'feed-2'   => [ 'Kostað efni', 'Grein frá samstarfsaðila', 'ad-feed' ],
		'sticky'   => [ 'Hálfsíða – fylgir skruni', '300×600', 'ad-half' ],
		'bill'     => [ 'Billboard', '970×250 · sími 320×100', 'ad-wide' ],
		'article'  => [ 'Auglýsing í grein', '300×250 / 728×90', 'ad-feed' ],
	];
}

function hk_ad( $slot ) {
	$html = '';
	if ( function_exists( 'the_ad_placement' ) ) {
		ob_start();
		the_ad_placement( 'hk-' . $slot );
		$html = trim( ob_get_clean() );
	}

	if ( '' === $html ) {
		if ( ! get_theme_mod( 'hk_show_placeholders', true ) ) {
			return;
		}
		[ $name, $size, $class ] = hk_ad_slots()[ $slot ] ?? [ 'Auglýsing', '', 'ad-feed' ];
		$html = sprintf( '<div class="ad %s"><b>%s</b><span>%s</span><span>hk-%s</span></div>', esc_attr( $class ), esc_html( $name ), esc_html( $size ), esc_html( $slot ) );
	}

	printf( '<div class="ad-wrap ad-wrap-%s"><div class="ad-label">Auglýsing</div>%s</div>', esc_attr( $slot ), $html ); // phpcs:ignore WordPress.Security.EscapeOutput
}

/** Kostunarmerki („í boði …“) sem stjórnendur skrá í Sérsníða. */
function hk_sponsor( $key ) {
	$name = get_theme_mod( 'hk_sponsor_' . $key, '' );
	$url  = get_theme_mod( 'hk_sponsor_' . $key . '_url', '' );
	if ( ! $name ) {
		if ( ! get_theme_mod( 'hk_show_placeholders', true ) ) {
			return;
		}
		$name = 'Kostunaraðili';
	}
	$inner = esc_html( $name );
	if ( $url ) {
		$inner = sprintf( '<a href="%s" rel="sponsored noopener" target="_blank">%s</a>', esc_url( $url ), $inner );
	}
	printf( '<span class="sponsor">í boði <i>%s</i></span>', $inner ); // phpcs:ignore WordPress.Security.EscapeOutput
}

/** Setur auglýsingu inn í greinar á eftir 3. efnisgrein. */
add_filter( 'the_content', function ( $content ) {
	if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}
	$parts = explode( '</p>', $content );
	if ( count( $parts ) < 5 ) {
		return $content;
	}
	ob_start();
	hk_ad( 'article' );
	$ad = ob_get_clean();
	return implode( '</p>', array_slice( $parts, 0, 3 ) ) . '</p>' . $ad . implode( '</p>', array_slice( $parts, 3 ) );
}, 20 );
