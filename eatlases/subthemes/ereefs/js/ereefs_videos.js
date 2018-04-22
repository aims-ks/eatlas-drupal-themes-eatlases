// Define $ as jQuery
(function ($) {
	/**
	 * Make sure the video on the home page is proportionally sized
	 * by resizing it's height according to it's width.
	 * NOTE: The width of the video dynamically changes
	 *   when the page is resized, due to the CSS attribute
	 *   "max-width: 100%".
	 */
	function ereefs_resize_videos(ereefs_videos) {
		ereefs_videos.each(function(index) {
			// Original size: $(this).get(0).width, $(this).get(0).height
			// Current size: $(this).width(), $(this).height()
			// NOTE:
			//   $(this) = The eReefs video for "index".
			//   $(this).get(0) = The video attributes.
			//   $(this).get(index) does not make any sense since $(this) is the element for "index".
			var orig_width = $(this).get(0).width,
				orig_height = $(this).get(0).height;
				current_width = $(this).width();

			if (current_width && orig_width && orig_height) {
				$(this).height(current_width * orig_height / orig_width);
			}
		});
	}

	// Execute when the page is ready
	$(document).ready(function(){
		var ereefs_videos = $(this).find('.ereefs-video').find('iframe');
		if (ereefs_videos !== null && ereefs_videos.length > 0) {
			// Resize the video(s) after page is loaded
			ereefs_resize_videos(ereefs_videos);

			// Resize the video(s) after page is resized
			$(window).resize(function () {
				ereefs_resize_videos(ereefs_videos);
			});
		}
	});
})(jQuery);
