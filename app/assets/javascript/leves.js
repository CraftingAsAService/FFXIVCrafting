var leves = {
	init:function() {
		$('#class-selector').multiselect({
			buttonClass: 'btn',
			buttonWidth: 'auto',
			buttonContainer: '<div class="btn-group" />',
			maxHeight: false,
			includeSelectAllOption: true,
			buttonText: function(options) {
				if (options.length == 0) {
					return 'None selected <b class="caret"></b>';
				}
				else if (options.length == 1) {
					return '<img src="/img/classes/' + $(options[0]).val() + '.png"> <b class="caret"></b>';
				}
				else if (options.length > 1) {
					return options.length + ' selected  <b class="caret"></b>';
				}
				else {
					var selected = '';
					options.each(function() {
					selected += $(this).text() + ', ';
					});
					// <img src='/img/classes/{{ $job }}.png'>
					return selected.substr(0, selected.length - 2) + ' <b class="caret"></b>';
				}
			}
		});

		$('#type-selector').multiselect({
			buttonClass: 'btn',
			buttonWidth: 'auto',
			buttonContainer: '<div class="btn-group" />',
			maxHeight: false,
			includeSelectAllOption: true,
			buttonText: function(options) {
				if (options.length == 0) {
					return 'None selected <b class="caret"></b>';
				}
				else if (options.length > 1) {
					return options.length + ' selected  <b class="caret"></b>';
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

		$('.leve-form').submit(function(event) {
			event.preventDefault();

			leves.search();
		});

		$('.leve-form .filter-form').click(function(event) {
			event.preventDefault();
			$('.leve-form').submit();
		});

		$('.toggle-advanced').click(function(e) {
			e.preventDefault();
			$('.advanced').toggleClass('hidden');
			$('.advanced input').val('');
		});

		$('.leve-text-search').keyup(function(e) {
			if (e.which == 13)
				leves.search();
		});

		leves.search();
	},
	search:function() {
		var classes = [], //$('#class-selector + .btn-group input:checked'),
			types = [], //$('#type-selector + .btn-group input:checked'),
			triple_only = $('#triple_only').is(':checked'),
			min_level = parseInt($('#min-level').val()),
			max_level = parseInt($('#max-level').val()),
			leve_item = $('#leve_item').val(),
			leve_name = $('#leve_name').val(),
			leve_location = $('#leve_location').val();

		$('#class-selector + .btn-group input:checked').each(function() {
			classes[classes.length] = $(this).val();
		});
		$('#type-selector + .btn-group input:checked').each(function() {
			types[types.length] = $(this).val();
		});

		$('.leve_rewards').popover('destroy');

		$.ajax({
			url: '/leve',
			type: 'post',
			dataType: 'html',
			data: {
				classes : classes,
				types : types,
				triple_only : triple_only,
				min_level : min_level,
				max_level : max_level,
				leve_item : leve_item,
				leve_name : leve_name,
				leve_location : leve_location
			},
			beforeStart:function() {
				global.noty({
					type: 'info',
					text: 'Searching for Leves'
				});
			},
			success:function(output) {
				$('.leve-table tbody').html(output);

				if (typeof(initXIVDBTooltips) != 'undefined')
					initXIVDBTooltips();

				global.reset_popovers();

				$('[rel=tooltip]').tooltip();
			}
		});
	}
}

$(leves.init);