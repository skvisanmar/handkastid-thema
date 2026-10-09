<?php
/**
 * Fyllir staðbundnu prufusíðuna með fréttum úr seed.json (sóttar af handkastid.net).
 * Myndir eru ekki afritaðar heldur vísað í þær á handkastid.net í gegnum `_hk_image_url`.
 */

if ( get_option( 'hk_seeded' ) ) {
	return;
}

$data = json_decode( file_get_contents( __DIR__ . '/seed.json' ), true );

// Flokkar
foreach ( $data['categories'] as $c ) {
	if ( ! term_exists( $c['slug'], 'category' ) ) {
		wp_insert_term( $c['name'], 'category', [ 'slug' => $c['slug'] ] );
	}
}

// Höfundar
$authors = [];
$author_id = function ( $name ) use ( &$authors ) {
	if ( isset( $authors[ $name ] ) ) {
		return $authors[ $name ];
	}
	$login = sanitize_user( remove_accents( strtolower( str_replace( ' ', '.', $name ) ) ), true ) ?: 'hofundur';
	$user  = get_user_by( 'login', $login );
	$id    = $user ? $user->ID : wp_insert_user( [
		'user_login'   => $login,
		'user_pass'    => wp_generate_password(),
		'display_name' => $name,
		'role'         => 'author',
	] );
	return $authors[ $name ] = is_wp_error( $id ) ? 1 : $id;
};

// Fréttir
foreach ( $data['posts'] as $p ) {
	$cat_ids = [];
	foreach ( $p['categories'] as $slug ) {
		$t = get_category_by_slug( $slug );
		if ( $t ) { $cat_ids[] = $t->term_id; }
	}
	$id = wp_insert_post( [
		'post_title'    => $p['title'],
		'post_name'     => $p['slug'],
		'post_content'  => $p['content'],
		'post_excerpt'  => '',
		'post_status'   => 'publish',
		'post_date'     => str_replace( 'T', ' ', $p['date'] ),
		'post_author'   => $author_id( $p['author'] ),
		'post_category' => $cat_ids,
	], true );
	if ( ! is_wp_error( $id ) && $p['image'] ) {
		update_post_meta( $id, '_hk_image_url', esc_url_raw( $p['image'] ) );
	}
}

// Síður
$pages = [
	'forsida'                  => [ 'Forsíða', '' ],
	'frettir'                  => [ 'Fréttir', '' ],
	'um-handkastid'            => [ 'Um Handkastið', '<p>Handkastið var stofnað sem hlaðvarpsþáttur um íslenskan handbolta sumarið 2017.</p>' ],
	'stodutoflur'              => [ 'Stöðutöflur', '<p>Hér koma stöðutöflur allra deilda.</p>' ],
	'naestu-leikir'            => [ 'Næstu leikir', '<p>Hér koma næstu leikir.</p>' ],
	'nyjustu-urslit'           => [ 'Nýjustu úrslit', '<p>Hér koma nýjustu úrslit.</p>' ],
	'leikir-og-urslit-dagsins' => [ 'Leikir og úrslit dagsins', '<p>Hér koma leikir og úrslit dagsins.</p>' ],
];
$page_ids = [];
foreach ( $pages as $slug => [ $title, $content ] ) {
	$page_ids[ $slug ] = wp_insert_post( [ 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => $title, 'post_name' => $slug, 'post_content' => $content ] );
}

update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $page_ids['forsida'] );
update_option( 'page_for_posts', $page_ids['frettir'] );
update_option( 'blogname', 'Handkastið' );
update_option( 'blogdescription', 'Allt um handboltann' );
update_option( 'timezone_string', 'Atlantic/Reykjavik' );
update_option( 'date_format', 'j. F Y' );
update_option( 'time_format', 'H:i' );
update_option( 'posts_per_page', 15 );

// Varagögn frá HSÍ, notuð ef prufusíðan nær ekki sambandi við hsi.is
foreach ( glob( __DIR__ . '/hsi/*.json' ) as $file ) {
	if ( preg_match( '/(matches|standings)-(\d+)\.json$/', $file, $m ) ) {
		$json = json_decode( file_get_contents( $file ), true );
		if ( isset( $json['data'] ) ) {
			update_option( "hk_hsi_{$m[1]}_{$m[2]}_last", $json['data'], false );
		}
	}
}

// Eyða sjálfgefnu „Hello world“-færslunni og prufusíðunni
wp_delete_post( 1, true );
wp_delete_post( 2, true );

global $wp_rewrite;
$wp_rewrite->set_permalink_structure( '/%year%/%monthnum%/%day%/%postname%/' );
flush_rewrite_rules();

update_option( 'hk_seeded', 1 );
