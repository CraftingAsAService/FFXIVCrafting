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
			});

			$('#inclusions').val(ids.join(','));

			localStorage.setItem('config:' + 'crafting-index-inclusions', $('.inclusions-list .label-primary').map(function() { return $(this).index(); }).get().join());
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
			});

			// Translate these mins and maxes to the radio buttons in the modal
			// Grab the ilvl from there, fill in the inputs
			$('#recipe-level-start').val($('#start' + min).data('start'));
			$('#recipe-level-end').val($('#end' + max).data('end'));
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
		});

		$('#ilvl-modal .choose').click(function(event) {
			event.preventDefault();

			var modalEl = $(this).closest('.modal'),
				start = $('#ilvl-modal input[type=radio][name=start]:checked').data('start'),
				end = $('#ilvl-modal input[type=radio][name=end]:checked').data('end');

			modalEl.modal('hide');

			$('#recipe-level-start').val(start);
			$('#recipe-level-end').val(end);
		});
	},
	class_selection_events:function() {
		$('.class-selector').click(function(event) {
			event.preventDefault();
			event.stopPropagation();

			var multiple = event.metaKey || event.ctrlKey,
				el = $(this);

			if (multiple)
				el.toggleClass('active', ! el.hasClass('active'));
			else {
				$('.class-selector').removeClass('active');
				el.addClass('active');
			}

			// Fix the checkboxes based on the .active class
			$('.jobs-list input:checkbox').each(function() {
				var checkboxEl = $(this),
					active = checkboxEl.closest('.class-selector').hasClass('active');

				checkboxEl.prop('checked', active);
			});
		});
	}
}

$(craftingIndex.init);