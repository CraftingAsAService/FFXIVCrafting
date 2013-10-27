var quests = {
	init:function() {
		quests.events();

		quests.decipher_hash();

		quests.search();

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
		$('#quest_item').val(hash[3]);

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

		$('.quest-form').submit(function(event) {
			event.preventDefault();

			quests.search();
		});

		$('.quest-form .filter-form').click(function(event) {
			event.preventDefault();
			$('.quest-form').submit();
		});

		$('.quest-text-search').keyup(function(e) {
			if (e.which == 13)
				quests.search();
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
	},
	search:function() {
		var classes = [], //$('#class-selector + .btn-group input:checked'),
			min_level = parseInt($('#min-level').val()),
			max_level = parseInt($('#max-level').val()),
			quest_name = $('#quest_item').val().toLowerCase();

		$('#class-selector + .btn-group input:checked').each(function() {
			classes[classes.length] = $(this).val();
		});

		document.location.hash = [
				classes.join(','), 
				min_level, 
				max_level, 
				quest_name
			].join('|');

		$('tr.quest').each(function() {
			var tr = $(this),
				name = $('td span.name', tr).html().toLowerCase(),
				lvl = parseInt($('td span.level', tr).html()),
				cls = $('td img.class-icon', tr).data('abbr');

			var matches = true;

			// Name Test
			if (quest_name != '')
				if (name.indexOf(quest_name) < 0)
					matches = false;

			// Class Test
			var class_match = false;
			for (var i = 0; i < classes.length; i++)
				if (classes[i] == cls)
				{
					class_match = true;
					break;
				}

			if (class_match == false)
				matches = false;

			if (min_level > lvl || lvl > max_level)
				matches = false;

			tr[(matches ? 'remove' : 'add') + 'Class']('hidden');
		});
	}
}

$(quests.init);