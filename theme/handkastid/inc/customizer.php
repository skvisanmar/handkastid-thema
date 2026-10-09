<?php
/**
 * Stillingar sem stjórnendur breyta sjálfir í Útlit → Sérsníða → Handkastið.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'customize_register', function ( WP_Customize_Manager $c ) {
	$c->add_section( 'hk', [ 'title' => 'Handkastið', 'priority' => 30 ] );

	$fields = [
		'hk_spotify'              => [ 'Spotify-slóð hlaðvarpsins', 'url' ],
		'hk_apple'                => [ 'Apple Podcasts-slóð', 'url' ],
		'hk_instagram'            => [ 'Instagram', 'url' ],
		'hk_facebook'             => [ 'Facebook', 'url' ],
		'hk_x'                    => [ 'X', 'url' ],
		'hk_sponsor_games'        => [ 'Kostunaraðili Leikjavaktarinnar', 'text' ],
		'hk_sponsor_games_url'    => [ 'Vefslóð kostunaraðila Leikjavaktarinnar', 'url' ],
		'hk_sponsor_bak'          => [ 'Kostunaraðili Bakhliðarinnar', 'text' ],
		'hk_sponsor_bak_url'      => [ 'Vefslóð kostunaraðila Bakhliðarinnar', 'url' ],
		'hk_sponsor_pod'          => [ 'Kostunaraðili hlaðvarpsins', 'text' ],
		'hk_sponsor_pod_url'      => [ 'Vefslóð kostunaraðila hlaðvarpsins', 'url' ],
	];
	foreach ( $fields as $id => [ $label, $type ] ) {
		$c->add_setting( $id, [ 'default' => '', 'sanitize_callback' => 'url' === $type ? 'esc_url_raw' : 'sanitize_text_field' ] );
		$c->add_control( $id, [ 'section' => 'hk', 'label' => $label, 'type' => $type ] );
	}

	$c->add_setting( 'hk_show_placeholders', [ 'default' => true, 'sanitize_callback' => 'wp_validate_boolean' ] );
	$c->add_control( 'hk_show_placeholders', [
		'section'     => 'hk',
		'label'       => 'Sýna auð auglýsingapláss',
		'description' => 'Sýnir merkta reiti þar sem engin auglýsing er skráð. Slökkvið á þessu áður en síðan fer í loftið.',
		'type'        => 'checkbox',
	] );
} );
