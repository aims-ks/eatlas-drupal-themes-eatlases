<?php
/**
 * NOTE: Print a field
 *     $node = node_load($nid);
 *     $field = field_get_items('node', $node, 'field_name');
 *     $output = field_view_value('node', $node, 'field_name', $field[0]);
 *     print render($output);
 * OR
 *     print render(field_view_field('node', $node, 'body', ''));
 *     print render(field_view_field('node', $node, 'body', 'teaser'));
 *
 * Add CSS for IE only:
 *   drupal_add_css(path_to_theme() . '/css/ie.css', array('group' => CSS_THEME, 'browsers' => array('IE' => 'lte IE 7', '!IE' => FALSE), 'preprocess' => FALSE));
 */

// Implements: hook_form_FORM_ID_alter(&$form, &$form_state, $form_id)
// Inspired on: https://www.drupal.org/node/154137
function eatlases_form_search_block_form_alter(&$form, &$form_state, $form_id) {
	$form['search_block_form']['#size'] = 40;  // define size of the textfield
	// Alternative text, shown on mouse over
	$form['search_block_form']['#attributes']['title'] = t('Search for articles, metadata, images, etc.'); 
	// HTML5 Placeholder attribute
	$form['search_block_form']['#attributes']['placeholder'] = t('Articles, metadata, images, etc.');

	// Increase font size
	$form['search_block_form']['#attributes']['style'] = 'font-size: 1em;';
}


/**
 * Implements theme_menu_link(). 
 * - Allow images as menu items.
 *   Just supply an image URL in the menu title. The title will be replaced
 *   with an img tag. The description is used as alt text and title.
 *     Reference: http://chrisshattuck.com/blog/how-use-images-menu-items-drupal-simple-preprocessing-function
 * - Disable sub-menu links for mobile devices
 */
function eatlases_menu_link($variables) {
	$element = &$variables['element'];

	if (isset($element['#title']) && $element['#title']) {
		$pattern = '/\S+\.(png|gif|jpg|svg)\b/i';
		if (preg_match($pattern, $element['#title'], $matches) > 0) {
			$element['#title'] = preg_replace(
				$pattern,
				'<img alt="' . $element['#localized_options']['attributes']['title'] . '" src="' . url($matches[0]) . '" />',
				$element['#title']
			);
			$element['#localized_options']['html'] = TRUE;
			$element['#attributes']['class'][] = 'menu-image-item';
		}
	}

	// Do not render the link on parent menu items that leads to the 404 page.
	// Original:
	//     File: www/includes/menu.inc
	//     Function: theme_menu_link
	//     Line: 1625
	$element = $variables['element'];
	$href_alias = drupal_get_path_alias($element['#href']);

	if ($element['#below'] && $href_alias === '404') {
		$sub_menu = drupal_render($element['#below']);
		// Add a useless "a" tag, for more consistancy (works better with existing CSS)
		$output = '<a>' . $element['#title'] . '</a>';
		return '<li' . drupal_attributes($element['#attributes']) . '>' . $output . $sub_menu . "</li>\n";
	} else {
		return theme_menu_link($variables);
	}
}

/**
 * Implements hook_preprocess_node
 * API:
 *     https://api.drupal.org/api/drupal/modules!system!theme.api.php/function/hook_preprocess_HOOK/7
 * Add the "node-prepress" CSS class to the page body element (if applicable)
 */
function eatlases_preprocess_node(&$variables) {
	$isPrepress = FALSE;
	if (isset($variables['node'])) {
		$node = $variables['node'];
		$isPrepress = _eatlases_is_prepress($node);
	}
	if ($isPrepress) {
		$variables['classes_array'][] = 'node-prepress';
	}
}

function _eatlases_is_prepress($node) {
	if ($node === NULL || !is_object($node)) {
		return FALSE;
	}
	if (property_exists($node, 'field_draft') &&
			isset($node->field_draft[LANGUAGE_NONE][0]['value']) &&
			$node->field_draft[LANGUAGE_NONE][0]['value']) {
		return TRUE;
	}
	return FALSE;
}

