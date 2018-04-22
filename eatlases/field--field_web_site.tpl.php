<?php
/**
 * Inspired of Drupal core field template:
 *     /modules/field/theme/field.tpl.php
 */
?>
<div class="<?php print $classes; ?>"<?php print $attributes; ?>>
	<div class="field-items"<?php print $content_attributes; ?>>
		<?php foreach ($items as $delta => $item): ?>
			<?php
				$url = NULL;
				if (isset($item['#markup']) && $item['#markup']) {
					$url = $item['#markup'];
				}
				if ($url) {
					print l($label, $url, array('attributes' => array('target' => '_blank')));
				} else {
					print render($item);
				}
			?>
		<?php endforeach; ?>
	</div>
</div>
