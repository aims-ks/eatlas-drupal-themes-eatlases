<?php

/**
 * Implements: hook_preprocess_HOOK
 *   https://api.drupal.org/api/drupal/modules%21system%21theme.api.php/function/hook_preprocess_HOOK/7.x
 * Changes the search form to use the "search" input element of HTML5.
 * Add search overlay and minimised search button.
 */
function eatlas_preprocess_search_block_form(&$vars) {
	$vars['search_form'] =
		'<div class="search-block-minimised">' . "\n" .
			'<div class="show-search-overlay" title="Search"></div>' . "\n" .
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