/**
 * Implements hook_preprocess_block
 * API:
 *     https://api.drupal.org/api/drupal/modules!system!theme.api.php/function/hook_preprocess_HOOK/7
 * Example:
 *     https://api.drupal.org/api/drupal/modules!block!block.module/function/template_preprocess_block/7
 *
 * Add a wrapper CSS class to view blocks that defined a CSS class.
 *
 * Example:
 *     View with CSS class "test" will be contained in a block with
 *     the CSS class "test-wrapper".
 * See:
 *     http://drupal.stackexchange.com/questions/46317/how-to-add-css-classes-to-a-views-generated-block-not-to-its-result-output-th/118669#118669
 */
function eatlases_preprocess_block(&$variables) {
	$default_display_id = 'default';
	// Trying to access the fields:
	//     $display_id =      $variables['elements']['#views_contextual_links_info']['views_ui']['view_display_id']
	//     $default_display = $variables['elements']['#views_contextual_links_info']['views_ui']['view']->display['default']->display_options['css_class']
	//     $display =         $variables['elements']['#views_contextual_links_info']['views_ui']['view']->display[$display_id]->display_options['css_class']
	if (isset($variables['elements']['#views_contextual_links_info']['views_ui'])) {
		$view_ui = $variables['elements']['#views_contextual_links_info']['views_ui'];
		if (isset($view_ui['view_display_id'])) {
			$display_id = $view_ui['view_display_id'];
			if (isset($view_ui['view']) && property_exists($view_ui['view'], 'display') && isset($view_ui['view']->display[$display_id])) {

				$default_css_class = NULL;
				if (isset($view_ui['view']->display[$default_display_id])) {
					$default_display = $view_ui['view']->display[$default_display_id];
					if (property_exists($default_display, 'display_options') && isset($default_display->display_options['css_class'])) {
						$default_css_class = $default_display->display_options['css_class'];
					}
				}

				$view_css_class = NULL;
				$display = $view_ui['view']->display[$display_id];
				if (property_exists($display, 'display_options') && isset($display->display_options['css_class'])) {
					$view_css_class = $display->display_options['css_class'];
				}

				$css_class = $view_css_class ? $view_css_class : $default_css_class;
				if ($css_class) {
					$variables['classes_array'][] = "$css_class-wrapper";
				}
			}
		}
	}
}

/**
 * Get the organisation link, with its top parents.
 * Example:
 *     e-Atlas (AIMS)
 * NOTE: This custom function is used in some templates:
 *     field--field_author.tpl.php
 *     field--field_organisation.tpl.php
 */
function eatlases_get_organisation_link($organisation_id) {
	$organisation = node_load($organisation_id);
	if ($organisation && property_exists($organisation, 'nid')) {
		$org_nid = $organisation->nid;
		$org_name = property_exists($organisation, 'field_name') && isset($organisation->field_name[LANGUAGE_NONE][0]['value']) ?
				$organisation->field_name[LANGUAGE_NONE][0]['value'] :
				$organisation->title;

		// If it is an organisation; get the organisation abbreviation
		// and place it after the organisation name:
		// Example:
		//     Organisation's name => Australian Institute of Marine Science
		//     Organisation's abbreviation => AIMS
		//     Result => Australian Institute of Marine Science (AIMS)
		if (!property_exists($organisation, 'field_parent') || $organisation->field_parent === NULL) {
			$org_abbr = property_exists($organisation, 'field_abbr') && isset($organisation->field_abbr[LANGUAGE_NONE][0]['value']) ?
					$organisation->field_abbr[LANGUAGE_NONE][0]['value'] :
					$organisation->title;
			if ($org_abbr !== $org_name) {
				$org_name .= "<span class=\"abbr\"> ($org_abbr)</span>";
			}
		}
		$org_link = l($org_name, 'node/' . $org_nid, array('html' => TRUE));
		$link = $org_link;

		// If it is an organisation section; get the parent organisation
		// abbreviation and place it after the organisation section name:
		// Example:
		//     Organisation section's name => e-Atlas
		//     Organisation section's parent abbreviation => AIMS
		//     Result => e-Atlas (AIMS)
		if (property_exists($organisation, 'field_parent') && isset($organisation->field_parent[LANGUAGE_NONE][0]['nid'])) {
			$root_org = $organisation;
			// Find the root organisation
			while (property_exists($root_org, 'field_parent') && isset($root_org->field_parent[LANGUAGE_NONE][0]['nid'])) {
				$root_org = node_load($organisation->field_parent[LANGUAGE_NONE][0]['nid']);
			}
			$root_org_nid = $root_org->nid;
			$root_org_name = property_exists($root_org, 'field_abbr') && isset($root_org->field_abbr[LANGUAGE_NONE][0]['value']) ?
					$root_org->field_abbr[LANGUAGE_NONE][0]['value'] :
					$root_org->title;
			$root_org_link = l($root_org_name, "node/" . $root_org_nid, array('html' => TRUE));

			$link .= "<span class=\"root-abbr\"> ($root_org_link)</span>";
		}

		return $link;
	}
	return NULL;
}

