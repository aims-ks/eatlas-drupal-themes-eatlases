(function ($) {
	var searchShowHideAnimation = 400;
	var minimisedSearch, overlaySearch;

	$(document).ready(
		function() {
			minimisedSearch = $(".block-search .search-block-minimised");
			overlaySearchBlock = $(".block-search .search-block-overlay");
			overlaySearchOverlay = overlaySearchBlock.find(".search-overlay");
			overlaySearchContent = overlaySearchBlock.find(".search-overlay-content");
			minimisedSearch.show();
			overlaySearchBlock.hide();

			// Add 'click' event listener on the search magnifier button
			minimisedSearch.find('.show-search-overlay').click(function(event) {
				showSearchOverlay();
				return false;
			});

			// Add 'click' event listener on the close overlay button
			overlaySearchContent.find('.close-search-overlay').click(function(event) {
				hideSearchOverlay();
				return false;
			});

			// Add 'click' event listener anywhere on the overlay
			overlaySearchOverlay.click(function(event) {
				hideSearchOverlay();
				return false;
			});

			// Hide search when the "Escape" key is pressed
			$(document).keyup(function(e) {
				if (e.keyCode == 27) {
					hideSearchOverlay();
				}
			});
		}
	);

	/**
	 * Show / Hide functions
	 * NOTE: Browsers have issue applying opacity on elements (search-block-overlay) that contains
	 *   child with transparency (search-overlay). Elements bellow (content of the page) will be rendered on top (over search-overlay).
	 *   The solution is to change the opacity of elements individually (search-overlay and search-overlay-content).
	 */

	function hideSearchOverlay() {
		overlaySearchOverlay.css('opacity', 0.5);
		overlaySearchOverlay.animate({ opacity: 0 }, searchShowHideAnimation, 'swing', function() {
			overlaySearchBlock.hide();
		});

		overlaySearchContent.css('opacity', 1);
		overlaySearchContent.animate({ opacity: 0 }, searchShowHideAnimation, 'swing', function() {
			overlaySearchBlock.hide();
		});
	}

	function showSearchOverlay() {
		overlaySearchOverlay.css('opacity', 0);
		overlaySearchContent.css('opacity', 0);
		overlaySearchBlock.show();

		overlaySearchOverlay.animate({ opacity: 0.5 }, searchShowHideAnimation, 'swing');
		overlaySearchContent.animate({ opacity: 1 }, searchShowHideAnimation, 'swing');

		// Focus on the search field
		overlaySearchContent.find('input[type=search]').focus();
	}
}(jQuery));
