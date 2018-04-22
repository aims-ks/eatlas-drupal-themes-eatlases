<?php ?>
<div id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?>"<?php print $attributes; ?>>

	<?php print $user_picture; ?>

	<?php if ($display_submitted): ?>
		<div class="submitted">
			<?php print $submitted; ?>
		</div>
	<?php endif; ?>

	<div class="content"<?php print $content_attributes; ?>>
		<?php
			print render($content['field_position']);

			if (isset($content['field_preview']['#items'][0])) {
				$preview_image = $content['field_preview']['#items'][0];

				if (module_exists('eatlas_media_frame_filter')) {
					print '<div class="ereefs-member-photo">';
					print eatlas_media_frame_decorate(
						array('#item' => $preview_image),
						array(
							'showMetadata' => false,
							'styleName' => 'ereefs_member',
							'media_style' => 'wikipedia'
						)
					);
					print '</div>';
					hide($content['field_preview']);
				}
			}

			// We hide the comments and links now so that we can render them later.
			hide($content['comments']);
			hide($content['links']);
			print render($content);
		?>
	</div>

	<?php print render($content['links']); ?>

	<?php print render($content['comments']); ?>
</div>
