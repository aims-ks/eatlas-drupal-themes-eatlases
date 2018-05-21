<?php

/**
 * Display the Dataset Teaser's authors (aka Mini Record)
 *
 * Variables available:
 * - $view: The view object
 * - $field: The field handler object that can process the input
 * - $row: The raw SQL result that can be used
 * - $output: The processed output that will normally be used.
 *
 * When fetching output from the $row, this construct should be used:
 * $data = $row->{$field->field_alias}
 *
 * The above will guarantee that you'll always get the correct data,
 * regardless of any changes in the aliasing that might happen if
 * the view is modified.
 */

$values = $row->field_field_ea_rss_authors
?>

<?php
	$html_arr = array();
	foreach($values as $value) {

		// Get the author's organisation name
		$organisation_name = NULL;
		$entity = isset($value['rendered']['#options']['entity']) ? $value['rendered']['#options']['entity'] : NULL;
		if ($entity && isset($entity->field_organisation[LANGUAGE_NONE][0]['nid'])) {
			$organisation_id = $entity->field_organisation[LANGUAGE_NONE][0]['nid'];
			$organisation = node_load($organisation_id);
			if ($organisation) {
				if (isset($organisation->field_abbr[LANGUAGE_NONE][0]['value'])) {
					$organisation_name = $organisation->field_abbr[LANGUAGE_NONE][0]['value'];
				} else if (isset($organisation->field_name[LANGUAGE_NONE][0]['value'])) {
					$organisation_name = $organisation->field_name[LANGUAGE_NONE][0]['value'];
				} else if (isset($organisation->title)) {
					$organisation_name = $organisation->title;
				}
			}
		}

		// Add the organisation's name to the author's name
		if ($organisation_name) {
			$value['rendered']['#title'] .= " ($organisation_name)";
		}

		// Render the author as HTML, store the result in a array.
		$html_arr[] = render($value['rendered']);
	}

	// Print the authors
	if (!empty($html_arr)) {
		print implode(', ', $html_arr);
	}
?>
