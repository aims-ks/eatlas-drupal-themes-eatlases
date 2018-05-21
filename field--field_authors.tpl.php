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
		<?php foreach ($items as $delta => $item): ?>
			<?php if (isset($item['#options']['entity'])): ?>
				<?php
					$person = $item['#options']['entity'];

					$link = 'node/' . $person->nid;

					$photo = NULL;
					$photoValues = field_get_items('node', $person, 'field_photos');
					if ($photoValues) {
						$photoValue = field_view_value('node', $person, 'field_photos', $photoValues[0]);
						if (module_exists('eatlas_media_frame_filter')) {
							$photo = eatlas_media_frame_decorate(
								$photoValue,
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
							$photo = l('<img src="' . image_style_url('m_icon', $photoValue['#file']->uri) . '" />', $link, array('html' => TRUE));
						}
					} else {
						// NOTE: path_to_theme() return the theme currently used to theme the element instead of "this" theme.
						$noPhotoUrl = base_path() . drupal_get_path('theme', 'eatlases') . '/img/no-photo.png';
						$photo = l('<img src="' . $noPhotoUrl . '" />', $link, array('html' => TRUE));;
					}

					$personFullName = eatlases_get_person_name($person);
					$linkedPerson = l($personFullName, $link);

					// TODO keep track of author history... It's not a good idea to display the current working history, it may credit the wrong organisation.
					//$organisationValues = field_get_items('node', $person, 'field_organisation');
				?>
				<div class="field-item field-item-author <?php print $delta % 2 ? 'odd' : 'even'; ?>"<?php print $item_attributes[$delta]; ?>>
					<?php if ($photo): ?>
						<div class="photo">
							<?php print $photo; ?>
						</div>
					<?php endif; ?>

					<div class="info">
						<div class="author-name">
							<?php if ($linkedPerson): ?>
								<?php print $linkedPerson; ?>
							<?php endif; ?>
						</div>

						<?php /* ?>
						<?php if ($organisationValues): ?>
							<div class="organisations">
								<?php foreach ($organisationValues as $delta => $item): ?>
									<?php
										// We can't use field_view_value here because it won't apply the "organisation" field template (those templates apply to the fields, not the values)
										//print render(field_view_value('node', $person, 'field_organisation', $organisationValue));

										print '<div class="field-item '.($delta % 2 ? 'odd' : 'even').'">';
											if (isset($item['nid'])) {
												// The item contains a reference to the organisation obj, which contains its ID
												print eatlases_get_organisation_link($item['nid']); 
											} else {
												// Default behaviour
												print render($item);
											}
										print '</div>';

									?>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
						<?php */ ?>
					</div>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
</div>
