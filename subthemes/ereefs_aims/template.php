<?php

/**
 * Implements hook_preprocess_html().
 *
 * Add conditional Style Sheet for IE8 and lower.
 */
function ereefs_aims_preprocess_html(&$variables) {
	// Add conditional stylesheets for IE8.
	drupal_add_css(
		drupal_get_path('theme', 'ereefs_aims') . '/css/ereefs_aims_ie8.css',
		array(
			'group' => CSS_THEME,
			'browsers' => array(
				'IE' => 'lte IE 8',
				'!IE' => FALSE
			),
			'weight' => 999,
			'every_page' => TRUE
		)
	);
}

?>
