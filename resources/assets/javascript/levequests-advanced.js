var leves = {
	init:function() {
		leves.events();

		leves.decipher_hash();
			
		leves.search();

		return;
	},
	decipher_hash:function() {
		var hash = document.location.hash;

		if (hash == '')
			return false;

		// Take off the #, explode
		hash = hash.slice(1).split('|');

		// Fill in the fields
		$('#class-selector').multiselect('deselect', 'CRP').multiselect('select', hash[0].split(','));
		$('#min-level').val(hash[1]);
		$('#max-level').val(hash[2]);
		$('#type-selector').multiselect('deselect', ['Town', 'Courier', 'Field']).multiselect('select', hash[3].split(','));
		if (hash[4] == '1')
			$('#repeatable_only').prop('checked', true);

		if (hash[5] != '' || hash[6] != '' || hash[7] != '')
			$('.toggle-advanced').trigger('click');

		$('#leve_item').val(hash[5]);
		$('#leve_name').val(hash[6]);
		$('#leve_location').val(hash[7]);

		return true;
	},
	events:function() {
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

		$('#min-level, #max-level').change(function() {
			var el = $(this);
			var this_min = parseInt(el.attr('min')),
				this_max = parseInt(el.attr('max')),
				val = parseInt(el.val());

			// Prevent overlapping inputs
			if (el.is('#max-level'))
			{
				var min_el_val = parseInt($('#min-level').val());
				if (val < min_el_val)
				{
					el.val(min_el_val);
					val = min_el_val;
				}
			}
			else
			{
				var max_el_val = parseInt($('#max-level').val());
				if (val > max_el_val)
				{
					el.val(max_el_val);
					val = max_el_val;
				}
			}
			
			// Prevent going over/under min/max attributes
			if (val < this_min) val = this_min;
			if (val > this_max) val = this_max;

			el.val(val);

			return;
		});

		$('#save-setup').click(function(event) {
			event.preventDefault();

			global.set_cookie('previous_leve_load', document.location.hash);

			global.noty({
				type: 'success',
				text: 'Setup Saved'
			});

			return;
		});

		$('#load-setup').click(function(event) {
			event.preventDefault();

			global.noty({
				type: 'info',
				text: 'Loading Setup'
			});

			document.location.hash = decodeURIComponent(global.get_cookie('previous_leve_load'));

			leves.decipher_hash();
			leves.search();

			return;
		});
	},
	search:function() {
		var classes = [], //$('#class-selector + .btn-group input:checked'),
			types = [], //$('#type-selector + .btn-group input:checked'),
			repeatable_only = $('#repeatable_only').is(':checked'),
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

		document.location.hash = [
				classes.join(','), 
				min_level, 
				max_level, 
				types.join(','),
				repeatable_only == true ? 1 : 0,
				leve_item,
				leve_name,
				leve_location
			].join('|');

		$('.leve_rewards').popover('destroy');

		$.ajax({
			url: '/levequests/populate-advanced',
			type: 'get',
			dataType: 'html',
			data: {
				classes : classes,
				types : types,
				repeatable_only : repeatable_only,
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