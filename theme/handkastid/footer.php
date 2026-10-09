<footer class="site-footer">
	<div class="wrap">
		<div>
			<h4>Fréttir</h4>
			<ul>
				<li><a href="<?php echo esc_url( hk_cat_url( 'strakarnir-okkar' ) ); ?>">Strákarnir okkar</a></li>
				<li><a href="<?php echo esc_url( hk_cat_url( 'stelpurnar-okkar' ) ); ?>">Stelpurnar okkar</a></li>
				<li><a href="<?php echo esc_url( hk_cat_url( 'islenskar-frettir-karla' ) ); ?>">Íslenski boltinn</a></li>
				<li><a href="<?php echo esc_url( hk_cat_url( 'erlendar-frettir-karla' ) ); ?>">Erlent</a></li>
			</ul>
		</div>
		<div>
			<h4>Leikir</h4>
			<ul>
				<li><a href="<?php echo esc_url( hk_page_url( 'nyjustu-urslit' ) ); ?>">Úrslit</a></li>
				<li><a href="<?php echo esc_url( hk_page_url( 'naestu-leikir' ) ); ?>">Næstu leikir</a></li>
				<li><a href="<?php echo esc_url( hk_page_url( 'stodutoflur' ) ); ?>">Stöðutöflur</a></li>
			</ul>
		</div>
		<div>
			<h4>Handkastið</h4>
			<ul>
				<li><a href="<?php echo esc_url( hk_cat_url( 'handkastid' ) ); ?>">Hlaðvarp</a></li>
				<li><a href="<?php echo esc_url( hk_cat_url( 'bakhlidin' ) ); ?>">Bakhliðin</a></li>
				<li><a href="<?php echo esc_url( hk_page_url( 'um-handkastid' ) ); ?>">Um Handkastið</a></li>
			</ul>
		</div>
		<div>
			<h4>Fylgdu okkur</h4>
			<ul>
				<?php
				foreach ( [ 'hk_instagram' => 'Instagram', 'hk_facebook' => 'Facebook', 'hk_x' => 'X', 'hk_spotify' => 'Spotify' ] as $mod => $label ) {
					$url = get_theme_mod( $mod );
					echo $url
						? sprintf( '<li><a href="%s" rel="noopener" target="_blank">%s</a></li>', esc_url( $url ), esc_html( $label ) )
						: sprintf( '<li>%s</li>', esc_html( $label ) );
				}
				?>
			</ul>
		</div>
		<div class="copy">© <?php echo esc_html( wp_date( 'Y' ) ); ?> Handkastið – allt um handboltann. Stofnað sem hlaðvarp sumarið 2017.</div>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
