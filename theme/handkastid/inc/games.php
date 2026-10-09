<?php
/**
 * Leikjavaktin og stöðutöflur.
 *
 * Gagnaveitan er ekki enn tengd. Þangað til skila föllin sýnidæmum sem eru
 * merkt „Sýnidæmi“ á síðunni. Til að tengja raunveruleg gögn (t.d. frá HSÍ)
 * er nóg að hengja sig á síurnar `hk_games` og `hk_standings`.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Hver leikur: comp, home, away, hs, as (mörk eða null), ht (hálfleikur),
 * state: 'done' | 'live' | 'upcoming', note (t.d. „34. mín“ eða „Í kvöld 19:30“).
 */
function hk_games() {
	$sample = [
		[ 'comp' => 'Powerade-bikar karla', 'home' => 'HK', 'away' => 'Afturelding', 'hs' => 27, 'as' => 33, 'ht' => '13–16', 'state' => 'done' ],
		[ 'comp' => 'Powerade-bikar karla', 'home' => 'Haukar', 'away' => 'Stjarnan', 'hs' => 31, 'as' => 24, 'ht' => '15–11', 'state' => 'done' ],
		[ 'comp' => 'Grill 66-deild kvenna', 'home' => 'Víkingur', 'away' => 'Afturelding', 'hs' => 22, 'as' => 29, 'ht' => '10–14', 'state' => 'done' ],
		[ 'comp' => 'Olís-deild kvenna', 'home' => 'Valur', 'away' => 'Fram', 'hs' => 14, 'as' => 12, 'state' => 'live', 'note' => '34. mín' ],
		[ 'comp' => 'Olís-deild karla', 'home' => 'FH', 'away' => 'ÍBV', 'hs' => null, 'as' => null, 'state' => 'upcoming', 'note' => 'Í kvöld 19:30' ],
	];
	$games = apply_filters( 'hk_games', null );
	return null === $games ? [ 'sample' => true, 'games' => $sample ] : [ 'sample' => false, 'games' => $games ];
}

/** Röð: [lið, leikir, markamunur, stig]. `cut` = síðasta sæti inn í úrslitakeppni. */
function hk_standings() {
	$sample = [
		'Olís karla'  => [ 'cut' => 8, 'rows' => [
			[ 'Haukar', 5, 31, 10 ], [ 'FH', 5, 18, 8 ], [ 'Valur', 5, 15, 8 ], [ 'Afturelding', 5, 9, 7 ], [ 'Fram', 5, 4, 6 ],
			[ 'KA', 5, 1, 5 ], [ 'ÍBV', 5, -2, 5 ], [ 'Stjarnan', 5, -6, 4 ], [ 'HK', 5, -10, 3 ], [ 'Selfoss', 5, -24, 0 ],
		] ],
		'Olís kvenna' => [ 'cut' => 6, 'rows' => [
			[ 'Valur', 4, 38, 8 ], [ 'Haukar', 4, 21, 6 ], [ 'Fram', 4, 12, 6 ], [ 'ÍBV', 4, 3, 4 ], [ 'Stjarnan', 4, -2, 4 ],
			[ 'Grótta', 4, -9, 2 ], [ 'KA/Þór', 4, -25, 2 ], [ 'Selfoss', 4, -38, 0 ],
		] ],
	];
	$data = apply_filters( 'hk_standings', null );
	return null === $data ? [ 'sample' => true, 'tables' => $sample ] : [ 'sample' => false, 'tables' => $data ];
}

function hk_upcoming() {
	$sample = [
		[ 'FH – ÍBV', 'Í kvöld 19:30' ],
		[ 'Valur – KA', 'Lau 16:00' ],
		[ 'Selfoss – Fram', 'Lau 18:00' ],
	];
	$data = apply_filters( 'hk_upcoming', null );
	return null === $data ? [ 'sample' => true, 'games' => $sample ] : [ 'sample' => false, 'games' => $data ];
}

function hk_signed( $n ) {
	return $n > 0 ? '+' . $n : ( $n < 0 ? '−' . abs( $n ) : '0' );
}
