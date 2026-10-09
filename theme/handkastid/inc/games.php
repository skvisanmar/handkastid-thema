<?php
/**
 * Deildir, leikir, úrslit og stöðutöflur – sótt frá HSÍ.
 *
 * Gögnin koma úr sama vefþjónustu og hsi.is notar sjálft:
 *   https://www.hsi.is/api/hsi/tournaments/{mót}/matches
 *   https://www.hsi.is/api/hsi/tournaments/{mót}/standings
 *
 * Hvert svar er geymt í 10 mínútur. Ef HSÍ svarar ekki (vefurinn hafnar
 * stundum of mörgum beiðnum) er síðasta góða svarið notað áfram.
 *
 * Mótanúmerin breytast á hverju tímabili. Þau eru í `hk_leagues()` og
 * má líka yfirskrifa með síunni `hk_leagues`.
 */

defined( 'ABSPATH' ) || exit;

function hk_leagues() {
	return apply_filters( 'hk_leagues', [
		'olis-karla' => [
			'tid'   => 9142,
			'name'  => 'Olís-deild karla',
			'short' => 'Olís karla',
			// Sæti 1–8 í úrslitakeppni, 11. sæti í umspil, 12. sæti fellur
			'zones' => [ 'playoff' => 8, 'playoff_down' => 1, 'down' => 1 ],
		],
		'olis-kvenna' => [
			'tid'   => 9141,
			'name'  => 'Olís-deild kvenna',
			'short' => 'Olís kvenna',
			'zones' => [ 'playoff' => 6, 'playoff_down' => 1, 'down' => 1 ],
		],
		'grill-karla' => [
			'tid'   => 9140,
			'name'  => 'Grill 66-deild karla',
			'short' => 'Grill 66 karla',
			'zones' => [ 'up' => 1, 'playoff_up' => 5 ],
		],
		'grill-kvenna' => [
			'tid'   => 9143,
			'name'  => 'Grill 66-deild kvenna',
			'short' => 'Grill 66 kvenna',
			'zones' => [ 'up' => 1, 'playoff_up' => 5 ],
		],
	] );
}

/* ---------- Sótt frá HSÍ ---------- */

function hk_hsi_get( $tid, $kind ) {
	$key  = "hk_hsi_{$kind}_{$tid}";
	$data = get_transient( $key );
	if ( false !== $data ) {
		return $data;
	}

	$res = wp_remote_get( "https://www.hsi.is/api/hsi/tournaments/{$tid}/{$kind}", [
		'timeout' => 8,
		'headers' => [ 'Accept' => 'application/json' ],
	] );
	$body = is_wp_error( $res ) ? null : json_decode( wp_remote_retrieve_body( $res ), true );

	if ( 200 === wp_remote_retrieve_response_code( $res ) && isset( $body['data'] ) ) {
		$data = $body['data'];
		set_transient( $key, $data, 10 * MINUTE_IN_SECONDS );
		update_option( "{$key}_last", $data, false );
		return $data;
	}

	// HSÍ svaraði ekki: nota síðustu góðu gögn og reyna aftur eftir 2 mínútur
	$data = get_option( "{$key}_last", [] );
	set_transient( $key, $data, 2 * MINUTE_IN_SECONDS );
	return $data;
}

/** Vallarnöfn koma stundum með aukabilum eða ólíkri stafsetningu. */
function hk_clean_venue( $name ) {
	return trim( preg_replace( '/\s+/', ' ', (string) $name ) );
}

function hk_tv_name( $code ) {
	$map = [ 'SIM' => 'Sjónvarp Símans', 'RUV' => 'RÚV', 'RÚV' => 'RÚV', 'RUV2' => 'RÚV 2', 'ST2' => 'Stöð 2 Sport' ];
	return $map[ strtoupper( trim( (string) $code ) ) ] ?? '';
}

function hk_team_logo( $team_id ) {
	return $team_id ? 'https://www.hsi.is/logo/team/' . rawurlencode( $team_id ) . '/original_small' : '';
}

/**
 * Allir leikir deildar á samræmdu sniði:
 * id, league, round, home, away, home_id, away_id, ts, hs, as, hhs, has,
 * state ('done' | 'live' | 'upcoming'), venue, tv, spectators.
 */
