/**
 * Viewport - Detect which viewport the user is on, and if the screen resizes for any reason, change it
 * Usage
 *	$(document).on('viewportchanged', function() {
 *		console.log('Viewport changed to ', viewport.current);
 *	});
 */
var viewport = {
	current: null,
	sizes: {
		mobile : {
			width : 0
		},
		tablet : {
			width : 751
		},
		desktop : {
			width : 970
		}
	},
	init:function() {
		// Configure the Viewports
		viewport.determine_size(true);
		viewport.detect_resize();
	},
	determine_size:function(first_run) {
		// Assume the mobile viewport
		var new_viewport = 'mobile',
			viewport_width = $(window).innerWidth();

		$.each(viewport.sizes, function(name) {
			if (viewport_width >= this.width)
				new_viewport = name;
		});
		
		if (viewport.current == new_viewport)
			return;

		viewport.current = new_viewport;

		if (first_run !== true)
			$.event.trigger({
				type: 'viewportchanged'
			});
	},
	detect_resize:function() {
		$(window).bind('resize', function() {
			viewport.determine_size();
		});
	}
}

$(viewport.init);