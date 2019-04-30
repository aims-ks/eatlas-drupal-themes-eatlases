<?php
/**
 * Original file: www/modules/system/page.tpl.php
 * NOTE: The page title has been moved into the node.tpl.php template for better flexibility.
 */

$theme_path = drupal_get_path('theme', 'amps');
$context = new EAtlas_spatial_publisher_template_context($node, NULL);
?>

	<header>
		<div class="header nocontent">
			<?php
			// Find the 3 blocks of the top menu and "hide" them from the header
			$_searchForm = NULL;
			foreach($page['header'] as $blockDelta => $block) {
				if (isset($block['#block']) && is_object($block['#block'])) {
					if ($block['#block']->module === 'search' && $block['#block']->delta === 'form') {
						$_searchForm = $block;
						hide($page['header'][$blockDelta]);
					}
				}
			}
			?>

			<div class="amps-logo"><a href="/amps"><img src="/<?php print $theme_path; ?>/img/logo-marine-parks.svg" /></a></div>
			<div class="amps-home"><a href="/amps"><img src="/<?php print $theme_path; ?>/img/home-icon.svg" /></a></div>

			<?php print render($page['header']); ?>
			<?php print render($page['header_print']); ?>
			<?php print render($_searchForm); ?>
		</div>
	</header>

	<section>
		<div class="navigation_map_node">
			<!-- [View] [Edit] [Outline] tabs (for authenticated users) -->
			<?php if ($tabs): ?>
				<div id="tabs-wrapper" class="clearfix">
				<?php print render($tabs); ?></div>
			<?php endif; ?>

			<!-- Floating left (for authenticated users) -->
			<?php if ($page['sidebar_float']): ?>
				<aside>
					<div class="sidebar_float">
						<?php print render($page['sidebar_float']); ?>
					</div>
				</aside>
			<?php endif; ?>
			<!-- end Floating left -->

			<a id="main-content"></a>

			<?php if ($action_links): ?><ul class="action-links"><?php print render($action_links); ?></ul><?php endif; ?>

			<script>
				var extra_content =
					<?php
						// Put page blocks HTML into a JavaScript variable
						// (they will be added to the right panel)
						foreach (array_keys($page['content']) as $key) {
							if ($key !== 'system_main' && $key[0] !== '#') {
								print json_encode(drupal_render($page['content'][$key])) . ' + ';
							}
						}
					?>
					'';
			</script>

			<?php print render($page['content']); ?>
		</div>

	</section>
