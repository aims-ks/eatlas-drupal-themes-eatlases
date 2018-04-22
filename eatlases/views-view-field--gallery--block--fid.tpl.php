<?php
// Original: sites/all/modules/views/theme/views-view-field.tpl.php
// Modified for "media": sites/all/themes/eatlases/views-view-field.tpl.php

print '<div class="gallery-image">';
if (isset($output) && is_numeric($output)) {
	$file = file_load($output);
	if (module_exists('eatlas_media_frame_filter')) {
		print eatlas_media_frame_decorate(
			array (
				'#file' => $file
			),
			array(
				'media_style' => 'onImage',
				'media_link' => 'direct',
				'styleName' => 'm_article_sq_crop'
			)
		);
	} else {
		print l('<img src="' . image_style_url('medium', $file->uri) . '" />', 'media/' . $output, array('html' => TRUE));
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
print '</div>';
?>

