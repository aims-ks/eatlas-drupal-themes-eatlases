<?php
// Original: www/modules/search/search-result.tpl.php
/**
 * @file
 * Default theme implementation for displaying a single search result.
 *
 * This template renders a single search result and is collected into
 * search-results.tpl.php. This and the parent template are
 * dependent to one another sharing the markup for definition lists.
 *
 * Available variables:
 * - $url: URL of the result.
 * - $title: Title of the result.
 * - $snippet: A small preview of the result. Does not apply to user searches.
 * - $info: String of all the meta information ready for print. Does not apply
 *   to user searches.
 * - $info_split: Contains same data as $info, split into a keyed array.
 * - $module: The machine-readable name of the module (tab) being searched, such
 *   as "node" or "user".
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 *
 * Default keys within $info_split:
 * - $info_split['type']: Node type (or item type string supplied by module).
 * - $info_split['user']: Author of the node linked to users profile. Depends
 *   on permission.
 * - $info_split['date']: Last update of the node. Short formatted.
 * - $info_split['comment']: Number of comments output as "% comments", %
 *   being the count. Depends on comment.module.
 *
 * Other variables:
 * - $classes_array: Array of HTML class attribute values. It is flattened
 *   into a string within the variable $classes.
 * - $title_attributes_array: Array of HTML attributes for the title. It is
 *   flattened into a string within the variable $title_attributes.
 * - $content_attributes_array: Array of HTML attributes for the content. It is
 *   flattened into a string within the variable $content_attributes.
 *
 * Since $info_split is keyed, a direct print of the item is possible.
 * This array does not apply to user searches so it is recommended to check
 * for its existence before printing. The default keys of 'type', 'user' and
 * 'date' always exist for node searches. Modules may provide other data.
 * @code
 *   <?php if (isset($info_split['comment'])): ?>
 *     <span class="info-comment">
 *       <?php print $info_split['comment']; ?>
 *     </span>
 *   <?php endif; ?>
 * @endcode
 *
 * To check for all available data within $info_split, use the code below.
 * @code
 *   <?php print '<pre>'. check_plain(print_r($info_split, 1)) .'</pre>'; ?>
 * @endcode
 *
 * @see template_preprocess()
 * @see template_preprocess_search_result()
 * @see template_process()
 *
 * @ingroup themeable
 */

$can_edit = FALSE;

$preview = FALSE;
$file = FALSE;
$type = isset($result['type']) ? $result['type'] : FALSE;
$node_type = NULL;
$title = NULL;
$edit_link = NULL;

