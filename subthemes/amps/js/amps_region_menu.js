(function ($) {
	$(document).ready(function() {
		/**
		 * Make menu appear slowly
		 * to give a chance to the user
		 * to move the mouse out and cancel the menu.
		 * NOTE: Only affect "eAtlas_spatial_publisher_menu" found in "menu".
		 *   We don't want to change the behaviour of the "accordion-menu" (aka hamburger menu).
		 */
		$('.menu .eAtlas_spatial_publisher_menu').hover(
			// Mouse moved IN
			function() {
				var subMenu = $(this).find('ul');

				// Disable mouse events on the sub-menu to allow the user to move mouse out and make the menu disappear
				//   while it's fading in
				subMenu.css('pointer-events', 'none');
				// Make the sub-menu transparent. A JQuery animation will slowly make it appear.
				subMenu.css('opacity', '0');

				// Fade sub-menu in
				subMenu.animate(
					{
						'opacity': 1
					}, {
						'duration': 500,
						'complete': function() {
							// Re-enable the mouse event listener on the sub-menu
							// to allow the user to select a region.
							// - Remove the CSS property 'pointer-events'
							$(this).css('pointer-events', '');
						}
					}
				);
			},
			function() {
				var subMenu = $(this).find('ul');
				// Cancel fade-in animation (if it's still running)
				subMenu.stop();
			}
		);
	});
}(jQuery));