/**
 * Get the organisation logos (linked)
 * NOTE: This custom function is used in some templates:
 *     field--field_art_organisation.tpl.php
 */
function eatlases_get_organisation_logo($organisation_id) {
	$organisation = node_load($organisation_id);
	if ($organisation && property_exists($organisation, 'nid') && isset($organisation->field_logos[LANGUAGE_NONE])) {
		$linked_logos = array();
		$org_nid = $organisation->nid;
		$org_logos = $organisation->field_logos[LANGUAGE_NONE];
		if ($org_logos) {
			// Return the last logo:
			//     Go through all logos, starting from the last one,
			//     and return the first one that works.
			foreach (array_reverse($org_logos) as $delta => $org_logo) {
				$linked_logo = NULL;
				if (isset($org_logo['uri']) && file_exists($org_logo['uri'])) {
					if (module_exists('eatlas_media_frame_filter')) {
						$linked_logo = eatlas_media_frame_decorate(
							array (
								'#item' => $org_logo
							),
							array(
								'showMetadata' => false,
								'styleName' => 'm_logo',
								'media_style' => 'none'
							),
							url('node/' . $org_nid),
							NULL,
							FALSE
						);
					} else {
						$linked_logo = l('<img src="' . image_style_url('m_small', $org_logo['uri']) . '" />', 'node/' . $org_nid, array('html' => TRUE));
					}
				}
				if ($linked_logo) {
					return $linked_logo;
				}
			}
		}
	}
	return NULL;
}

function eatlases_get_person_name($person) {
	$title = NULL;
	$titleValues = field_get_items('node', $person, 'field_title');
	if ($titleValues) {
		$title = field_view_value('node', $person, 'field_title', $titleValues[0]);
	}
	return ($title ? render($title) . ' ' : '') . $person->title;
}

function _eatlases_get_published_timestamp($node) {
	if ($node === NULL || !is_object($node)) {
		return NULL;
	}
	if (property_exists($node, 'field_published_date')) {
		return _eatlases_date_field_to_timestamp($node->field_published_date);
	}
	return NULL;
}

function _eatlases_get_created_timestamp($node) {
	if ($node === NULL || !is_object($node)) {
		return NULL;
	}
	if (property_exists($node, 'created')) {
		return $node->created;
	}
	return NULL;
}

function _eatlases_get_updated_timestamp($node) {
	if ($node === NULL || !is_object($node)) {
		return NULL;
	}
	if (property_exists($node, 'changed')) {
		return $node->changed;
	}
	return NULL;
}

function _eatlases_date_field_to_timestamp($field) {
	if ($field === NULL || !is_array($field)) {
		return NULL;
	}
	if (isset($field[LANGUAGE_NONE][0]['value']) && $field[LANGUAGE_NONE][0]['value']) {
		return strtotime($field[LANGUAGE_NONE][0]['value']);
	}
	return NULL;
}

/**
 * Definition of the aggregator block template (on front page).
 * NOTE: Aggregator page is defined in: aggregator-item.tpl.php
 */
