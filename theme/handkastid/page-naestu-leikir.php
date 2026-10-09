<?php
/**
 * Næstu leikir – gögn frá HSÍ.
 */

get_header();
get_template_part( 'template-parts/match-days', null, [ 'title' => 'Næstu leikir', 'mode' => 'fixtures' ] );
get_footer();
