<?php
/**
 * Original file: www/modules/system/page.tpl.php
 * NOTE: The page title has been moved into the node.tpl.php template for better flexibility.
 */

if (isset($node)) {
	// Fix the title of some node type (without having to override the node template)
	if (property_exists($node, 'type')) {
		switch($node->type) {
			case 'organisation':
			case 'organisation_section':
				$organisationNameValues = field_get_items('node', $node, 'field_name');
				if ($organisationNameValues) {
					$organisationName = render(field_view_value('node', $node, 'field_name', $organisationNameValues[0]));
					if ($organisationName) {
						// Set the page title (visible on the browser window title bar)
						// This function can also be used to change the breadcrumb, but it's too late for that. It has already been printed.
						$title = drupal_set_title($organisationName);
					}
				}
				break;
			case 'person':
				$personFullName = eatlases_get_person_name($node);
				if ($personFullName) {
					$title = drupal_set_title($personFullName);
				}
				break;
		}
	}
}

?>
	<header>
		<div class="header nocontent">
			<?php
			// Find the 3 blocks of the top menu and "hide" them from the header
			$_mainMenuLeft = NULL;
			$_mainMenuRight = NULL;
			$_searchForm = NULL;
			foreach($page['header'] as $blockDelta => $block) {
				if (isset($block['#block']) && is_object($block['#block'])) {
					if ($block['#block']->delta === 'main-menu') {
						$_mainMenuLeft = $block;
						hide($page['header'][$blockDelta]);
					} else if ($block['#block']->subject === 'Main menu right') {
						$_mainMenuRight = $block;
						hide($page['header'][$blockDelta]);
					} else if ($block['#block']->module === 'search' && $block['#block']->delta === 'form') {
						$_searchForm = $block;
						hide($page['header'][$blockDelta]);
					}
				}
			}
			?>
			<div id="main-menus">
				<div class="main-menus-content">
					<div class="menu-left">
						<?php print render($_mainMenuLeft); ?>
					</div>
					<div class="menu-right">
						<?php print render($_searchForm); ?>
						<?php print render($_mainMenuRight); ?>
					</div>
				</div>
			</div>

			<?php print render($page['header']); ?>
			<?php print render($page['header_print']); ?>
		</div>
	</header>

	<section>
		<!-- Banner (but only show in view mode, not in edit mode) -->
		<?php if ($page['banner'] && !arg(2)): ?>
			<div class="banner">
				<?php print render($page['banner']); ?>
			</div>
		<?php endif; ?>

		<!-- Floating left -->
		<?php if ($page['sidebar_float']): ?>
			<aside>
				<div class="sidebar_float">
					<?php print render($page['sidebar_float']); ?>
				</div>
			</aside>
		<?php endif; ?>
		<!-- end Floating left -->

		<div id="wrapper">
			<div id="container">
				<?php
					// Breadcrumbs - Disabled (not usefull for the eatlas)
					//print $breadcrumb;
				?>

				<a id="main-content"></a>
				<?php if ($tabs): ?>
					<div id="tabs-wrapper" class="clearfix">
					<?php print render($tabs); ?></div>
				<?php endif; ?>

				<?php print render($tabs2); ?>
				<?php print $messages; ?>
				<?php print render($page['help']); ?>
				<?php if ($action_links): ?><ul class="action-links"><?php print render($action_links); ?></ul><?php endif; ?>

				<?php
					// This snipet is used to move some fields from the main
					// page content to the sidebar (the left column)
					// using the "eatlas_field_extra_attributes" module to determine
					// if the field has to be moved.
					$content = NULL;
					$fieldsInSidebarFirst = array();

					if (isset($page['content']['system_main']['nodes'][arg(1)])) {
						$content = &$page['content']['system_main']['nodes'][arg(1)];

						if (module_exists('eatlas_field_extra_attributes') && function_exists('eatlas_field_extra_attributes_get_content_field_names')) {
							$fieldsInSidebarFirst = eatlas_field_extra_attributes_get_content_field_names($content, 'sidebar_first');
						}
					}

					$hasSidebarFirst = (isset($page['sidebar_first']) && $page['sidebar_first']) || !empty($fieldsInSidebarFirst);
				?>

				<?php if ($hasSidebarFirst): ?>
					<div id="sidebar-first" class="sidebar">
						<?php
							foreach($fieldsInSidebarFirst as $field_name) {
								print render($content[$field_name]);
								hide($content[$field_name]);
							}

							print render($page['sidebar_first']);
						?>
					</div>
				<?php endif; ?>

				<div id="page-content" class="page-content_<?php print $hasSidebarFirst ? 'with-column' : 'full-width'; ?>">
					<?php print render($title_prefix); ?>
					<?php if ($title): ?>
						<h1 class="page-title"><?php print $title ?></h1>
					<?php endif; ?>
					<?php print render($title_suffix); ?>

					<?php print render($page['content']); ?>
					<div style="clear:both;"></div>
				</div>
				<?php print $feed_icons ?>

				<?php if ($page['sidebar_second']): ?>
					<div id="sidebar-second" class="sidebar">
						<?php print render($page['sidebar_second']); ?>
					</div>
				<?php endif; ?>

			</div> <!-- /#container -->
		</div> <!-- /#wrapper -->
    <footer>
      <div id="footer" class="nocontent">
        <div id="wrapper">
          <div id="container">
            <?php print render($page['footer_print']); ?>
            <?php print render($page['footer']); ?>
          </div>
        </div>
      </div>
    </footer>
	</section>
