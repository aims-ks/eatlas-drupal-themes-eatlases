<?php
$banner_style = NULL;
$banner_css_class = NULL;
$media_style = NULL;

// API: https://api.drupal.org/api/drupal/modules!field!theme!field.tpl.php/7

// The node (or file if we decide to apply this to the files in the future)
$entity = $element['#object'];

$entity_type = arg(0); // 'node'

// Do not mess with the preview image when it's not the preview image for the current node.
// I.E. Do not touch it if the node is display in a list or an image slider for example.
if ($entity->nid !== arg(1)) {
	return NULL;
}

// Do not show the banner if the field "field_banner" is set to "None" (null).
if ($entity) {
	$banner_field = field_get_items($entity_type, $entity, 'field_banner');
	if ($banner_field) {
		$banner_field_value = $banner_field[0]['value'];
		switch($banner_field_value) {
			case 'full':
				$banner_style = 'm_preview_full';
				$banner_css_class = 'preview-full';
				$media_style = 'wikipedia';
				break;
			case 'strip':
				$banner_style = 'm_preview_strip';
				$banner_css_class = 'preview-strip';
				$media_style = 'enlarge';
				break;
		}
	}
}
?>

<div class="<?php print $classes; ?>"<?php print $attributes; ?>>
	<?php if ($banner_style !== NULL): ?>
		<?php if (!$label_hidden): ?>
			<div class="field-label"<?php print $title_attributes; ?>><?php print $label ?><span class="label-suffix">:&nbsp;</span></div>
		<?php endif; ?>
		<div class="field-items"<?php print $content_attributes; ?>>
			<?php foreach ($items as $delta => $item): ?>
				<div class="field-item <?php print $banner_css_class . ' ' . ($delta % 2 ? 'odd' : 'even'); ?>"<?php print $item_attributes[$delta]; ?>>
					<?php
						$item['#view_mode'] = $banner_style;
						if (module_exists('eatlas_media_frame_filter')) {
							print eatlas_media_frame_decorate(
								$item,
								array(
									'media_style' => $media_style
								)
							);
						} else {
							print render($item);
						}
					?>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>