function eatlases_aggregator_block_item($variables) {
	global $user;
	$item = $variables['item'];

	$link = check_url($item->link);
	if (_get_feed_url($item->fid)) {
		// NERP Project outputs feed
		$output = '<div class="rss-item project-feed">';
		$output .= '<p class="rss-title"><a href="'. $link .'" target="_blank">'. check_plain($item->title) . '</a></p>';
		if ($item->timestamp != null) {
			$publishedDate = $item->timestamp;

			$output .= '<p class="rss-date">Posted on <span>' . date('j F Y', $publishedDate) . '</span></p>';
		}

		$output .= '</div>';
	} else {
		// Normal RSS feed
		$output = '<div class="rss-item">';
		if ($user->uid && module_exists('blog') && user_access('create blog entries')) {
			if ($image = theme('image', 'misc/blog.png', t('blog it'), t('blog it'))) {
				$output .= '<div class="icon">'. l($image, 'node/add/blog', array('attributes' => array('title' => t('Comment on this news item in your personal blog.'), 'class' => 'blog-it'), 'query' => "iid=$item->iid", 'html' => TRUE)) .'</div>';
			}
		}

		// Display the external link to the item.
		$output .= '<p class="rss-title"><a href="'. $link .'" target="_blank">'. check_plain($item->title) . '</a></p>';

		if ($item->timestamp != null) {
			$publishedDate = $item->timestamp;

			$output .= '<p class="rss-date">Posted on <span>' . date('j F Y', $publishedDate) . '</span></p>';
		}

		if ($item->description != null) {
			// The aggregator_filter_xss has probably already been applied, but better be safe.
			$description = aggregator_filter_xss($item->description);

			// text_summary only cut the description on </p> or <br>. That's not elegant.
			//$teaser = text_summary($description);

			// truncate_utf8 do a good job but also break the HTML.
			// _filter_htmlcorrector fix the broken HTML.
			// truncate_utf8 BUG:
			//     Wordsafe has to be set to False because there is an issue with this parameter.
			//     See: https://drupal.org/node/1712106#comment-7943037
			//     API: https://api.drupal.org/api/drupal/includes%21unicode.inc/function/truncate_utf8/7
			// aggregator_teaser_length: Configurable using Drupal admin:
			//     Configuration -> Feed aggregator -> Settings (tab) -> Length of trimmed description
			//     Keep 600 as default to be consistent with Drupal default.
			$teaser_size = variable_get('aggregator_teaser_length', 600);
			$teaser = truncate_utf8(strip_tags($description, "<p><br><i><em><b><strong><img>"), $teaser_size, FALSE, TRUE);
			$teaser = _filter_htmlcorrector($teaser);

			$teaser .= ' <a href="' . $link . '" target="_blank"> ' . t('read more') . '</a>';
			
			// NOTE: The teaser can not be placed into a <p> since it may contains block elements.
			$output .= '<div class="rss-teaser">' . $teaser . '</div>';
		}

		// Fix floating element allignment
		$output .= '<div style="clear: both;"></div>';

		$output .= '</div>';
	}

	return $output;
}

/**
 * theme_more_link
 * Redefine where the 'more' link point to, for the feeds that has
 * a URL defined in _get_feed_url.
 * NOTE: Used with the NERP feeds.
 */
function eatlases_more_link ($variables) {
	$url = $variables['url'];
	$title = $variables['title'];
	$target = NULL;

	if (stristr($url, 'aggregator/')) {
		$feed_id = substr($url, strrpos($url, '/') + 1);
		$nerp_url = _get_feed_url($feed_id);
		if ($nerp_url) {
			$url = $nerp_url;
			$target = '_blank';
		}
	}
	return '<div class="more-link">' . t('<a href="@link" title="@title"'.($target ? ' target="'.$target.'"' : '').'>more</a>', array('@link' => check_url($url), '@title' => $title)) . '</div>';
}

/**
 * Get the URL associated with a feed, used to defined where
 * the 'more' link should point to.
 * NOTE: Used with the NERP feeds.
 */
