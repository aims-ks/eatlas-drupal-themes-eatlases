<?php

function ereefs_preprocess_node($vars) {
	$css_filename = NULL;
	if (isset($vars['nid'])) {
		switch ($vars['nid']) {
			case '1435':
			case '1441':
			case '1442':
			case '1443':
			case '1444':
			case '1445':
				// About page
				$css_filename = 'ereefs_about.css';
				break;
			case '1436':
				// Partners page
				$css_filename = 'ereefs_partners.css';
				break;
			case '1446':
			case '1447':
			case '1448':
			case '1449':
			case '1450':
			case '1451':
				// Platform pages
				$css_filename = 'ereefs_platform.css';
				break;
			case '1438':
			case '1453':
			case '1454':
				// Applications pages
				$css_filename = 'ereefs_applications.css';
				break;
			case '1452':
				// Data page
				$css_filename = 'ereefs_data.css';
				break;
			case '1439':
				// Publications page
				$css_filename = 'ereefs_publications.css';
				break;
		}
	}

	if (!empty($css_filename)) {
		// https://api.drupal.org/api/drupal/includes%21common.inc/function/drupal_add_css/7
		drupal_add_css(drupal_get_path('theme', 'ereefs') . '/css/pages/' . $css_filename,
			array(
				'group' => CSS_THEME
			)
		);
	}
}

/**
 * Implements hook_preprocess_html().
 *
 * Add conditional Style Sheet for IE8 and lower.
 */
function ereefs_preprocess_html(&$variables) {
	// Add conditional stylesheets for IE8.
	drupal_add_css(
		drupal_get_path('theme', 'ereefs') . '/css/ereefs_ie8.css',
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