if (isset($result['file'])) {
	// Image search result

	// Set the title
	if (property_exists($result['file'], 'media_title') &&
			isset($result['file']->media_title[LANGUAGE_NONE]) &&
			isset($result['file']->media_title[LANGUAGE_NONE][0]) &&
			isset($result['file']->media_title[LANGUAGE_NONE][0]['value']) &&
			$result['file']->media_title[LANGUAGE_NONE][0]['value']) {
		$title = $result['file']->media_title[LANGUAGE_NONE][0]['value'];

	} else if (property_exists($result['file'], 'filename') &&
			$result['file']->filename) {
		$title = $result['file']->filename;
	}

	$file = array (
		'#file' => $result['file']
	);

	if (isset($result['file']->fid)) {
		$edit_link = url('media/' . $result['file']->fid . '/edit');
		// "file_entity_access" should be used, but it's not defined in
		// all version of the Media module. The function "user_access"
		// is less specific (do not allow to check file individually)
		// but should always works since it's a core function.
		// "media_access" is deprecated.
		if (function_exists('file_entity_access')) {
			$can_edit = file_entity_access('update', $result['file']);
		} else {
			$can_edit = (user_access('administer media') || user_access('edit media'));
		}
	}
} else if (isset($result['node'])) {
	// Node search result
	// The field for the preview image depend on the node type (default: 'field_preview')
	if (isset($result['node']->type)) {
		$node_type = $result['node']->type;
		$title = $result['node']->title;
		$fieldPreviewName = NULL;
		switch($node_type) {
			case 'person':
				$fieldPreviewName = 'field_photos';
				break;
			case 'organisation':
			case 'organisation_section':
			case 'research_program':
				$fieldPreviewName = 'field_logos';
				break;
			case 'eatlas_georss_item':
				// It will only occured if the module eatlas_georss_aggregator is enabled
				$fieldBody = field_get_items('node', $result['node'], 'body');
				if ($fieldBody) {
					$body_field_view = field_view_value('node', $result['node'], 'body', $fieldBody[0]);
					$body = render($body_field_view);

					// Extract images
					preg_match_all('/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $body, $images);
					if (isset($images[1][0])) {
						$previewSrc = $images[1][0];
						$file = array (
							'#item' => array(
								'url' => $previewSrc
							)
						);
					}
				}
				break;
			default:
				$fieldPreviewName = 'field_preview';
				break;
		}
		if ($fieldPreviewName) {
			$fieldPreview = field_get_items('node', $result['node'], $fieldPreviewName);
			if ($fieldPreview) {
				$file = field_view_value('node', $result['node'], $fieldPreviewName, $fieldPreview[0]);
			}
		}
	}

	if (isset($result['node']->nid)) {
		$edit_link = url('node/' . $result['node']->nid . '/edit');
		$can_edit = node_access('update', $result['node']);
	}
} else if (isset($result['item'])) {
	// Aggregator search result
	if (isset($result['item']['description'])) {
		$desc = $result['item']['description'];

		// Extract images
		preg_match_all('/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/i',$desc, $images);
		if (isset($images[1][0])) {
			$previewSrc = $images[1][0];
			$file = array (
				'#item' => array(
					'url' => $previewSrc
				)
			);
		}
	}
	// $edit_link:
	// There is no UI to edit the aggregator_item
}

if ($file) {
	if (module_exists('eatlas_media_frame_filter')) {
		$preview = eatlas_media_frame_decorate(
			$file,
			array(
				'showMetadata' => false,
				'styleName' => 'm_small',
				'media_style' => 'gallery'
			),
			$url,
			NULL,
			FALSE
		);
	} else {
		if (isset($file['#item']['url'])) {
			$preview = l('<img src="' . $file['#item']['url'] . '" />', $url, array('html' => TRUE));
		} else {
			$preview = l('<img src="' . image_style_url('m_small', $file['#file']->uri) . '" />', $url, array('html' => TRUE));
		}
	}
}

// Video - Add the "Play" button over the preview image.
if ($node_type === 'video') {
	if ($preview) {
		// If preview - Add the play button
		$preview = '<div class="view-video">' .
					$preview .
					'<a href="' . $url . '" class="view-video-overlay" title="' . check_plain($title) . '"></a>' . 
				'</div>';
	} else {
		// If no preview - Create an empty preview box and add the play button
		$preview = '<div class="view-video">' .
					'<div class="mediaframe_gallery wysiwyg-media-file noMetadata no-preview"></div>' .
					'<a href="' . $url . '" class="view-video-overlay" title="' . check_plain($title) . '"></a>' .
				'</div>';
	}
}

if (!$title) {
	$title = 'Untitled';
}

?>
<!-- class="search-result" -->
<li class="<?php print $classes; ?>"<?php print $attributes; ?>>
	<div class="search-preview">
		<?php if ($preview): ?>
			<?php print $preview; ?>
		<?php endif; ?>
	</div>

	<div class="search-abstract">
		<?php print render($title_prefix); ?>
		<h3 class="title"<?php print $title_attributes; ?>>
			<a href="<?php print $url; ?>"><?php print $title; ?></a>
			<?php if ($can_edit && $edit_link): ?>
				<span class="edit-link"><a href="<?php print $edit_link; ?>">[edit]</a></span>
			<?php endif; ?>
		</h3>
		<?php print render($title_suffix); ?>

		<div class="search-snippet-info">
			<?php if ($snippet): ?>
				<p class="search-snippet"<?php print $content_attributes; ?>><?php print $snippet; ?></p>
			<?php endif; ?>
			<?php if ($info): ?>
				<p class="search-info"><?php print $info; ?></p>
			<?php endif; ?>
			<?php if ($type): ?>
				<p class="search-type"><?php print $type; ?></p>
			<?php endif; ?>
		</div>
	</div>
</li>
