var crafting = {
	init:function() {
		crafting.events();
		crafting_tour.init();
	},
	events:function() {
		$('#self_sufficient_switch').change(function() {
			$(this).closest('form').submit();
		});

		$('#obtain-these-items .collapse').click(function() {
			var button = $(this);

			button.toggleClass('glyphicon-chevron-down').toggleClass('glyphicon-chevron-up');

			var tbody = $(this).closest('tbody');

			var trEls = tbody.find('tr:not(:first-child)');

			trEls.toggleClass('hidden');
		});

		// Store the amount needed
		$('.needed input').each(function() {
			var el = $(this);
			el.data('amount', parseInt(el.val()));
		});

		// If they change needed or obtained

		$('.needed input').change(function() {
			var el = $(this);
			var amount = parseInt(el.val()),
				prev = el.data('amount');

			var diff = amount - prev;

			if (diff == 0)
				return;

			el.data('amount', amount);

			var tr = el.closest('tr');

			crafting.change_reagents(el.closest('tr'), diff);

			tr.find('input.obtained').trigger('change');
		});

		$('input.obtained').change(function() {
			var el = $(this),
				tr = el.closest('tr'),
				neededEl = tr.find('.needed input'),
				neededVal = neededEl.val();

			if (neededEl.length == 0) {
				neededEl = tr.find('.needed span');
				neededVal = neededEl.html();
			}

			tr[(parseInt(el.val()) >= parseInt(neededVal) ? 'add' : 'remove') + 'Class']('success');
		});

		$('.obtained-ok').click(function() {
			var tr = $(this).closest('tr'),
				neededEl = tr.find('.needed input'),
				neededVal = neededEl.val(),
				obtainedEl = tr.find('input.obtained');

			if (neededEl.length == 0) {
				neededEl = tr.find('.needed span');
				neededVal = neededEl.html();
			}

			obtainedEl.val(neededVal).trigger('change');
		})
	},
	change_reagents:function(tr, change_amount) {
		var data = tr.data('requires'),
			trItemId = tr.data('itemId');

		if (typeof(data) === 'undefined' || data == '')
			return;

		var requires = data.split('&');

		// Add this item to the list if it's in the pre-req's too
		if (tr.hasClass('exempt') && $('tr.reagent:not(.exempt)[data-item-id=' + tr.data('itemId') + ']').length > 0)
			requires[requires.length] = '1x' + trItemId;

		for (var i = 0; i < requires.length; i++) {
			var t = requires[i].split('x'),
				quantity = t[0],
				itemId = t[1];

			var target = $('tr.reagent:not(.exempt)[data-item-id=' + itemId + ']'),
				neededEl = target.find('.needed span'),
				current_amount = parseInt(neededEl.html()),
				change = change_amount * quantity;

			neededEl.html(current_amount + change);

			if (itemId != trItemId)
				crafting.change_reagents(target, change);

			target.find('input.obtained').trigger('change');
		}
	}
}

var crafting_tour = {
	tour: null,
	first_run: true,
	init:function() {
		var startEl = $('#start_tour');

		crafting_tour.tour = new Tour({
			orphan: true,
			onStart:function() {
				return startEl.addClass('disabled', true);
			},
			onEnd:function() {
				return startEl.removeClass('disabled', true);
			}
		});

		startEl.click(function(e) {
			e.preventDefault();

			if ($('#toggle-slim').bootstrapSwitch('status'))
				$('#toggle-slim').bootstrapSwitch('setState', false);

			if (crafting_tour.first_run == true)
				crafting_tour.build();
			
			if ($(this).hasClass('disabled'))
				return;

			crafting_tour.tour.restart();
		});
	},
	build:function() {

		crafting_tour.tour.addSteps([
			{
				element: '#CraftingList-section', 
				title: 'Recipe List',
				content: 'The list at the bottom is your official Recipe List.  You will be making these items.',
				placement: 'top'
			},
			{
				element: '#Gathered-section tr:first-child',
				title: 'Gathered Section',
				content: 'Items you can gather with MIN, BTN or FSH will appear in the Gathered Section.',
				placement: 'bottom'
			},
			{
				element: '#Bought-section tr:first-child',
				title: 'Bought Section',
				content: 'Items you cannot gather will be thrown into the Bought Section.',
				placement: 'bottom'
			},
			{
				element: '#Other-section tr:first-child',
				title: 'Other Section',
				content: 'Items that cannot be bought or gathered show up in the Other Section.  Most likely these will involve monster drops.',
				placement: 'bottom'
			},
			{
				element: '#PreRequisiteCrafting-section tr:first-child',
				title: 'Pre-Requisite Crafting',
				content: 'Why buy what you can craft?  The Crafted Section contains items necessary for your main recipes to finish.  The previous sections will already contain the sub items required.',
				placement: 'bottom'
			},
			{
				element: '#self-sufficient-form', 
				title: 'Self Sufficient',
				content: 'By default it assumes you want to be Self Sufficient.  Turning this option off will eliminate the Gathering and Crafting aspect and appropriately force the items into either Bought or Other.',
				placement: 'top'
			},
			{
				element: '#leveling-information',
				title: 'Leveling Information',
				content: 'Pay attention to the Leveling Information box as it will give you a heads up as to what your next quest turn ins will require.',
				placement: 'top'
			}
		]);

		crafting_tour.first_run = false;
	}
}

$(crafting.init);