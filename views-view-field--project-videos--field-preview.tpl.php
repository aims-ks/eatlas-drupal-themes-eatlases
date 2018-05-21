<?php
/**
 * Template used to display video in the "View: Project's videos" block (video gallery)
 *
 * Available variables:
 *   [0] => template_file (path to this file)
 *   [1] => variables     (array containing all variables listed bellow - somewhat redundant)
 *   [2] => view          (the definition of the view)
 *   [3] => field         (the form field)
 *   [4] => row           ()
 *   [5] => theme_hook_original
 *   [6] => theme_hook_suggestion
 *   [7] => theme_hook_suggestions
 *   [8] => output
 *   [9] => zebra
 *   [10] => id (???)
 *   [11] => directory
 *   [12] => classes_array
 *   [13] => attributes_array
 *   [14] => title_attributes_array
 *   [15] => content_attributes_array
 *   [16] => title_prefix
 *   [17] => title_suffix
 *   [18] => user
 *   [19] => db_is_active
 *   [20] => is_admin
 *   [21] => logged_in
 *   [22] => is_front
 */

if ($row && property_exists($row, 'nid')) {
	$node_id = $row->nid;
	$node = node_load($node_id);

	$node_uri = 'node/' . $node_id;
	$node_url = url($node_uri);
	$node_title = check_plain($node->title);

	if (property_exists($row, 'field_field_preview')) {
		if ($row->field_field_preview) {
			foreach ($row->field_field_preview as $delta => $field_preview) {
				if ($field_preview && isset($field_preview['rendered']) && isset($field_preview['rendered']['#file'])) {
					$rendered = $field_preview['rendered'];
					$file = $rendered['#file'];
					if ($file) {
						?>
							<div class="view-video">
								<?php
								if (module_exists('eatlas_media_frame_filter')) {
									print eatlas_media_frame_decorate(
										$rendered,
										array(
											'showMetadata' => false,
											'media_style' => 'gallery'
										),
										$node_url
									);
								} else {
									print l('<img src="' . image_style_url('medium', $file->uri) . '" title="' . $node_title . '" />', $node_uri, array('html' => TRUE));
								}
								?>
								<a href="<?php print $node_url; ?>" class="view-video-overlay" title="<?php print $node_title ?>"></a>
							</div>
						<?php
					}
				}
			}
		} else {
			?>
				<div class="view-video">
					<div class="mediaframe_gallery wysiwyg-media-file noMetadata no-preview"></div>
					<a href="<?php print $node_url; ?>" class="view-video-overlay" title="<?php print $node_title ?>"></a>
				</div>
			<?php
		}
	}
}
?>
