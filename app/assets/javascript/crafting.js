var crafting = {
	init:function() {
		crafting.events();
		crafting_tour.init();
	},
	events:function() {
		$('.bootswitch').bootstrapSwitch({
			onSwitchChange:function() {
				$(this).closest('form').submit();	
			}
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
				total = $('td.total', tr).html();

			$('input.obtained', tr).val(total).trigger('change');

			return;
		});

		crafting.init_reagents();

		crafting.recalculate_all(true);

		$('.vendors').on('click', function(event) {
			event.preventDefault();
			
			var el = $(this),
				id = el.closest('.reagent').data('itemId');

			if (el.hasClass('loading'))
				return;

			var modal = $('#vendors_for_' + id);

			if (modal.length == 0)
			{

				$.ajax({
					url: '/vendors/view/' + id,
					dataType: 'json',
					beforeSend:function() {

						el.addClass('loading');

						global.noty({
							type: 'warning',
							text: 'Loading Vendors'
						});
					},
					success:function(json) {
						$('body').append(json.html);

						$('#vendors_for_' + id).modal();

						el.removeClass('loading');
					}
				});
			}
			else
			{
				$('#vendors_for_' + id).modal('show');
			}

			return;
		});

		$('.clusters').on('click', function(event) {
			event.preventDefault();
			
			var el = $(this),
				id = el.closest('.reagent').data('itemId'),
				classjob_id = el.data('classjobId');

			if (el.hasClass('loading'))
				return;

			var modal = $('#clusters_for_' + id);

			if (modal.length == 0)
			{

				$.ajax({
					url: '/gathering/clusters/' + id,
					dataType: 'json',
					beforeSend:function() {

						el.addClass('loading');

						global.noty({
							type: 'warning',
							text: 'Loading Clusters'
						});
					},
					success:function(json) {
						$('body').append(json.html);

						$('#clusters_for_' + id).modal();

						el.removeClass('loading');
					}
				});
			}
			else
			{
				$('#clusters_for_' + id).modal('show');
			}

			return;
		});
		
		$('.beasts').on('click', function(event) {
			event.preventDefault();
			
			var el = $(this),
				id = el.data('itemId');

			if (el.hasClass('loading'))
				return;

			var modal = $('#beasts_for_' + id);

			if (modal.length == 0)
			{

				$.ajax({
					url: '/gathering/beasts/' + id,
					dataType: 'json',
					beforeSend:function() {

						el.addClass('loading');

						global.noty({
							type: 'warning',
							text: 'Loading Beasts'
						});
					},
					success:function(json) {
						$('body').append(json.html);

						$('#beasts_for_' + id).modal();

						el.removeClass('loading');
					}
				});
			}
			else
			{
				$('#beasts_for_' + id).modal('show');
			}

			return;
		});

		$('#map_it').click(function(event) {
			event.preventDefault();

			global.noty({
				type: 'warning',
				text: 'Loading Map'
			});

			var data = [];

			// Go through the form and get the item id's and what's needed
			$('#Gathered-section, #Bought-section, #Other-section').find('tr.reagent').each(function() {
				var td = $(this),
					id = td.data('itemId'),
					needed = td.find('.needed span').html();

				data[data.length] = id + '|' + needed;
			});

			data = data.join('||');

			var title = $('#banner h1').text();
			if ($('#banner h2').length > 0)
				title += ' ' + $('#banner h2').text();

			var form = $('<form action="/map" method="POST">' + 
				'<input type="hidden" name="items" value="' + data + '">' +
				'<input type="hidden" name="title" value="' + title + '">' +
				'</form>');
			
			$('body').append(form);
			
			form.submit();
		});

		$('#csv_download').click(function(event) {
			event.preventDefault();

			// var data = [["name1", "city1", "some other info"], ["name2", "city2", "more info"]];
			var data = [["Item", "iLevel", "Yields", "Needed", "Purchase", "Source"]];

			$('tr.reagent').each(function() {
				var row = [],
					el = $(this);

				row.push(el.find('span.name').text());
				row.push(el.find('.ilvl').text().replace(/\s|\n/ig, ''));
				row.push(el.data('yields'));

				// if ($('.needed input', el).length > 0)
				// 	row.push($('.needed input', el).val());
				// else
				// 	row.push($('.needed span', el).text());

				// row.push(el.find('.obtained').val());
				row.push(el.find('.total').text());

				row.push(el.find('.vendors').length ? (el.find('.vendors').text().replace(/\s|\n/ig, '') + ' gil') : '');

				var source = [];
				el.find('.crafted_gathered .class-icon').each(function() {
					return source.push($(this).attr('title'));
				});

				if (el.find('.crafted_gathered .beasts').length > 0)
					source.push('Beasts');

				row.push(source.join(', '));



				data.push(row);

				return;
			});

			global.exportToCsv($('.csv-filename').text() + '.csv', data);

			return;
		})

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

			// no more needed?
			if (recipe.needed < 0)
			{
				recipe.obtained += recipe.needed; // Take the amount off of obtained

				var obtained = Math.ceil(recipe.obtained / recipe.yields) * recipe.yields;
				
				recipe.elements.obtained.val(obtained < 0 ? 0 : obtained);

				recipe.needed = 0; // Add the (absolute value) amount back to needed
			}

			// We want to make sure the maximum, and the needed, are multiples of the Yield
			var needed = Math.ceil(recipe.needed / recipe.yields) * recipe.yields,
				max = Math.ceil(recipe.total / recipe.yields) * recipe.yields;

			recipe.elements.needed.html(needed);
			recipe.elements.obtained.attr('max', max);

			recipe.elements.total.html(recipe.total < 0 ? 0 : Math.ceil(recipe.total / recipe.yields) * recipe.yields);

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