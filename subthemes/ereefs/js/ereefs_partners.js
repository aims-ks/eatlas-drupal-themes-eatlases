// Define $ as jQuery
(function ($) {
	// Hide all partner text
	// NOTE: They are hidden by JavaScript, so the page will still be usefull
	//   if the browser do not support JavaScript (like search engine bot)
	function ereefs_hide_partners() {
		$('#partners').find('[class^="partner-separator"]').addClass('hidden');
		$('#partners').find('div[id^="partner-"]').addClass('hidden');
	}

	// Execute when the page is ready
	$(document).ready(function(){
		// If there is an anchor in the URL, show the corresponding text
		// Example: /ereefs/project-partners#partner-csiro
		var selected = $(location).attr('hash');
		if (selected) {
			ereefs_hide_partners();
			$(selected).removeClass('hidden');
		}

		// Find all the "span" elements in the "logo-container"s
		// and attach a onClick function to each of them
		$(this).find('.logo-container').find('span').click(function () {
			// Hide all partner text (to hide the previously selected one)
			ereefs_hide_partners();

			// Show the partner text corresponding to the clicked logo
			$('#' + $(this).attr("class")).removeClass('hidden');
		});
	});
})(jQuery);