function _get_feed_url($feed_id) {
	$feeds = array(
		1 => 'http://www.nerptropical.edu.au/project/monitoring-status-and-trends-coral-reefs-great-barrier-reef', // 1.1
		2 => 'http://www.nerptropical.edu.au/project/marine-wildlife-management-great-barrier-reef-world-heritage-area', // 1.2
		3 => 'http://www.nerptropical.edu.au/project/characterising-cumulative-impacts-global-regional-and-local-stressors-present-and-past', // 1.3
		13 => 'http://www.nerptropical.edu.au/project/marine-turtles-and-dugongs-torres-strait', // 2.1
		14 => 'http://www.nerptropical.edu.au/project/mangrove-and-freshwater-habitat-status-torres-strait-islands', // 2.2
		15 => 'http://www.nerptropical.edu.au/project/monitoring-health-torres-strait-coral-reefs', // 2.3
		16 => 'http://www.nerptropical.edu.au/project/rainforest-biodiversity', // 3.1
		17 => 'http://www.nerptropical.edu.au/project/identifying-rainforest-refugia-and-hotspots', // 3.2
		18 => 'http://www.nerptropical.edu.au/project/targeted-surveys-missing-and-critically-endangered-rainforest-frogs', // 3.3
		19 => 'http://www.nerptropical.edu.au/project/monitoring-key-vertebrate-species', // 3.4
		20 => 'http://www.nerptropical.edu.au/project/tracking-coastal-turbidity-over-time-and-demonstrating-effects-river-discharge', // 4.1
		21 => 'http://www.nerptropical.edu.au/project/chronic-effects-pesticides-and-their-persistence-tropical-waters', // 4.2
		22 => 'http://www.nerptropical.edu.au/project/ecological-risk-assessment-pesticides-nutrients-and-sediments', // 4.3
		23 => 'http://www.nerptropical.edu.au/project/hazard-assessment-water-quality-threats-torres-strait-marine-waters-ecosystems', // 4.4
		24 => 'http://www.nerptropical.edu.au/project/understanding-diversity-great-barrier-reef', // 5.1
		25 => 'http://www.nerptropical.edu.au/project/experimental-and-field-investigations-combined-water-quality-and-climate-effects', // 5.2
		26 => 'http://www.nerptropical.edu.au/project/vulnerability-seagrass-habitats-to-flood-plume-impacts', // 5.3
		27 => 'http://www.nerptropical.edu.au/project/maximising-benefits-mobile-predators-great-barrier-reef-ecosystems', // 6.1
		28 => 'http://www.nerptropical.edu.au/project/drivers-juvenile-shark-biodiversity-and-abundance-inshore-GBR-ecosystems', // 6.2
		29 => 'http://www.nerptropical.edu.au/project/critical-seabird-foraging-locations-and-trophic-relationships', // 6.3
		30 => 'http://www.nerptropical.edu.au/project/fire-and-rainforests', // 7.1
		31 => 'http://www.nerptropical.edu.au/project/invasive-species-risks-and-responses-wet-tropics', // 7.2
		32 => 'http://www.nerptropical.edu.au/project/climate-change-and-impacts-extreme-climatic-events-australias-wet-tropics', // 7.3
		33 => 'http://www.nerptropical.edu.au/project/monitoring-ecological-effects-great-barrier-reef-zoning-plan', // 8.1
		34 => 'http://www.nerptropical.edu.au/project/marine-reserves-contribute-biodiversity-and-fishery-sustainability', // 8.2
		35 => 'http://www.nerptropical.edu.au/project/significance-no-take-marine-protected-areas-regional-recruitment-and-population', // 8.3
		36 => 'http://www.nerptropical.edu.au/project/dynamic-vulnerability-maps-and-decision-support-tools-great-barrier-reef', // 9.1
		37 => 'http://www.nerptropical.edu.au/project/mse-gbr', // 9.2
		38 => 'http://www.nerptropical.edu.au/project/prioritising-management-actions-great-barrier-reef-islands', // 9.3
		39 => 'http://www.nerptropical.edu.au/project/conservation-planning-for-a-changing-coastal-zone', // 9.4
		4 => 'http://www.nerptropical.edu.au/project/seltmp', // 10.1
		5 => 'http://www.nerptropical.edu.au/project/socio-economic-systems-and-reef-resilience', // 10.2
		6 => 'http://www.nerptropical.edu.au/project/building-resilient-torres-strait-communities', // 11.1
		7 => 'http://www.nerptropical.edu.au/project/approaches-for-detecting-disease-and-preventing-spread-torres-strait', // 11.2
		8 => 'http://www.nerptropical.edu.au/project/indigenous-peoples-and-protected-areas', // 12.1
		9 => 'http://www.nerptropical.edu.au/project/project-122-harnessing-natural-regeneration-cost-effective-rainforest-restoration', // 12.2
		10 => 'http://www.nerptropical.edu.au/project/wet-tropics-residents-and-tourists-social-and-economic-values', // 12.3
		11 => 'http://www.nerptropical.edu.au/project/governance-planning-and-effective-application-emerging-ecosystem-service-markets', // 12.4
		12 => 'http://www.nerptropical.edu.au/node/93'  // 13.1
	);
	return isset($feeds[$feed_id]) ? $feeds[$feed_id] : NULL;
}


