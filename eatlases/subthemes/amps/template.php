<?php

/**
 * Implements template_preprocess_html
 *   https://api.drupal.org/api/drupal/includes%21theme.inc/function/template_preprocess_html/7.x
 *
 * Adds custom font, to looks like the marine park website:
 *   https://parksaustralia.gov.au
 * See: https://www.drupal.org/docs/7/theming/working-with-css/adding-style-sheets-from-info-files
 */
function amps_preprocess_html(&$variables) {
	drupal_add_css('https://fonts.googleapis.com/css?family=Muli:400,600,700|Lato:400,700', array('type' => 'external'));
}

/**
 * Implements: hook_preprocess_HOOK
 *   https://api.drupal.org/api/drupal/modules%21system%21theme.api.php/function/hook_preprocess_HOOK/7.x
 * Changes the search form to use the "search" input element of HTML5.
 * Add search overlay and minimised search button.
 * NOTE: The search "href" is only used when the browser doesn't support JavaScript (accessibility)
 */
function amps_preprocess_search_block_form(&$vars) {
	$vars['search_form'] =
		'<div class="search-block-minimised">' . "\n" .
			'<a class="show-search-overlay" title="Search" href="/search"></a>' . "\n" .
		'</div>' . "\n" .
		'<div class="search-block-overlay">' . "\n" .
			'<div class="search-overlay"></div>' . "\n" .
			'<div class="search-overlay-content">' . "\n" .
				$vars['search_form'] . "\n" .
				'<div class="close-search-overlay" title="Close search"></div>' . "\n" .
			'</div>' . "\n" .
		'</div>';
}

?>