function hk_league( $key ) {
	static $cache = [];
	if ( isset( $cache[ $key ] ) ) {
		return $cache[ $key ];
	}
	$L   = hk_leagues()[ $key ];
	$raw = hk_hsi_get( $L['tid'], 'matches' );
	$tz  = wp_timezone();
	$now = time();
	$out = [];

	foreach ( (array) $raw as $g ) {
		if ( empty( $g['GameDayTime'] ) ) {
			continue;
		}
		$ts   = ( new DateTimeImmutable( $g['GameDayTime'], $tz ) )->getTimestamp();
		$has  = '' !== ( $g['ResultHomeTeam'] ?? '' ) && '' !== ( $g['ResultAwayTeam'] ?? '' );
		$mins = ( $now - $ts ) / 60;

		if ( $has && ( in_array( $g['Status'], [ 'S', 'U' ], true ) || $mins > 100 ) ) {
			$state = 'done';
		} elseif ( $mins >= 0 && $mins < 100 ) {
			$state = 'live';
		} elseif ( $mins >= 100 ) {
			$state = 'pending'; // leik lokið en úrslit ekki komin inn
		} else {
			$state = 'upcoming';
		}

		$int = fn( $v ) => '' === ( $v ?? '' ) ? null : (int) $v;
		$out[] = [
			'id'         => $g['GameId'],
			'league'     => $key,
			'round'      => (int) $g['Round'],
			'home'       => trim( $g['HomeTeamName'] ),
			'away'       => trim( $g['AwayTeamName'] ),
			'home_id'    => $g['HomeTeamid'] ?? '',
			'away_id'    => $g['AwayTeamId'] ?? '',
			'ts'         => $ts,
			'hs'         => $int( $g['ResultHomeTeam'] ),
			'as'         => $int( $g['ResultAwayTeam'] ),
			'hhs'        => $int( $g['ResultHomeFirstHalf'] ),
			'has'        => $int( $g['ResultAwayFirstHalf'] ),
			'state'      => $state,
			'venue'      => hk_clean_venue( $g['StadiumName'] ?? '' ),
			'tv'         => hk_tv_name( $g['TvLive'] ?? '' ),
			'spectators' => (int) ( $g['Spectators'] ?? 0 ),
		];
	}
	usort( $out, fn( $a, $b ) => $a['ts'] <=> $b['ts'] );
	return $cache[ $key ] = $out;
}

/**
 * Stöðutafla. Notar töflu HSÍ (hún tekur t.d. tillit til stigafrádráttar)
 * og bætir við síðustu fimm leikjum. Ef taflan næst ekki er hún reiknuð út frá leikjum.
 */
function hk_table( $key ) {
	$L       = hk_leagues()[ $key ];
	$matches = hk_league( $key );

	$form = [];
	foreach ( $matches as $m ) {
		if ( 'done' !== $m['state'] ) {
			continue;
		}
		$form[ $m['home'] ][] = $m['hs'] > $m['as'] ? 'S' : ( $m['hs'] === $m['as'] ? 'J' : 'T' );
		$form[ $m['away'] ][] = $m['as'] > $m['hs'] ? 'S' : ( $m['hs'] === $m['as'] ? 'J' : 'T' );
	}

	$st   = hk_hsi_get( $L['tid'], 'standings' );
	$rows = [];
	foreach ( (array) ( $st['STANDINGS']['TEAM'] ?? [] ) as $t ) {
		$name   = trim( $t['TEAM_SHORT_NAME'] );
		$rows[] = [
			'team' => $name, 'id' => $t['TEAM'],
			'p' => (int) $t['MATCHES'], 'w' => (int) $t['HOME'], 'd' => (int) $t['TIE'], 'l' => (int) $t['LOSE'],
			'gf' => (int) $t['GOALS'], 'ga' => (int) $t['GOALS_RECIEVED'], 'pts' => (int) $t['POINTS'],
			'form' => array_slice( $form[ $name ] ?? [], -5 ),
		];
	}
	if ( $rows ) {
		return $rows;
	}

	// Reiknað út frá leikjum
	$calc = [];
	foreach ( $matches as $m ) {
		foreach ( [ [ $m['home'], $m['home_id'] ], [ $m['away'], $m['away_id'] ] ] as [ $n, $id ] ) {
			$calc[ $n ] ??= [ 'team' => $n, 'id' => $id, 'p' => 0, 'w' => 0, 'd' => 0, 'l' => 0, 'gf' => 0, 'ga' => 0, 'pts' => 0, 'form' => array_slice( $form[ $n ] ?? [], -5 ) ];
		}
		if ( 'done' !== $m['state'] ) {
			continue;
		}
		foreach ( [ [ $m['home'], $m['hs'], $m['as'] ], [ $m['away'], $m['as'], $m['hs'] ] ] as [ $n, $f, $a ] ) {
			$r = &$calc[ $n ];
			$r['p']++; $r['gf'] += $f; $r['ga'] += $a;
			if ( $f > $a ) { $r['w']++; $r['pts'] += 2; } elseif ( $f === $a ) { $r['d']++; $r['pts']++; } else { $r['l']++; }
			unset( $r );
		}
	}
	$calc = array_values( $calc );
	usort( $calc, fn( $a, $b ) => [ $b['pts'], $b['gf'] - $b['ga'], $b['gf'] ] <=> [ $a['pts'], $a['gf'] - $a['ga'], $a['gf'] ] );
	return $calc;
}

