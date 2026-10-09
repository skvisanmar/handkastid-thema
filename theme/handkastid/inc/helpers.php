<?php
/**
 * Hjálparföll sem sniðmátin nota.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Slóð á mynd fréttar. Notar einkennismynd (featured image) ef hún er til,
 * annars `_hk_image_url` (notað á prufusíðunni þar sem myndir eru sóttar af handkastid.net).
 */
function hk_image_url( $post = null, $size = 'hk-card' ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}
	if ( has_post_thumbnail( $post ) ) {
		return (string) get_the_post_thumbnail_url( $post, $size );
	}
	return (string) get_post_meta( $post->ID, '_hk_image_url', true );
}

/** Prentar myndareit sem bakgrunnsmynd, eða ekkert ef fréttin er myndlaus. */
function hk_image( $post = null, $size = 'hk-card', $class = 'img' ) {
	$url = hk_image_url( $post, $size );
	if ( ! $url ) {
		return;
	}
	printf(
		'<div class="%s"><img src="%s" alt="" loading="lazy" decoding="async"></div>',
		esc_attr( $class ),
		esc_url( $url )
	);
}

/**
 * Stutt, lesendavænt heiti á flokki fréttar. Langir flokkanöfn eins og
 * „Íslenskar fréttir - Karla“ verða „Olís-deild karla“ o.s.frv.
 */
function hk_label( $post = null ) {
	$map = [
		'strakarnir-okkar'        => [ 'Strákarnir okkar', true ],
		'stelpurnar-okkar'        => [ 'Stelpurnar okkar', true ],
		'handkastid'              => [ 'Hlaðvarp', true ],
		'bakhlidin'               => [ 'Bakhliðin', false ],
		'yngri-landslid'          => [ 'Yngri landslið', false ],
		'islenskar-frettir-karla' => [ 'Íslenski boltinn – karlar', false ],
		'islenskar-frettir-kvenna'=> [ 'Íslenski boltinn – konur', false ],
		'erlendar-frettir-karla'  => [ 'Erlent – karlar', false ],
		'erlendar-frettir-kvenna' => [ 'Erlent – konur', false ],
	];
	$slugs = wp_list_pluck( get_the_category( get_post( $post ) ), 'slug' );
	foreach ( $map as $slug => [ $name, $red ] ) {
		if ( in_array( $slug, $slugs, true ) ) {
			return [ 'name' => $name, 'red' => $red, 'url' => get_category_link( get_category_by_slug( $slug ) ) ];
		}
	}
	return null;
}

function hk_the_tag( $post = null ) {
	$l = hk_label( $post );
	if ( $l ) {
		printf( '<span class="tag%s">%s</span>', $l['red'] ? ' red' : '', esc_html( $l['name'] ) );
	}
}

/** Lyklar fyrir síuhnappana í „Nýjustu fréttir“: is, er, kv, ka. */
function hk_filter_keys( $post = null ) {
	$keys = [];
	foreach ( wp_list_pluck( get_the_category( get_post( $post ) ), 'slug' ) as $s ) {
		if ( str_starts_with( $s, 'islenskar' ) ) { $keys[] = 'is'; }
		if ( str_starts_with( $s, 'erlendar' ) )  { $keys[] = 'er'; }
		if ( str_ends_with( $s, 'kvenna' ) || 'stelpurnar-okkar' === $s ) { $keys[] = 'kv'; }
		if ( str_ends_with( $s, 'karla' ) || 'strakarnir-okkar' === $s )  { $keys[] = 'ka'; }
	}
	return implode( ' ', array_unique( $keys ) );
}

/** „Í dag 14:05“, „Í gær 21:30“ eða „7. október · 22:14“. */
function hk_time( $post = null ) {
	$ts    = get_post_timestamp( $post );
	$today = wp_date( 'Y-m-d' );
	$day   = wp_date( 'Y-m-d', $ts );
	$time  = wp_date( 'H:i', $ts );
	if ( $day === $today ) {
		$when = 'Í dag ' . $time;
	} elseif ( $day === wp_date( 'Y-m-d', strtotime( '-1 day' ) ) ) {
		$when = 'Í gær ' . $time;
	} else {
		$when = wp_date( 'j. F', $ts ) . ' · ' . $time;
	}
	return sprintf( '<time datetime="%s">%s</time>', esc_attr( get_the_date( 'c', $post ) ), esc_html( $when ) );
}

