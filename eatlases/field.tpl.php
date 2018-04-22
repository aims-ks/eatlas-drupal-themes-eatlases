<?php
/**
 * Inspired of Drupal core field template:
 *     /modules/field/theme/field.tpl.php
 * NOTE The only change here is the addition of a span around the label column (":"), to be able to remove it using CSS.
 */
?>
<div class="<?php print $classes; ?>"<?php print $attributes; ?>>
	<?php if (!$label_hidden): ?>
		<div class="field-label"<?php print $title_attributes; ?>><?php print $label ?><span class="label-suffix">:&nbsp;</span></div>
	<?php endif; ?>
	<div class="field-items"<?php print $content_attributes; ?>>
		<?php foreach ($items as $delta => $item): ?>
			<div class="field-item <?php print $delta % 2 ? 'odd' : 'even'; ?>"<?php print $item_attributes[$delta]; ?>><?php print render($item); ?></div>
		<?php endforeach; ?>
	</div>
</div>
