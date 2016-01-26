var crafting = {
	init:function() {
		crafting.events();
		crafting_tour.init();
	},
	events:function() {

		$('#toggle-crystals').click(crafting.toggle_crystals);

		if (localStorage.getItem('config:toggle-crystals') == 'off')
			$('#toggle-crystals').trigger('click');

		$('#obtain-these-items .collapsible').click(function() {
			var button = $(this);

			button.find('i').toggleClass('glyphicon-chevron-down').toggleClass('glyphicon-chevron-up');

			var tbody = $(this).closest('tbody');

			var trEls = tbody.find('tr:not(:first-child)');

			trEls.toggleClass('hidden');
		});

		// If they change needed or obtained

		$('.needed input').change(function() {
			var el = $(this),
				// Why is this a thing?  It's always `true`... right?
				root_engaged = el.closest('#CraftingList-section').length > 0;

			crafting.recalculate_all(root_engaged);

			// Fix #CraftingList totals
			el.closest('tr').find('.total').html(el.val());

			return;
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

		$('#clear-localstorage').click(crafting.clear_localstorage);

		crafting.set_localstorage_id();

		crafting.restore_localstorage();

		crafting.init_reagents();

		crafting.recalculate_all(true);

		$('#map_all, #map_remaining').click(function(event) {
			event.preventDefault();

			// var remaining = $(this).attr('id') == 'map_remaining';

			// global.noty({
			// 	type: 'warning',
			// 	text: 'Loading Map'
			// });

			// var data = [];

			// // Go through the form and get the item id's and what's needed
			// $('#Gathered-section, #Bought-section, #Other-section').find('tr.reagent').each(function() {
			// 	var td = $(this),
			// 		id = td.data('itemId'),
			// 		needed = td.find('.needed span').html();

			// 	if (remaining && needed == '0')
			// 		return;

			// 	data[data.length] = id + '|' + needed;
			// });

			// data = data.join('||');

			// var title = $('#banner h1').text();
			// if ($('#banner h2').length > 0)
			// 	title += ' ' + $('#banner h2').text();

			// var csrf_token = $('meta[name="csrf-token"]').attr('content');

			// var form = $('<form action="/map" method="POST">' +
			// 	'<input type="hidden" name="_token" value="' + csrf_token + '">' +
			// 	'<input type="hidden" name="items" value="' + data + '">' +
			// 	'<input type="hidden" name="title" value="' + title + '">' +
			// 	'</form>');

			// $('body').append(form);

			// form.submit();
		});

		$('#csv_download').click(function(event) {
			event.preventDefault();

			var data = [["Item", "iLevel", "Yields", "Needed", "Purchase"]];

			$('tr.reagent').each(function() {
				var row = [],
					el = $(this);

				row.push(el.find('span.name').text());
				row.push(el.find('.ilvl').text().replace(/\s|\n/ig, ''));
				row.push(el.data('yields'));

				row.push(el.find('.total').text());

				row.push(el.find('.vendors').length ? (el.find('.vendors').text().replace(/\s|\n/ig, '') + ' gil') : '');

				data.push(row);

				return;
			});

			var filename = $('.csv-filename').text().trim() + ' ' + $('.csv-filename + h2').text().trim();

			global.exportToCsv(filename + '.csv', data);

			return;
		});

		return;
	},
	toggle_crystals:function() {
		var toggleEl = $(this),
			colspan = toggleEl.closest('th').attr('colspan'),
			tr = toggleEl.closest('tr');

		if (toggleEl.hasClass('off'))
		{
			tr.next('tr.crystals').remove();

			$('[data-item-category=Crystal]').each(function() {
				$(this).show();
				return;
			});

			toggleEl.removeClass('off');
			localStorage.removeItem('config:toggle-crystals');
		}
		else
		{
			// Create a new cell and row
			var new_cell = $('<th>', { colspan: colspan, 'class': 'text-center' });
			var new_row = $('<tr>', { 'class': 'crystals hidden' }).append(new_cell);

			// Throw the row after the current one
			tr.after(new_row);

			$('[data-item-category=Crystal]').each(function() {
				var el = $(this),
					item_id = el.data('itemId'),
					name = el.find('.name').first().html(),
					img = el.find('img').first().clone(true, true),
					total = parseInt(el.find('.total').html()),
					label_type = total == 0 ? 'success' : 'primary';

				// Hide the row.  Don't use .hidden because of the collapsible stuff
				el.hide();

				img.removeClass('margin-right');

				var span = $('<span>', {
					'id': 'crystal-' + item_id,
					'class': 'crystal-container',
					'html': '<span class="label label-' + label_type + '">' + total + '</span>',
					'rel': 'tooltip',
					'title': name
				});

				span.tooltip(global.tooltip_options);

				span.append(img);

				new_cell.append(span);

				return;
			});

			new_row.removeClass('hidden');

			toggleEl.addClass('off');

			localStorage.setItem('config:toggle-crystals', 'off');
		}

		return;
	},
	localstorage_id: null,
	set_localstorage_id:function() {
		crafting.localstorage_id = 'page:' + encodeURIComponent(window.location.pathname);

		if (crafting.localstorage_id.match('from-list') != null)
			crafting.localstorage_id = $('#CraftingList-section').find('.reagent').map(function() {
				return $(this).data('itemId') + '_' + $(this).find('.needed input').val();
			}).get().sort().join('|');
	},
	clear_localstorage:function(event) {
		event.preventDefault();

		localStorage.removeItem(crafting.localstorage_id);

		// Refresh the page
		location.reload();

		return;
	},
	restore_localstorage:function() {
		var page_items = JSON.parse(localStorage.getItem(crafting.localstorage_id));

		if (page_items === null)
			return;

		// Fill in reagents from the progress bucket
		$('.reagent').not('.exempt').each(function() {
			var el = $(this),
				itemId = el.data('itemId'),
				obtainedEl = el.find('input.obtained');

			if (typeof page_items.progress.hasOwnProperty('item' + itemId) !== 'undefined')
				if (page_items.progress['item' + itemId] > 0)
					obtainedEl.val(page_items.progress['item' + itemId]);

			return;
		});

		// Fill in reciepts from the the contents bucket
		$('.reagent.exempt').each(function() {
			var el = $(this),
				itemId = el.data('itemId'),
				neededEl = el.find('.needed input'),
				obtainedEl = el.find('input.obtained');

			if (typeof page_items.contents.hasOwnProperty('needed' + itemId) !== 'undefined')
				if (page_items.contents['needed' + itemId] > 0)
					neededEl.val(page_items.contents['needed' + itemId]);

			if (typeof page_items.contents.hasOwnProperty('item' + itemId) !== 'undefined')
				if (page_items.contents['item' + itemId] > 0)
					obtainedEl.val(page_items.contents['item' + itemId]);

			return;
		});

		return;
	},
	store_localstorage:function() {
		var page_items = {
			'progress': {}, // Obtainable items
			'contents': {}, // Needed items
		};

		// Populate the progress bucket
		$('.reagent').not('.exempt').each(function() {
			var el = $(this),
				itemId = el.data('itemId'),
				obtainedEl = el.find('input.obtained'),
				val = parseInt(obtainedEl.val());

			if (val > 0)
				page_items.progress['item' + itemId] = val;

			return;
		});

		// Populate the contents bucket
		$('.reagent.exempt').each(function() {
			var el = $(this),
				itemId = el.data('itemId'),
				neededEl = el.find('.needed input'),
				obtainedEl = el.find('input.obtained'),
				neededVal = parseInt(neededEl.val()),
				obtainedVal = parseInt(obtainedEl.val());

			if (neededVal > 0)
				page_items.contents['needed' + itemId] = neededVal;
			if (obtainedVal > 0)
				page_items.contents['item' + itemId] = obtainedVal;

			return;
		});

		localStorage.setItem(crafting.localstorage_id, JSON.stringify(page_items));

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

					// Don't include itself as a requirement
					if (data.item_id == t[1])
						continue;

					data.reagents[data.reagents.length] = {
						'item_id': t[1],
						'quantity': parseInt(t[0])
					};
				}

			crafting.reagents[crafting.reagents.length] = data;

			return;
		});

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

			recipe.elements.total.html(recipe.total < 0 ? 0 : (Math.ceil(recipe.total / recipe.yields) * recipe.yields));

			recipe.elements.row[(recipe.needed == 0 ? 'add' : 'remove') + 'Class']('success');
		}

		crafting.store_localstorage();

		// Transfer the numbers to the crystals
		if ($('tr.crystals').length > 0)
		$('[data-item-category=Crystal]').each(function() {
			var el = $(this),
				item_id = el.data('itemId'),
				total = parseInt(el.find('.total').html()),
				label = $('#crystal-' + item_id).find('.label');

			label.html(total);

			label.toggleClass('label-primary', total != 0);
			label.toggleClass('label-success', total == 0);

			return;
		});

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