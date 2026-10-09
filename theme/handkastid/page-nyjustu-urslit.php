<?php
/**
 * Nýjustu úrslit – gögn frá HSÍ.
 */

get_header();
get_template_part( 'template-parts/match-days', null, [ 'title' => 'Nýjustu úrslit', 'mode' => 'results' ] );
get_footer();
