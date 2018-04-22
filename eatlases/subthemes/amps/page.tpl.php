<?php
/**
 * Original file: www/modules/system/page.tpl.php
 * NOTE: The page title has been moved into the node.tpl.php template for better flexibility.
 */

$theme_path = drupal_get_path('theme', 'amps');

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

// Determine the markup of the header div
// Default markup
$header_image_div_style = NULL;
if (isset($node) && $node) {
	// Do not mess with the preview image when it's not the preview image for the current node.
	// I.E. Do not touch it if the node is display in a list or an image slider for example.
	if ($node->nid === arg(1)) {
		$header_image_type_field = field_get_items('node', $node, 'field_header_image_type');
		if ($header_image_type_field) {
			$header_image_type_field_value = $header_image_type_field[0]['value'];
		} else {
			// The mandatory field has not been set (the node was created before the field)
			$header_image_type_field_value = 'branding';
		}

		// If something goes wrong (e.g. a required field is not set),
		//   the 'header_image_div' won't be displayed.
		switch($header_image_type_field_value) {

			case 'preview':
				$preview_field = field_get_items('node', $node, 'field_preview');
				if ($preview_field && isset($preview_field[0]) && isset($preview_field[0]['fid'])) {
					$preview_url = _cmr_theme_get_image_url($preview_field[0]['fid']);
					if ($preview_url) {
						$header_image_div_style = 'background-image: url(\'' . check_plain($preview_url) . '\')';
					}
				}
				break;

			case 'custom':
				$custom_field = field_get_items('node', $node, 'field_header_image');
				if ($custom_field && isset($custom_field[0]) && isset($custom_field[0]['fid'])) {
					$custom_url = _cmr_theme_get_image_url($custom_field[0]['fid']);
					if ($custom_url) {
						$header_image_div_style = 'background-image: url(\'' . check_plain($custom_url) . '\')';
					}
				}
				break;

			//case 'branding':
			//	break;
		}
	}
}

function _cmr_theme_get_image_url($fid, $style=NULL) {
	if ($fid !== NULL) {
		$image_file = file_load($fid);
		if ($image_file && property_exists($image_file, 'uri')) {
			$image_uri = $image_file->uri;
			if ($image_uri) {
				if ($style) {
					return image_style_url($style, $image_file->uri);
				} else {
					return file_create_url($image_file->uri);
				}
			}
		}
	}
	return NULL;
}

$content = NULL;
if (isset($page['content']['system_main']['nodes'][arg(1)])) {
	$content = &$page['content']['system_main']['nodes'][arg(1)];
}
?>
	<header>
		<div class="header branding-background nocontent">
			<?php
			// Find the 3 blocks of the top menu and "hide" them from the header
			$_mainMenuLeft = NULL;
			$_mainMenuRight = NULL;
			$_searchForm = NULL;
			foreach($page['header'] as $blockDelta => $block) {
				if (isset($block['#block']) && is_object($block['#block'])) {
					if ($block['#block']->delta === 'menu-branding-menu-default') {
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

			<div class="amps-logo"><a href="/amps"><img src="/<?php print $theme_path; ?>/img/logo-marine-parks.svg" /></a></div>

			<?php
				print render($page['header']);
				print render($page['header_print']);
				print render($_searchForm);
			?>
		</div>
		<?php if ($header_image_div_style): ?>
			<div class="amps-header-image" style="<?php print $header_image_div_style; ?>">
				<div class="amps-header-title">
					<?php if ($title): ?>
						<?php print render($title_prefix); ?>
						<h1 class="page-title"><?php print $title ?></h1>
						<?php print render($title_suffix); ?>
						<?php $title = FALSE; ?>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>
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
					$fieldsInSidebarFirst = array();

					if ($content && module_exists('eatlas_field_extra_attributes') && function_exists('eatlas_field_extra_attributes_get_content_field_names')) {
						$fieldsInSidebarFirst = eatlas_field_extra_attributes_get_content_field_names($content, 'sidebar_first');
					}

					$hasSidebarFirst = (isset($page['sidebar_first']) && $page['sidebar_first']) || !empty($fieldsInSidebarFirst);
				?>

				<?php if ($hasSidebarFirst): ?>
					<div id="sidebar-first" class="sidebar">
						<?php
							if ($content) {
								foreach($fieldsInSidebarFirst as $field_name) {
									print render($content[$field_name]);
									hide($content[$field_name]);
								}
							}

							print render($page['sidebar_first']);
						?>
					</div>
				<?php endif; ?>

				<div id="page-content" class="page-content_<?php print $hasSidebarFirst ? 'with-column' : 'full-width'; ?>">
					<?php if ($title): ?>
						<?php print render($title_prefix); ?>
						<h1 class="page-title"><?php print $title ?></h1>
						<?php print render($title_suffix); ?>
					<?php endif; ?>

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
