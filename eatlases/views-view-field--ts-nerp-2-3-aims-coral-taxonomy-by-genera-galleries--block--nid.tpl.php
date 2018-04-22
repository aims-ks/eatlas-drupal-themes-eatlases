<?php
/**
 * Temporary template to display the Gallery of Galleries.
 * Example:
 *     http://eatlas.org.au/node/1426
 *
 * This should eventually be replace by "faceted search",
 * which will show a serie of checkboxes for different
 * taxonomy terms.
 */
print '<div class="gallery-image">';
if (isset($output) && is_numeric($output)) {
	$node = node_load($output);
	$preview = $node->field_preview;
	if (!empty($preview)) {
		$file = file_load($preview[LANGUAGE_NONE][0]['fid']);
		if (!empty($file) && module_exists('eatlas_media_frame_filter')) {
			print eatlas_media_frame_decorate(
				array (
					'#file' => $file
				),
				array(
					'media_style' => 'onImage',
					'media_link' => 'direct',
					'media_title' => $node->title,
					'styleName' => 'm_article_sq_crop'
				)
			);
		}
	} else {
		// Show a broken image when there is no preview image
		print l('<img src="/sites/all/modules/media/images/icons/default/image-x-generic.png" />', 'node/' . $output, array('html' => TRUE));
	}
}
print '</div>';
?>

