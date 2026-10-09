<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip" href="#efni">Fara beint í efni</a>

<header class="site-header">
	<div class="top">
		<svg class="court" viewBox="0 0 520 340" fill="none" aria-hidden="true">
			<path d="M40 0 A220 220 0 0 0 480 0" stroke="currentColor" stroke-width="3"/>
			<path d="M-20 0 A280 280 0 0 0 540 0" stroke="currentColor" stroke-width="3" stroke-dasharray="14 12"/>
			<line x1="230" y1="250" x2="290" y2="250" stroke="currentColor" stroke-width="3"/>
		</svg>
		<div class="wrap">
			<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/logo.jpg' ); ?>" alt="" width="46" height="46">
				<span><b><?php bloginfo( 'name' ); ?></b><small>Allt um handboltann</small></span>
			</a>
			<button class="menu-btn" type="button" aria-expanded="false" aria-controls="valmynd">Valmynd</button>
			<nav class="nav" id="valmynd" aria-label="Aðalvalmynd">
				<?php
				hk_menu( 'primary', [
					'Fréttir'      => hk_news_url(),
					'Úrslit'       => hk_page_url( 'nyjustu-urslit' ),
					'Næstu leikir' => hk_page_url( 'naestu-leikir' ),
					'Stöðutöflur'  => hk_page_url( 'stodutoflur' ),
					'Hlaðvarp'     => hk_cat_url( 'handkastid' ),
					'Um okkur'     => hk_page_url( 'um-handkastid' ),
				] );
				?>
				<form class="search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
					<label class="sr" for="leit">Leita</label>
					<input id="leit" type="search" name="s" placeholder="Leita…" value="<?php echo esc_attr( get_search_query() ); ?>">
				</form>
			</nav>
		</div>
	</div>
	<nav class="subnav" aria-label="Flokkar">
		<div class="wrap">
			<?php
			hk_menu( 'categories', [
				'Strákarnir okkar'  => hk_cat_url( 'strakarnir-okkar' ),
				'Stelpurnar okkar'  => hk_cat_url( 'stelpurnar-okkar' ),
				'Íslenski boltinn – karlar' => hk_cat_url( 'islenskar-frettir-karla' ),
				'Íslenski boltinn – konur'  => hk_cat_url( 'islenskar-frettir-kvenna' ),
				'Erlent – karlar'   => hk_cat_url( 'erlendar-frettir-karla' ),
				'Erlent – konur'    => hk_cat_url( 'erlendar-frettir-kvenna' ),
				'Bakhliðin'         => hk_cat_url( 'bakhlidin' ),
				'Yngri landslið'    => hk_cat_url( 'yngri-landslid' ),
			] );
			?>
		</div>
	</nav>
</header>

<div class="ad-top"><div class="wrap"><?php hk_ad( 'top' ); ?></div></div>
