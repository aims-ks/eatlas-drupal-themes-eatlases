<?php
/**
 * @file
 * This template is used to print a single field in a view.
 *
 * It is not actually used in default Views, as this is registered as a theme
 * function which has better performance. For single overrides, the template is
 * perfectly okay.
 *
 * Variables available:
 * - $view: The view object
 * - $field: The field handler object that can process the input
 * - $row: The raw SQL result that can be used
 * - $output: The processed output that will normally be used.
 *
 * When fetching output from the $row, this construct should be used:
 * $data = $row->{$field->field_alias}
 *
 * The above will guarantee that you'll always get the correct data,
 * regardless of any changes in the aliasing that might happen if
 * the view is modified.
 */

// If it's a views that display photos, display them as 'gallery' items.
// Otherwise, call the 'eatlas_media_frame_filter' default template.
if (isset($output) && is_numeric($output)) {
	$file = file_load($output);
	if ($file) {
		if (module_exists('eatlas_media_frame_filter')) {
			print eatlas_media_frame_decorate(
				array (
					'#file' => $file
				),
				array(
					'showMetadata' => false,
					'media_style' => 'gallery',
					'media_link' => 'direct'
				)
			);
		} else {
			print l('<img src="' . image_style_url('medium', $file->uri) . '" />', 'media/' . $output, array('html' => TRUE));
		}
	}
} else {
	// Output the original template:
	//     www/sites/all/modules/media_gallery_fixes/media-item-details.tpl.php
	if (function_exists('drupal_get_path') && module_exists('eatlas_media_frame_filter')) {
		$template_path = DRUPAL_ROOT . '/' . drupal_get_path('module', 'eatlas_media_frame_filter') . '/templates/views-view-field.tpl.php';
		if (is_file($template_path)) {
			include($template_path);
		}
	}
}
