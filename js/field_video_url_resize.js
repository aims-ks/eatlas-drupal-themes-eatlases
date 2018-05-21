(function ($) {

	function resize_videos(videos) {
		videos.each(function(index) {
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
		var videos = $(this).find('.field-name-field-video-url').find('iframe');
		if (videos && videos.length > 0) {
			// Resize the video(s) after page is loaded
			resize_videos(videos);

			// Resize the video(s) after page is resized
			$(window).resize(function () {
				resize_videos(videos);
			});
		}
	});

}(jQuery));
