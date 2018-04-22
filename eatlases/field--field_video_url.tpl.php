<?php
/**
 * Inspired of Drupal core field template:
 *     /modules/field/theme/field.tpl.php
 */
?>
<div class="<?php print $classes; ?>"<?php print $attributes; ?>>
	<?php if (!$label_hidden): ?>
		<div class="field-label"<?php print $title_attributes; ?>>Video</div>
	<?php endif; ?>
	<div class="field-items"<?php print $content_attributes; ?>>
		<?php foreach ($items as $delta => $item): ?>
			<?php
			$embedded_video_url = NULL;
			if ($item && isset($item['#element']) && isset($item['#element']['url'])) {
				$video_url = $item['#element']['url'];
				if ($video_url) {
					$embedded_video_url = str_replace(
						array('https://vimeo.com/',              'https://youtu.be/'),
						array('https://player.vimeo.com/video/', 'https://www.youtube.com/embed/'),
						$video_url
					);
				}
			}
			?>

			<?php if ($embedded_video_url): ?>
				<!--
				NOTE: The width x height are used to set the aspect ratio.
					The CSS make it 100% of the page width and the script
					"field_video_url_resize.js" ensure the iFrame keeps its
					aspect ratio.
				-->
				<iframe width="560" height="315" src="<?php print $embedded_video_url ?>" frameborder="0" allowfullscreen></iframe>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
</div>
