<?php
/**
 * Inspired of Drupal core field template:
 *     /modules/field/theme/field.tpl.php
 */
?>
<div class="<?php print $classes; ?>"<?php print $attributes; ?>>
	<?php if (!$label_hidden): ?>
		<div class="field-label"<?php print $title_attributes; ?>><?php print $label ?><span class="label-suffix">:&nbsp;</span></div>
	<?php endif; ?>
	<div class="field-items"<?php print $content_attributes; ?>>
		<?php foreach ($items as $delta => $item) {
			print "<div class=\"field-item ".($delta % 2 ? 'odd' : 'even')."\"${item_attributes[$delta]}>";
				$org_id = NULL;
				if (isset($item['#markup']) && is_numeric($item['#markup'])) {
					// The item is an ID
					$org_id = $item['#markup'];
				} else if (isset($item['#options']['entity']->nid)) {
					// The item contains a reference to the organisation obj, which contains its ID
					$org_id = $item['#options']['entity']->nid;
				}

				if ($org_id !== NULL) {
					$logo = eatlases_get_organisation_logo($org_id);
					if ($logo) {
						print $logo;
					} else {
						print eatlases_get_organisation_link($org_id);
					}
				} else {
					// Default behaviour
					print render($item);
				}
			print '</div>';
		} ?>
	</div>
</div>

