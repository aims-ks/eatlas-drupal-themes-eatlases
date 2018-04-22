<?php

/**
 * @file
 * eAtlas view template to display a rows in a grid.
 *
 * - $rows contains a nested array of rows. Each row contains an array of
 *   columns.
 *
 * @ingroup views_templates
 * 
 * Original file:
 *   sites/all/modules/views/theme/views-view-grid.tpl.php
 * 
 * NOTE: The module View define a grid template which display
 *   its items in a HTML table. This is not suitable for responsive
 *   design. This template simply display all items in a Flex
 *   layout.
 * 
 * This template ignores the number of columns setting.
 */
?>
<?php if (!empty($title)) : ?>
	<h3><?php print $title; ?></h3>
<?php endif; ?>

<?php if (!empty($caption)) : ?>
	<h4><?php print $caption; ?></h4>
<?php endif; ?>

<div class="eatlases_view_grid <?php print $class; ?>"<?php print $attributes; ?>>
	<tbody class="Bouyahh">
		<?php foreach ($rows as $row_number => $columns): ?>
			<?php foreach ($columns as $column_number => $item): ?>
				<?php if ($item): ?>
					<div <?php if ($column_classes[$row_number][$column_number]) { print 'class="' . $column_classes[$row_number][$column_number] .'"';  } ?>>
						<?php print $item; ?>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
		<?php endforeach; ?>
	</tbody>
</div>