/**
 * Return a themed breadcrumb trail, using HTML5 semantic.
 * See: https://coderwall.com/p/p0nvjw
 *
 * @param $breadcrumb
 *   An array containing the breadcrumb links.
 * @return a string containing the breadcrumb output.
 * NOTE: The breadcrumb is not useful for the eAtlas as it stands now.
 *     So we are not using it.
 *     See themes/eatlases/page.tpl.php
 */
/*
function eatlases_breadcrumb($variables) {
	$breadcrumb = $variables['breadcrumb'];
	if (!empty($breadcrumb)) {
		// Add the title at the end of the breadcrumbs (if such title exists)
		$title = drupal_get_title();
		if (!empty($title)) {
			$breadcrumb[] = $title;
		}

		$breadcrumbStr = '<ol class="breadcrumb">';
		$len = count($breadcrumb);
		for($i = 0; $i<$len; $i++) {
			$liClass = '';
			if ($i == 0) { $liClass .= ' first'; }
			if ($i == $len-1) { $liClass .= ' last'; }
			$liClass = trim($liClass);
			$breadcrumbStr .= '<li'.($liClass ? ' class="'.$liClass.'"' : '').
				' itemscope itemtype="http://data-vocabulary.org/Breadcrumb">';
			// A proper way to write the link would be to add an attribute itemprop="url"
			// to the "a" and an attribute itemprop="title" to the link label,
			// but the "a" are build by the "l" function, which is used everywhere (so it
			// should not be overriden)
			$breadcrumbStr .= $breadcrumb[$i];
			$breadcrumbStr .= '</li>';
		}
		$breadcrumbStr .= '</ol>';
		return $breadcrumbStr;
	}
}

function _eatlases_subMenuToString($subMenu, $ulClass = null) {
	// Find the active part of the sub menu
	foreach($subMenu as $key => $menuItem) {
		$link = $menuItem['link'];
		if (!isset($link['hidden']) || $link['hidden'] == 0) {
			if (_eatlases_isCurrentPage($link['href'])) {
				$subMenuString = _eatlases_menuToString($menuItem['below'], $ulClass);
				if ($subMenuString) {
					$title = $link['title'] ? $link['title'] : $link['link_title'];
					return '<h2 class="sub-menu-header">' . $title . '</h2>' .
							$subMenuString;
				} else {
					return null;
				}
			} else {
				$branchMenu = _eatlases_subMenuToString($menuItem['below'], $ulClass);
				if ($branchMenu != null) {
					return $branchMenu;
				}
			}
		}
	}
	return null;
}

// As defined in the "l" function (without the options array)
function _eatlases_isCurrentPage($path) {
	return ($path == $_GET['q'] || ($path == '<front>' && drupal_is_front_page()));
}

// Called in page.tpl.php
//     $menu: Drupal menu object
//     $depth: Maximum depth desired, null for the whole menu
//     $ulClass: CSS Class of the ul element of the menu (child-menu used for children)
function _eatlases_menuToString($menu, $depth = null, $ulClass = null) {
	$menuStr = '';
	if (($depth === null || $depth > 0) && !empty($menu)) {
		$nbVisibleItem = 0;
		foreach($menu as $key => $menuItem) {
			$link = $menuItem['link'];
			if (!isset($link['hidden']) || $link['hidden'] == 0) {
				if ($nbVisibleItem == 0) {
					$menuStr .= '<ul'.($ulClass ? ' class="'.$ulClass.'"' : '').'>';
				}
				$nbVisibleItem++;

				$title = $link['title'] ? $link['title'] : $link['link_title'];
				$subMenu = $menuItem['below'];

				$desc = $link['localized_options'] && $link['localized_options']['attributes'] && $link['localized_options']['attributes']['title'] ? $link['localized_options']['attributes']['title'] : null;

				$subMenuStr = _eatlases_menuToString($subMenu, ($depth === null ? null : $depth-1), 'child-menu');
				$menuStr .= '<li><div>'.l($title, $link['href']);
				if (!empty($subMenuStr)) {
					$menuStr .= $subMenuStr;
				}
				$menuStr .= '</div></li>';
			}
		}
		if ($nbVisibleItem > 0) {
			$menuStr .= '</ul>';
		}
	}
	return $menuStr;
}
*/
?>