/** Svæði í töflu fyrir sæti (1-raðað): up, playoff, playoff_down, down eða ''. */
function hk_zone( $key, $pos, $count ) {
	$z = hk_leagues()[ $key ]['zones'];
	if ( ! empty( $z['up'] ) && $pos <= $z['up'] ) { return 'up'; }
	if ( ! empty( $z['playoff_up'] ) && $pos <= $z['playoff_up'] ) { return 'playoff'; }
	if ( ! empty( $z['playoff'] ) && $pos <= $z['playoff'] ) { return 'playoff'; }
	$down = $z['down'] ?? 0;
	if ( $down && $pos > $count - $down ) { return 'down'; }
	if ( ! empty( $z['playoff_down'] ) && $pos > $count - $down - $z['playoff_down'] ) { return 'playoff_down'; }
	return '';
}

function hk_zone_labels( $key ) {
	$z = hk_leagues()[ $key ]['zones'];
	$l = [];
	if ( ! empty( $z['up'] ) )           { $l['up'] = 'Fer upp í Olís-deildina'; }
	if ( ! empty( $z['playoff_up'] ) )   { $l['playoff'] = 'Umspil um sæti í Olís-deildinni'; }
	if ( ! empty( $z['playoff'] ) )      { $l['playoff'] = 'Úrslitakeppni'; }
	if ( ! empty( $z['playoff_down'] ) ) { $l['playoff_down'] = 'Umspil um áframhaldandi sæti'; }
	if ( ! empty( $z['down'] ) )         { $l['down'] = 'Fellur'; }
	return $l;
}

function hk_all_matches() {
	$all = [];
	foreach ( array_keys( hk_leagues() ) as $k ) {
		$all = array_merge( $all, hk_league( $k ) );
	}
	usort( $all, fn( $a, $b ) => [ $a['ts'], $a['league'] ] <=> [ $b['ts'], $b['league'] ] );
	return $all;
}

/* ---------- Framsetning ---------- */

function hk_crest( $team, $team_id = '' ) {
	$parts = preg_split( '/[\s\/]+/u', $team );
	$txt   = mb_strtoupper( count( $parts ) > 1 && ! is_numeric( $parts[1] ) ? mb_substr( $parts[0], 0, 1 ) . mb_substr( $parts[1], 0, 1 ) : mb_substr( $team, 0, 2 ) );
	$logo  = hk_team_logo( $team_id );
	return sprintf(
		'<span class="crest" aria-hidden="true"><span>%s</span>%s</span>',
		esc_html( $txt ),
		$logo ? sprintf( '<img src="%s" alt="" loading="lazy" onerror="this.remove()">', esc_url( $logo ) ) : ''
	);
}

