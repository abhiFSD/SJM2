
var POW = {};

POW.msg = {
	run: function(messages) {
		if (typeof messages == 'object') {
			$.each(messages, function(i, message) {
				switch (message.action) {
					case 'ok':
						break;

					case 'reload':
						window.location.reload();
						break;

					case 'redirect':
						window.location = message.url;
						break;

					case 'exec':
						if (window[message.command]) {
							window[message.command].apply(null, message.arguments);
						}
						else {
							message.command.apply(null, message.arguments);
						}
						break;

					case 'html':
						$(message.target).html(message.html);
						break;

					case 'close_modal':
						// hacky but leaves no artifacts
						$('.modal:visible .close').click();
						break;
				}
			});
		}
	},
	rc: function(type, url, data, callback) {
		$.ajax({
			url: url,
			type: type,
			data: data,
			error: function(xhr, text, errorString) {
				console.log(xhr.status+' '+text+' '+errorString);
				console.log(xhr.status+' '+xhr.responseText.substr(0, 500));
			},
			success: function(data) {
				if (!callback) {
					POW.msg.run(data);
				}
				else {
					callback.call(null, data);
				}
			}
		});
	}
};