function hk_initials( $name ) {
	$parts = preg_split( '/\s+/u', trim( $name ) );
	$first = mb_substr( $parts[0] ?? '', 0, 1 );
	$last  = count( $parts ) > 1 ? mb_substr( end( $parts ), 0, 1 ) : '';
	return mb_strtoupper( $first . $last );
}

/**
 * Mest lesið. Notar flokkinn „Mest lesið“ sem ritstjórn stýrir í dag.
 * Ef hann er tómur er notast við flettingar úr WP-PostViews síðustu 7 daga.
 */
function hk_most_read( $count = 5 ) {
	$q = new WP_Query( [
		'category_name'       => 'mest-lesid',
		'posts_per_page'      => $count,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	] );
	if ( $q->have_posts() ) {
		return $q;
	}
	return new WP_Query( [
		'posts_per_page'      => $count,
		'meta_key'            => 'views',
		'orderby'             => 'meta_value_num',
		'date_query'          => [ [ 'after' => '7 days ago' ] ],
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	] );
}

/** Nær í Spotify-slóð þáttar úr innfelldum spilara í fréttinni. */
function hk_spotify_url( $post = null ) {
	$content = get_post_field( 'post_content', get_post( $post ) );
	if ( preg_match( '#open\.spotify\.com/(?:embed/)?(episode|show)/([A-Za-z0-9]+)#', $content, $m ) ) {
		return 'https://open.spotify.com/' . $m[1] . '/' . $m[2];
	}
	return '';
}

/** Bakhliðin: „Bakhliðin: Nafn“ → „Nafn“. */
function hk_bak_name( $post = null ) {
	return trim( preg_replace( '/^Bakhliðin:\s*/u', '', get_the_title( $post ) ) );
}

/** Bakhliðin: sækir t.d. gælunafn og aldur úr föstu spurningunum. */
function hk_bak_facts( $post = null ) {
	$text  = wp_strip_all_tags( str_replace( [ '<br>', '<br />', '&nbsp;' ], [ "\n", "\n", ' ' ], get_post_field( 'post_content', get_post( $post ) ) ) );
	$facts = [];
	foreach ( [ 'Gælunafn', 'Aldur' ] as $key ) {
		if ( preg_match( '/' . $key . ':\s*([^\n]+)/u', $text, $m ) ) {
			$facts[ $key ] = trim( $m[1] );
		}
	}
	return $facts;
}

/** Valmynd úr WordPress ef hún er skilgreind, annars sjálfgefnir tenglar. */
function hk_menu( $location, $fallback ) {
	if ( has_nav_menu( $location ) ) {
		wp_nav_menu( [
			'theme_location' => $location,
			'container'      => false,
			'items_wrap'     => '%3$s',
			'depth'          => 1,
			'walker'         => new HK_Flat_Walker(),
		] );
		return;
	}
	foreach ( $fallback as $label => $url ) {
		printf( '<a href="%s">%s</a>', esc_url( $url ), esc_html( $label ) );
	}
}

/** Prentar valmyndartengla án <ul>/<li>. */
class HK_Flat_Walker extends Walker_Nav_Menu {
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$current = in_array( 'current-menu-item', (array) $item->classes, true ) ? ' class="on" aria-current="page"' : '';
		$output .= sprintf( '<a href="%s"%s>%s</a>', esc_url( $item->url ), $current, esc_html( $item->title ) );
	}
	public function end_el( &$output, $item, $depth = 0, $args = null ) {}
}

function hk_cat_url( $slug ) {
	$cat = get_category_by_slug( $slug );
	return $cat ? get_category_link( $cat ) : home_url( '/category/' . $slug . '/' );
}

function hk_page_url( $slug ) {
	$page = get_page_by_path( $slug );
	return $page ? get_permalink( $page ) : home_url( '/' . $slug . '/' );
}

/** Slóð á fréttayfirlitið („Allar fréttir“). */
function hk_news_url() {
	$id = (int) get_option( 'page_for_posts' );
	return $id ? get_permalink( $id ) : home_url( '/' );
}
