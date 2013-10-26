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
		$('.needed input, input.obtained').each(function() {
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
				obtained = parseInt(el.val()),
				neededEl = tr.find('.needed input'),
				neededVal = neededEl.val();

			if (neededEl.length == 0) {
				neededEl = tr.find('.needed span');
				neededVal = neededEl.html();
			}

			neededVal = parseInt(neededVal);

			if (obtained > neededVal)
			{
				obtained = neededVal;
				if (neededEl.is('input'))
					el.val(obtained);
				else
					el.html(obtained);
			}

			tr[(obtained >= neededVal ? 'add' : 'remove') + 'Class']('success');

			if (tr.closest('#CraftingList-section').length > 0)
			{
				var prev = el.data('amount');
				el.data('amount', obtained);

					// Different than other as we want the diff inverse
				var diff = prev - obtained;

				if (diff != 0)
				{
					crafting.change_reagents(tr, diff);
				}
			}
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
			trItemId = tr.data('itemId'),
			yields = tr.data('yields');

		if (typeof(data) === 'undefined' || data == '')
			return;

		var requires = data.split('&');

		// Add this item to the list if it's in the pre-req's too
		if (tr.hasClass('exempt') && $('tr.reagent:not(.exempt)[data-item-id=' + trItemId + ']').length > 0)
			requires[requires.length] = '1x' + trItemId;

		for (var i = 0; i < requires.length; i++) {
			var t = requires[i].split('x'),
				quantity = t[0],
				itemId = t[1];

			var target = $('tr.reagent:not(.exempt)[data-item-id=' + itemId + ']'),
				current_yields = target.data('yields'),
				neededEl = target.find('.needed span'),
				current_amount = parseInt(neededEl.html());

			// Let's make sure change amount is disible by the amount it yields
			var this_change_amount = change_amount * current_yields;

			var change = Math.ceil(this_change_amount * quantity / yields),
				new_amount = current_amount + change;

			neededEl.html(new_amount);

			if (itemId != trItemId)
				crafting.change_reagents(target, change / yields);

			var obtained_el = target.find('input.obtained');

			obtained_el.trigger('change');

			obtained_el[(new_amount <= 0 ? 'add' : 'remove') + 'Class']('disabled')
				.prop('disabled', new_amount <= 0 ? 'disabled' : '');

		}

		return;
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