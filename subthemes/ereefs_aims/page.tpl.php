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

<header class="app-header">
  <div class="container">
    <a class="aims-logo" href="https://www.aims.gov.au" title="Australian Institute of Marine Science">
      <img src="<?php print base_path() . drupal_get_path('theme', 'ereefs_aims'); ?>/img/AIMS-logo.png" alt="Australian Institute of Marine Science logo" />
    </a>
    <div class="text-nowrap title">
      <img class="aims-logo-sml"
         src="<?php print base_path() . drupal_get_path('theme', 'ereefs_aims'); ?>/img/AIMS-logo-sml.png" alt="AIMS small logo" />
      AIMS eReefs
    </div>
  </div>
</header>

<?php
// Find the 3 blocks of the top menu and "hide" them from the header
$_appMenu = NULL;
$_visualisationMenu = NULL;
$_searchForm = NULL;
foreach ($page['header'] as $blockDelta => $block) {
  if (isset($block['#block']) && is_object($block['#block'])) {
    if ($block['#block']->delta === 'menu-aims-ereefs-app-menu') {
      $_appMenu = $block;
      hide($page['header'][$blockDelta]);
    } else if ($block['#block']->delta === 'menu-menu-aims-ereefs') {
      // AIMS eReefs Visualisation Portal menu
      $_visualisationMenu = $block;
      hide($page['header'][$blockDelta]);
    } else if ($block['#block']->module === 'search' && $block['#block']->delta === 'form') {
      $_searchForm = $block;
      hide($page['header'][$blockDelta]);
    }
  }
}
?>

<nav id="app-nav-bar">
  <div class="container">
    <div class="region region-header" id="app-navbar-nav">
      <?php print render($_appMenu); ?>
    </div>
  </div>
</nav>

<nav id="extraction-tool-nav-bar">
  <div class="container">
    <div class="region region-header" id="basic-navbar-nav">
      <a href="/ereefs-aims" class="navbar-title">AIMS eReefs Visualisation Portal</a>
      <?php print render($_visualisationMenu); ?>
    </div>
  </div>
</nav>

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
	</section>

	<footer>
		<div id="footer" class="nocontent">
			<?php print render($page['footer_print']); ?>
			<?php print render($page['footer']); ?>
		</div>
	</footer>
