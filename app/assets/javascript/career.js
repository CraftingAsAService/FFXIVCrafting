var career = {
	init:function() {

		$('#supporter-producer-class, #receiver-recipient-class, #gatherer-class').multiselect({
			buttonClass: 'btn btn-sm btn-primary',
			buttonWidth: 'auto',
			buttonContainer: '<div class="btn-group" />',
			maxHeight: false,
			buttonText: function(options) {
				if (options.length == 0) {
					return 'None selected <b class="caret"></b>';
				}
				else {
					var selected = '';
					options.each(function() {
						selected += $(this).text() + ', ';
					});
					return selected.substr(0, selected.length - 2) + ' <b class="caret"></b>';
				}
			}
		});

		$('#supporter-supported-classes, #receiver-producer-classes, #gathering-supported-classes, #battling-supported-classes').multiselect({
			buttonClass: 'btn btn-sm btn-primary',
			buttonWidth: 'auto',
			buttonContainer: '<div class="btn-group" />',
			maxHeight: false,
			buttonText: function(options) {
				if (options.length == 0) {
					return 'None selected <b class="caret"></b>';
				}
				else if (options.length > 1) {
					return 'these ' + options.length + ' Classes  <b class="caret"></b>';
				}
				else {
					var selected = '';
					options.each(function() {
						selected += $(this).text() + ', ';
					});
					return selected.substr(0, selected.length - 2) + ' <b class="caret"></b>';
				}
			}
		});
	}
}

$(career.init);