/** „Í kvöld 19:30“, „Á morgun 14:00“, „Lau 11. okt · 16:00“. */
function hk_kickoff( $ts ) {
	$d = wp_date( 'Y-m-d', $ts );
	$t = wp_date( 'H:i', $ts );
	if ( wp_date( 'Y-m-d' ) === $d ) {
		return ( (int) wp_date( 'G', $ts ) >= 17 ? 'Í kvöld ' : 'Í dag ' ) . $t;
	}
	if ( wp_date( 'Y-m-d', strtotime( '+1 day' ) ) === $d ) {
		return 'Á morgun ' . $t;
	}
	return hk_dayname( $ts, true ) . ' ' . wp_date( 'j. M', $ts ) . ' · ' . $t;
}

function hk_dayname( $ts, $short = false ) {
	$long = [ 'sunnudagur', 'mánudagur', 'þriðjudagur', 'miðvikudagur', 'fimmtudagur', 'föstudagur', 'laugardagur' ];
	$abbr = [ 'Sun', 'Mán', 'Þri', 'Mið', 'Fim', 'Fös', 'Lau' ];
	$w    = (int) wp_date( 'w', $ts );
	return $short ? $abbr[ $w ] : $long[ $w ];
}

/** „Í dag · Föstudagur 9. október“ eða „Laugardagur 11. október“. */
function hk_day_heading( $ts ) {
	$d   = wp_date( 'Y-m-d', $ts );
	$rel = [
		wp_date( 'Y-m-d' )                        => 'Í dag',
		wp_date( 'Y-m-d', strtotime( '-1 day' ) ) => 'Í gær',
		wp_date( 'Y-m-d', strtotime( '+1 day' ) ) => 'Á morgun',
	][ $d ] ?? '';
	$abs = mb_convert_case( hk_dayname( $ts ), MB_CASE_TITLE ) . ' ' . wp_date( 'j. F', $ts );
	return $rel ? $rel . ' · ' . $abs : $abs;
}

function hk_group_by_day( $matches ) {
	$out = [];
	foreach ( $matches as $m ) {
		$out[ wp_date( 'Y-m-d', $m['ts'] ) ][] = $m;
	}
	return $out;
}

function hk_signed( $n ) {
	return $n > 0 ? '+' . $n : ( $n < 0 ? '−' . abs( $n ) : '0' );
}

/* ---------- Forsíða og hliðarstika ---------- */

/** Leikjavaktin: leikir í gangi, nýjustu úrslit og næstu leikir. */
function hk_games() {
	$all   = hk_all_matches();
	$live  = array_values( array_filter( $all, fn( $m ) => in_array( $m['state'], [ 'live', 'pending' ], true ) && $m['ts'] > time() - DAY_IN_SECONDS ) );
	$done  = array_values( array_filter( $all, fn( $m ) => 'done' === $m['state'] ) );
	$next  = array_values( array_filter( $all, fn( $m ) => 'upcoming' === $m['state'] ) );
	$games = array_merge( array_slice( $done, -4 ), $live, array_slice( $next, 0, max( 3, 8 - count( $live ) - 4 ) ) );
	return [ 'sample' => false, 'games' => $games ];
}

function hk_standings() {
	$tables = [];
	foreach ( [ 'olis-karla', 'olis-kvenna' ] as $k ) {
		$L = hk_leagues()[ $k ];
		$tables[ $L['short'] ] = [
			'key'  => $k,
			'cut'  => $L['zones']['playoff'],
			'rows' => array_map( fn( $r ) => [ $r['team'], $r['p'], $r['gf'] - $r['ga'], $r['pts'] ], hk_table( $k ) ),
		];
	}
	return [ 'sample' => false, 'tables' => $tables ];
}

function hk_upcoming() {
	$next = array_filter( hk_all_matches(), fn( $m ) => 'upcoming' === $m['state'] && str_starts_with( $m['league'], 'olis' ) );
	$out  = [];
	foreach ( array_slice( array_values( $next ), 0, 4 ) as $m ) {
		$out[] = [ $m['home'] . ' – ' . $m['away'], hk_kickoff( $m['ts'] ) ];
	}
	return [ 'sample' => false, 'games' => $out ];
}
