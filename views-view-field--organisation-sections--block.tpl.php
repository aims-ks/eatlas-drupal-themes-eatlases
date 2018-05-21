<?php
// Original: sites/all/modules/views/theme/views-view-field.tpl.php
/**
 * Variables available:
 *     $view: The view object
 *     $field: The field handler object that can process the input
 *     $row: The raw SQL result that can be used
 *     $output: The processed output that will normally be used.
 */

if (isset($row->nid) && is_numeric($row->nid)) {
	$logo = eatlases_get_organisation_logo($row->nid);
	if ($logo) {
		print $logo;
	} else {
		print eatlases_get_organisation_link($row->nid);
	}
} else if (isset($output)) {
	// Fallback - default behaviour
	print $output;
}
?>
