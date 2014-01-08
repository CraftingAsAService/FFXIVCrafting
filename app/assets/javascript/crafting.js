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

		// If they change needed or obtained

		$('.needed input').change(function() {
			return crafting.recalculate_all();
		});

		$('input.obtained').change(function() {
			return crafting.recalculate_all();
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
		});

		crafting.recalculate_all();
	},
	recalculate_all:function() {
		// Reset the fields
		$('#Gathered-section, #Other-section, #PreRequisiteCrafting-section').find('.needed span').html(0);

		$('#CraftingList-section .needed input').each(function() {
			var el = $(this),
				tr = el.closest('tr');
			crafting.change_reagents(tr, el.val() - tr.find('.obtained').val());
			return;
		});

		// And then take off any obtained
		$('#Gathered-section, #Other-section, #PreRequisiteCrafting-section').find('.obtained').each(function() {
			var el = $(this),
				tr = el.closest('tr'),
				needed_el = tr.find('.needed span'),
				obtained = parseInt(el.val()),
				needed = parseInt(needed_el.html());

			var new_amount = needed - obtained;
			if (new_amount < 0)
			{
				el.val(obtained + new_amount);
				new_amount = 0;
			}

			needed_el.html(new_amount);

			tr[(new_amount <= 0 ? 'add' : 'remove') + 'Class']('success');

			return;
		});

		return;
	},
	change_reagents:function(tr, parent_bake, indent) {
		var data = tr.data('requires'),
			trItemId = tr.data('itemId');

		if (typeof(data) === 'undefined' || data == '')
			return;

		var requires = data.split('&');

		// Add this item to the list if it's in the pre-req's too
		if (tr.hasClass('exempt') && $('tr.reagent:not(.exempt)[data-item-id=' + trItemId + ']').length > 0)
			requires[requires.length] = '1x' + trItemId;

		for (var i = 0; i < requires.length; i++) {
			// Required data
			var t = requires[i].split('x'),
				required = t[0],
				itemId = t[1];

			// Elements
			var target = $('tr.reagent:not(.exempt)[data-item-id=' + itemId + ']'),
				obtained_el = target.find('input.obtained'),
				needed_el = target.find('.needed span');

			// Element data and calculations
			var yields = target.data('yields'),
				current_amount = parseInt(needed_el.html()),
				obtained_amount = parseInt(obtained_el.val()),
				// Bake!
				bake = Math.ceil(parent_bake * required / yields), // (9 * 1) / 2 == 4.5 =~ 5
				end_result = bake * yields, // 5 * 2 == 10
				// Take the old amount and add in the end result of what was baked
				// Also take off what's already been obtained
				new_amount = current_amount + end_result;

			// Set the new amount
			needed_el.html(new_amount);

			obtained_el.attr('max', new_amount);

			// Now go deeper, baking what's needed minus what's been obtained
			if (itemId != trItemId)
				crafting.change_reagents(target, Math.ceil(bake - (obtained_amount / yields)), indent + ' ');
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