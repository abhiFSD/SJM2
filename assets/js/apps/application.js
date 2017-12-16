$(function() {

	// download buttons
	$(document).on('click', '[data-post-download]', function(e) {
		form_submit_target.call($(this), e, function(data) {
			window.location = data.download;
		});
	});

});
