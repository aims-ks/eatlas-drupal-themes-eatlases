<?php
/**
 * Inspired of Drupal core field template:
 *     www/modules/field/theme/field.tpl.php
 */

// Get the prepress value for the current node (if the field exists)
$node_prepress = isset($element['#object']) &&
		property_exists($element['#object'], 'field_draft') &&
		isset($element['#object']->field_draft[LANGUAGE_NONE][0]['value']) ?
			$element['#object']->field_draft[LANGUAGE_NONE][0]['value'] :
			FALSE;

// Put all related content output in that variable (we can then decide to hide the label when there is nothing to display)
$related_content_output = '';

foreach ($items as $delta => $item) {
	// The entity (node) is in a different location depending on the view display
	$item_entity = isset($item['#node']) ? $item['#node'] : (isset($item['#options']['entity']) ? $item['#options']['entity'] : NULL);

	if (isset($item_entity)) {
		$published = property_exists($item_entity, 'status') ? $item_entity->status : FALSE;
		$prepress = property_exists($item_entity, 'field_draft') && isset($item_entity->field_draft[LANGUAGE_NONE][0]['value']) ?
				$item_entity->field_draft[LANGUAGE_NONE][0]['value'] :
				FALSE;

		// Filter out some related content:
		// * Content that are NOT published
		// * Content that are set as prepress (NOTE: We still want to show those if the current node is also prepress)
		if ($published && ($node_prepress || !$prepress)) {
			$previewImage = property_exists($item_entity, 'field_preview') ? $item_entity->field_preview : NULL;
			$title = property_exists($item_entity, 'title') ? $item_entity->title : 'Untitled'; // Just to be sure (title is mandatory)
			$link = property_exists($item_entity, 'nid') ? 'node/' . $item_entity->nid : NULL;

			$linkedTitle = $link ? l($title, $link) : $title;

			$previewImageHtml = NULL;
			if (module_exists('eatlas_media_frame_filter')) {
				if (isset($previewImage[LANGUAGE_NONE][0])) {
					$previewImageHtml = eatlas_media_frame_decorate(
							array(
								'#item' => $previewImage[LANGUAGE_NONE][0]
							),
							array(
								'showMetadata' => false,
								'styleName' => 'm_icon',
								'media_style' => 'none'
							),
							url($link),
							NULL,
							FALSE
					);
				} else {
					// NOTE: path_to_theme() return the theme currently used to theme the element instead of "this" theme.
					$noPreviewUrl = base_path() . drupal_get_path('theme', 'eatlases') . '/img/no-preview_fade_small.png';
					$previewImageHtml = l('<img src="' . $noPreviewUrl . '" />', $link, array('html' => TRUE));
				}
			} else {
				if (isset($previewImage[LANGUAGE_NONE][0]['uri'])) {
					$previewImageHtml = l('<img src="' . image_style_url('m_icon', $previewImage[LANGUAGE_NONE][0]['uri']) . '"/>', $link, array('html' => TRUE));
				}
			}

			// Create the output for that item
			$related_content_output .= "<div class=\"field-item ".($delta % 2 ? 'odd' : 'even')."\"".$item_attributes[$delta].">\n";
			if ($previewImageHtml) {
				$related_content_output .= "<div class=\"preview\">\n" .
						render($previewImageHtml) .
						"</div>\n";
			}
			if($linkedTitle) {
				$related_content_output .= "<div class=\"title\">\n" .
						render($linkedTitle) .
						"</div>\n";
			}
			$related_content_output .= "</div>\n";
		}
	}
}
?>

<div class="<?php print $classes; ?>"<?php print $attributes; ?>>
	<?php if (!empty($related_content_output)): ?>
		<?php if (!$label_hidden): ?>
			<div class="field-label"<?php print $title_attributes; ?>><?php print $label ?><span class="label-suffix">:&nbsp;</span></div>
		<?php endif; ?>

		<div class="field-items"<?php print $content_attributes; ?>>
			<?php print $related_content_output; ?>
		</div>
	<?php endif; ?>
</div>
