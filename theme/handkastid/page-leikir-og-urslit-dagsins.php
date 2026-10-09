<?php
/**
 * Leikir og úrslit dagsins – gögn frá HSÍ.
 */

get_header();
get_template_part( 'template-parts/match-days', null, [ 'title' => 'Leikir og úrslit dagsins', 'mode' => 'today' ] );
get_footer();
