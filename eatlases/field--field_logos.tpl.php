<div class="<?php print $classes; ?>"<?php print $attributes; ?>>
	<?php if (!$label_hidden): ?>
		<div class="field-label"<?php print $title_attributes; ?>><?php print $label ?><span class="label-suffix">:&nbsp;</span></div>
	<?php endif; ?>
	<div class="field-items"<?php print $content_attributes; ?>>
		<?php foreach ($items as $delta => $item): ?>
			<div class="field-item <?php print $delta % 2 ? 'odd' : 'even'; ?>"<?php print $item_attributes[$delta]; ?>>
				<?php
					if (module_exists('eatlas_media_frame_filter')) {
						print eatlas_media_frame_decorate(
							$item,
							array(
								'showMetadata' => false,
								'media_style' => 'none'
							),
							url('media/' . $item['#file']->fid)
						);
					} else {
						print render($item);
					}
				?>
			</div>
		<?php endforeach; ?>
	</div>
</div>
