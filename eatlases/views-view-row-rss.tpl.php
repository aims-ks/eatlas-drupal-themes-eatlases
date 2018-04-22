<?php

/**
 * @file
 * Original: sites/all/modules/views/theme/views-view-row-rss.tpl.php
 *
 * @ingroup views_templates
 */

/**
 * This template is a copy of the original, but it modify the links of
 * nodes that has a "field_ea_rss_link" (i.e. it's a GeoRSS item),
 * so they point at the right URL instead of the internal copy.
 */

if (property_exists($row, 'nid')) {
	$node = node_load($row->nid);
	if (property_exists($node, 'field_ea_rss_link')) {
		$rss_link_field = field_get_items('node', $node, 'field_ea_rss_link');
		if ($rss_link_field) {
			$link = $rss_link_field[0]['value'];
		}
	}
}
?>
	<item>
		<title><?php print $title; ?></title>
		<link><?php print $link; ?></link>
		<description><?php print $description; ?></description>
		<?php print $item_elements; ?>
	</item>
