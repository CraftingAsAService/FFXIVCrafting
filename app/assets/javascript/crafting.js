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
			var root_engaged = $(this).closest('#CraftingList-section').length > 0;
			return crafting.recalculate_all(root_engaged);
		});

		$('input.obtained').change(function() {
			var root_engaged = $(this).closest('#CraftingList-section').length > 0;
			return crafting.recalculate_all(root_engaged);
		});

		$('.obtained-ok').click(function() {
			var tr = $(this).closest('tr'),
				needed = $('td.needed span', tr).length > 0 ? $('td.needed span', tr).html() : $('td.needed input', tr).val();

			$('input.obtained', tr).val(needed).trigger('change');

			return;
		});

		crafting.init_reagents();

		crafting.recalculate_all(true);

		return;
	},
	reagents:[],
	init_reagents:function() {
		$('.reagent').each(function() {
			var tr = $(this),
				data = {
					name: tr.find('a[href^="http"]').text().trim(), // Debug
					exempt: tr.hasClass('exempt'),
					item_id: tr.data('itemId'),
					yields: tr.data('yields'),
					reagents: [],
					needed: 0,
					obtained: 0,
					total: 0,
					elements: {
						'row': tr,
						'needed': $('td.needed span', tr).length > 0 ? $('td.needed span', tr) : $('td.needed input', tr),
						'obtained': $('input.obtained', tr),
						'total': $('td.total', tr)
					}
				};

			requires = tr.data('requires').split('&');

			// Add this item to the list if it's in the pre-req's too
			if (data.exempt && $('tr.reagent:not(.exempt)[data-item-id=' + data.item_id + ']').length > 0)
				requires[requires.length] = '1x' + data.item_id;

			if (requires.length == 1 && requires[0] == '')
				data.reagents = null;
			else
				for (var i = 0; i < requires.length; i++) 
				{
					// Required data
					var t = requires[i].split('x');

					data.reagents[data.reagents.length] = {
						'item_id': t[1],
						'quantity': parseInt(t[0]) 
					};
				}

			crafting.reagents[crafting.reagents.length] = data;

			return;
		});

		//console.log(crafting.reagents);

		return;
	},
	recalculate_all:function(root_engaged) {

		// Update "obtained" for each item
		// If it's Exempt, that means use it as a starting point
		for (var i = 0; i < crafting.reagents.length; i++)
		{
			var recipe = crafting.reagents[i];
			recipe.obtained = parseInt(recipe.elements.obtained.val());

			recipe.total = 0;

			if (recipe.exempt == true)
			{
				recipe.needed = parseInt(recipe.elements.needed.val());
				recipe.elements.obtained.attr('max', recipe.needed);

				// Highlight the exempt row if needed
				recipe.elements.row[(recipe.needed - recipe.obtained == 0 ? 'add' : 'remove') + 'Class']('success');

				// Ex. I need 20 of these, but already have 3.  The recipe yields 3
				// Ex. So 17 / 3 = 5.6, rounded up is 6.  We need to bake this recipe at least 6 times
				var bake = Math.ceil(Math.max(recipe.needed - recipe.obtained, 0) / recipe.yields);
				
				// Loop through all of it's children
				crafting.oven(recipe, bake, root_engaged);
			}
			else
				recipe.needed = 0; // Non exempt?  Reset needed.
				// This only works because of the natural order of things: exempt rows last.
		}

		// Now we have to take the obtained into account.
		// This means looking at each recipe with reagent that isn't exempt, and re-doing it
		for (var i = 0; i < crafting.reagents.length; i++)
		{
			var recipe = crafting.reagents[i];

			if (recipe.exempt == true || recipe.reagents == null)
				continue;

			// Let's "undo" some bakes
			var bake = Math.ceil(Math.min(0 - recipe.obtained, 0) / recipe.yields);
			
			// Loop through all of it's children
			crafting.oven(recipe, bake, root_engaged);
		}

		// Update fields
		for (var i = 0; i < crafting.reagents.length; i++)
		{
			var recipe = crafting.reagents[i];

			// Don't update exempt items
			if (recipe.exempt == true)
				continue;

			recipe.needed = recipe.needed - recipe.obtained;

			if (recipe.needed < 0)
			{
				recipe.obtained += recipe.needed; // Take the amount off of obtained
				if (recipe.obtained < 0)
					recipe.obtained = 0;

				recipe.elements.obtained.val(recipe.obtained);

				recipe.needed = 0; // Add the (absolute value) amount back to needed
			}

			recipe.elements.needed.html(recipe.needed);
			recipe.elements.obtained.attr('max', recipe.total);

			if (recipe.total < 0)
				recipe.total = 0;
			recipe.elements.total.html(recipe.total);

			recipe.elements.row[(recipe.needed == 0 ? 'add' : 'remove') + 'Class']('success');
		}

		return;
	},
	oven:function(recipe, parent_bake, root_engaged) {

		if (recipe.reagents == null)
			return;
		
		// Loop through all our reagents
		top: // Label for loop
		for (var i = 0; i < recipe.reagents.length; i++)
		{
			var reagent = recipe.reagents[i];

			// Loop through all known reagents
			for (var j = 0; j < crafting.reagents.length; j++)
			{
				var new_recipe = crafting.reagents[j];

				// If both match, bake it!
				if (new_recipe.item_id == reagent.item_id)
				{
					// Ex. Parent recipe is being baked 6 times.  The reagent indicates 2 are required.
					// Ex. 6 * 2 = 12; That's our immediate need, so add it to the total and needed
					var needed = parent_bake * reagent.quantity;
					new_recipe.needed += needed;
					new_recipe.total += needed;

					// Ex. Our needed now says 12.  How many times do we need to bake?
					// The recipe says it yields 3.  
					// Well, we already have 2, so (12 - 2) / 3 = 3.33; 4 bakes, rounded up

					var bake = Math.ceil(needed / new_recipe.yields);

					// Put it in the oven!
					crafting.oven(new_recipe, bake, root_engaged);
					
					continue top; // Jump to the next recipe's reagent
				}
			}
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