<?php
/**
 * @file
 * Original file: sites/all/modules/views/theme/views-view-field.tpl.php
 * This template is used to print a single dataset title, linked to the proper URL.
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

$can_edit = FALSE;

$new_output = NULL;
$edit_link = NULL;
if (property_exists($row, '_field_data') && isset($row->_field_data['nid']['entity_type']) && isset($row->_field_data['nid']['entity'])) {
	$label = $field->original_value;

	$entity_type = $row->_field_data['nid']['entity_type'];
	$entity = $row->_field_data['nid']['entity'];

	// I don't see why the entity type could be something else than 'node',
	// but just to be careful...
	if ($entity_type === 'node') {
		$edit_link = url('node/' . $row->nid . '/edit');
		$can_edit = node_access('update', $entity);
	}

	//$link = $entity->field_ea_rss_link[LANGUAGE_NONE][0]['value'];
	$link_field = field_get_items($entity_type, $entity, 'field_ea_rss_link');
	if ($link_field) {
		$link = $link_field[0]['value'];

		$new_output = '<a href="'.$link.'">'.$label.'</a>';
	}
}

if ($new_output) {
	print $new_output;
} else {
	// Fallback - Default behaviour
	print $output;
}
?>
