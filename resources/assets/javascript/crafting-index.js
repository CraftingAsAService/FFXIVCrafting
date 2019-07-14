var craftingIndex = {
	init:function() {
		$('.bootswitch').bootstrapSwitch();

		craftingIndex.options_events();

		craftingIndex.ilvl_events();

		craftingIndex.class_selection_events();

		craftingIndex.inclusions();
	},
	options_events:function() {

		if (localStorage.getItem('config:self_sufficient') == 0)
			$('#self_sufficient_switch').bootstrapSwitch('state', false);

		$('#self_sufficient_switch').on('switchChange.bootstrapSwitch', function(event, state) {
			if (state == true)
				localStorage.removeItem('config:self_sufficient')
			else
				localStorage.setItem('config:self_sufficient', 0);
		});

		$('#lvlType').on('change', function() {
			var el = $(this),
				lvlType = el.val();

			$('.ilvl-helper').toggleClass('hidden', lvlType != 'i');
			$('.rlvl-helper').toggleClass('hidden', lvlType != 'r');
		});
	},
	inclusions:function() {

		$('.inclusions-list .label').click(function() {
			$(this)
				.toggleClass('label-default').toggleClass('opaque')
				.toggleClass('label-primary');

			var ids = [];
			$('.inclusions-list .label-primary').each(function() {
				ids.push($(this).data('ids'));
				return;
			});

			$('#inclusions').val(ids.join(','));

			localStorage.setItem('config:' + 'crafting-index-inclusions', $('.inclusions-list .label-primary').map(function() { return $(this).index(); }).get().join());

			return;
		});

		// Run the preloaded items after the function's been declared

		var preloaded_inclusions = localStorage.getItem('config:' + 'crafting-index-inclusions');

		if (preloaded_inclusions != null && preloaded_inclusions != '')
		{
			preloaded_inclusions = preloaded_inclusions.split(',');
			for (var i = 0; i < preloaded_inclusions.length; i++)
			{
				$($('.inclusions-list span')[preloaded_inclusions[i]]).trigger('click');
			}
		}

		return;
	},
	ilvl_events:function() {

		$('#account-ilvl').click(function(event) {
			event.preventDefault();

			var min = null, max = null;
			$('.class-selector.active').each(function() {
				var el = $(this),
					level = el.data('level'),
					elMax = Math.ceil(level / 5) * 5,
					elMin = elMax - 4;

				if (elMin < min || min == null)
					min = elMin;
				if (elMax > max || max == null)
					max = elMax;

				return;
			});

			// Translate these mins and maxes to the radio buttons in the modal
			// Grab the ilvl from there, fill in the inputs
			$('#recipe-level-start').val($('#start' + min).data('start'));
			$('#recipe-level-end').val($('#end' + max).data('end'));

			return;
		});

		// Modal Events

		$('#ilvl-modal .select-range').click(function(event) {
			event.preventDefault();

			var el = $(this),
				modalEl = el.closest('.modal'),
				trEl = el.closest('tr'),
				start = trEl.find('[name=start]').data('start'),
				end = trEl.find('[name=end]').data('end');

			modalEl.modal('hide');

			$('#recipe-level-start').val(start);
			$('#recipe-level-end').val(end);

			return;
		});

		$('#ilvl-modal input[type=radio]').change(function() {
			// Make sure that end doesn't overlap the start (we don't want to select 10 - 5)

			var el = $(this),
				type = el.attr('name'),
				otherType = type == 'start' ? 'end' : 'start',
				typeEls = '#ilvl-modal input[type=radio][name=' + type + ']',
				otherEls = '#ilvl-modal input[type=radio][name=' + otherType + ']',
				typePosition = $(typeEls).index(el),
				otherPosition = $(otherEls).index($(otherEls + ':checked')[0]);

			if ((type == 'start' && typePosition > otherPosition) || (type == 'end' && typePosition < otherPosition))
				$($(otherEls)[typePosition]).prop('checked', true);

			return;
		});

		$('#ilvl-modal .choose').click(function(event) {
			event.preventDefault();

			var modalEl = $(this).closest('.modal'),
				start = $('#ilvl-modal input[type=radio][name=start]:checked').data('start'),
				end = $('#ilvl-modal input[type=radio][name=end]:checked').data('end');

			modalEl.modal('hide');

			$('#recipe-level-start').val(start);
			$('#recipe-level-end').val(end);

			return;
		});

		return;
	},
	class_selection_events:function() {

		// Bootstrap's `data-toggle='buttons'` wasn't powerful enough for what I wanted
		// Normal Clicks: Only allow one class to be selected
		// CTRL Clicks: Allow multiple classes to be selected
		// Scenarios
		//  One is highlighted, click another: first turns off, new turns on
		//  Clicking the same one: nothing
		//  CTRL Clicking the only one highlighted: nothing
		//  CTRL Clicking another one: both highlight, everything goes yellow (warning)
		//  CTRL Clicking one off (1 left): goes back to blue (primary)

		$('.class-selector').click(function(event) {
			event.stopPropagation();
			event.preventDefault();

			var multiple = event.metaKey || event.ctrlKey,
				el = $(this),
				checkboxEl = el.find('input:checkbox');

			// If this is not a multiple selection
			if ( ! multiple)
			{
				// Reset all active states
				$('.jobs-list .class-selector').removeClass('active');

				// Make the clicked one active
				el.addClass('active');
			}
			else
			{
				// Are there already more than one?
				var selected_count = $('.jobs-list input:checkbox:checked').length;

				// If this one is already active, and there's at least two selected, go ahead and unselect it
				if (el.hasClass('active') && selected_count > 1)
					el.removeClass('active');
				// If this one is already active, but it's the only selected one, just drop out
				else if ( ! el.hasClass('active'))
					el.addClass('active');
			}

			// Fix the checkboxes based on the .active class
			$('.jobs-list input:checkbox').each(function() {
				var checkboxEl = $(this),
					active = checkboxEl.closest('.class-selector').hasClass('active');

				checkboxEl.prop('checked', active);

				return;
			});

			// Fix the active images based on the .active class
			$('.jobs-list img').each(function() {
				var imgEl = $(this),
					active = imgEl.closest('.class-selector').hasClass('active');

				imgEl
					.toggleClass('selected', active)
					.attr('src', imgEl.data((active ? 'active' : 'original') + 'Src'));

				return;
			});

			return;
		});

		// Save the original src on each of these for ease of use later
		$('.jobs-list img').each(function() {
			$(this).data('originalSrc', $(this).attr('src'));
			return;
		});

		// Start the page off with one selected
		if ($('.class-selector.select-me').length == 1)
			$('.class-selector.select-me').first().trigger('click');
		else
			$('.class-selector').first().trigger('click');

		return;
	}
}

$(craftingIndex.init);