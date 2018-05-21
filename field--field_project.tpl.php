<?php
/**
 * Inspired of Drupal core field template:
 *     /modules/field/theme/field.tpl.php
 */
?>
<div class="<?php print $classes; ?>"<?php print $attributes; ?>>
	<?php if (!$label_hidden): ?>
		<div class="field-label"<?php print $title_attributes; ?>>Projects</div>
	<?php endif; ?>
	<div class="field-items"<?php print $content_attributes; ?>>
		<?php foreach ($items as $delta => $item): ?>
			<?php if (isset($item['#options']['entity'])): ?>
				<?php
					$project = $item['#options']['entity'];
					$link = 'node/' . $project->nid;
					$previewImage = property_exists($project, 'field_preview') ? $project->field_preview : NULL;

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
										'media_style' => 'wikipedia',
										'media_link' => 'none'
									),
									url($link),
									NULL,
									FALSE
							);
						}
					} else {
						if (isset($previewImage[LANGUAGE_NONE][0]['uri'])) {
							$previewImageHtml = l('<img src="' . image_style_url('m_icon', $previewImage[LANGUAGE_NONE][0]['uri']) . '"/>', $link, array('html' => TRUE));
						}
					}
				?>
				<div class="field-item <?php print $delta % 2 ? 'odd' : 'even'; ?>"<?php print $item_attributes[$delta]; ?>>
					<?php if ($previewImageHtml): ?>
						<div class="preview">
							<?php print render($previewImageHtml); ?>
						</div>
					<?php endif; ?>
					<?php print render($item); ?>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
</div>
