<?php
/**
 * Inspired of Drupal core field template:
 *     /modules/field/theme/field.tpl.php
 *
 * NOTE: Drupal encode HTML. The attribution field allow HTML.
 *   I can't find a way to prevent Drupal from encoding HTML so
 *   I opted for the lazy "html_entity_decode" solution,
 *   reprocessed using "filter_xss" just to be safe.
 */
?>
<div class="<?php print $classes; ?>"<?php print $attributes; ?>>
	<?php if (!$label_hidden): ?>
		<div class="field-label"<?php print $title_attributes; ?>>Video</div>
	<?php endif; ?>
	<div class="field-items"<?php print $content_attributes; ?>>
		<?php foreach ($items as $delta => $item): ?>
			<div class="video-attributions">
				<?php print filter_xss(
						// Allow HTML (revert encoded string back into HTML)
						html_entity_decode(render($item), ENT_QUOTES),
						// Allowed HTMl tags
						array('a', 'em', 'strong', 'cite', 'blockquote', 'code', 'ul', 'ol', 'li', 'dl', 'dt', 'dd', 'i', 'b')
				); ?>
			</div>
		<?php endforeach; ?>
	</div>
</div>
