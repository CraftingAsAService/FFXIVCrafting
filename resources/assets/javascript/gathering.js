var gathering = {
	init:function() {
		$('.collapse').click(gathering.collapse_row);

		$('.level_checkbox').click(gathering.toggle_levels);

		$('.job_checkbox').click(gathering.toggle_classes);

		$('#hide_shards').change(function() {
			$('.shard')[($(this).is(':checked') ? 'add' : 'remove') + 'Class']('hidden');
			$('.shard')[($(this).is(':checked') ? 'add' : 'remove') + 'Class']('shard-hidden');
			$('.and_more').popover('destroy');
			$('.and_more').popover();
			while($('#footer + .popover').length > 0)
				$('#footer + .popover').remove();
		});

		$('#hide_quests').change(function() {
			var hide_quests = $(this).is(':checked');

			$('.quest').each(function() {
				var neededEl = $('.amount_needed', $(this));
				var imgEl = $('img', neededEl);

				// If it's already level-hidden
				if (imgEl.hasClass('level-hidden'))
					imgEl[(hide_quests ? 'add' : 'remove') + 'Class']('quest-hidden');
				else
				{
					// It's not level hidden
					if (hide_quests == true)
					{
						neededEl
							.data('originalAdditional', neededEl.data('additional'))
							.data('additional', 0);

						imgEl.addClass('hidden').addClass('quest-hidden');
					}
					else
					{
						neededEl.data('additional', neededEl.data('originalAdditional'));
						imgEl.removeClass('quest-hidden').removeClass('hidden');
					}
				}
			});

			// Hiding or showing needs some tally love
			gathering.retally();
		});

		$('.and_more').popover();

		//gathering_tour.init();
	},
	collapse_row:function() {
		var btn = $(this);
		var tr = btn.closest('tr').next('tr');

		btn.toggleClass('glyphicon-chevron-up').toggleClass('glyphicon-chevron-down');
		tr.toggleClass('hidden');
	},
	toggle_levels:function() {
		var el = $(this);
		gathering[(el.is(':checked') ? 'show' : 'hide') + '_levels'](el);

		// Hiding or showing needs some tally love
		gathering.retally();
	},
	hide_levels:function(el) {
		// Target tr's in the tbody, as those are visible
		$('table.breakdown tbody tr.' + el.attr('id')).each(function() {
			var tr = $(this);
			var tfoot = tr.closest('table').find('tfoot');

			tfoot.append(tr);
		});

		var level = el.data('level');

		for (var i = level; i <= level + 4; i++)
		{
			$('td.amount_needed[data-level="' + i + '"]').each(function() {
				$(this)
					.data('originalAdditional', $(this).data('additional'))
					.data('additional', 0);
				$(this).find('img').addClass('hidden').addClass('level-hidden');
			});
		}
	},
	show_levels:function(el) {
		// Target tr's in the tfoot, as those are invisible
		$('table.breakdown tfoot tr.' + el.attr('id')).each(function() {
			var tr = $(this);
			var tbody = tr.closest('table').find('tbody');

			// It needs to be appended to the proper place
			var placed = false;
			if ($('tr', tbody).length > 0)
				$('tr', tbody).each(function() {
					var el = $(this);

					if (parseInt(tr.data('level')) < el.data('level'))
						return;

					el.after(tr);
					placed = true;
				});

			if (placed == false)
				tbody.prepend(tr);
		});

		var level = el.data('level');

		for (var i = level; i <= level + 4; i++)
		{
			$('td.amount_needed[data-level="' + i + '"]').each(function() {
				$(this).data('additional', $(this).data('originalAdditional'));
				var img = $(this).find('img');
				img.removeClass('level-hidden');
				if ( ! img.hasClass('quest-hidden'))
					img.removeClass('hidden');
			});
		}
	},
	toggle_classes:function() {
		var el = $(this);
		$('table.breakdown td.' + el.attr('id'))[(el.is(':checked') ? 'remove' : 'add') + 'Class']('hidden');

		// Hiding or showing needs some tally love
		gathering.retally();
	},
	retally:function() {
		$('table.breakdown').each(function() {
			var table = $(this);
			var prow = table.closest('tr').prev('tr');

			var tally = 0;
			table.find('tbody td:not(.hidden)').each(function() {
				var html = $(this).html();
				if (html != '-')
					tally += parseInt(html);
			});

			var amountEl = prow.find('.amount_needed');

			tally += parseInt(amountEl.data('additional'));

			amountEl.find('span').html(tally.formatMoney(0, ',', ''));

			if (tally == 0)
			{
				prow.addClass('hidden').addClass('zero-hidden');
			}
			else
			{
				if ( ! prow.hasClass('shard-hidden') && prow.hasClass('zero-hidden'))
					prow.removeClass('hidden');

				prow.removeClass('zero-hidden');
			}

			var costEl = prow.find('.total_cost');

			var per = parseInt(costEl.data('per'));

			if (per > 0)
				costEl.find('span').html((tally * per).formatMoney(0, ',', ''));
		});
	}
}
/*
var gathering_tour = {
	tour: null,
	first_run: true,
	init:function() {
		var startEl = $('#start_tour');

		gathering_tour.tour = new Tour({
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

			if (gathering_tour.first_run == true)
				gathering_tour.build();
			
			if ($(this).hasClass('disabled'))
				return;

			gathering_tour.tour.restart();
		});
	},
	build:function() {

		gathering_tour.tour.addSteps([
			{
				element: '#gathering-table tr:visible:first-child td:first-child', 
				title: 'Gathering List',
				content: 'The list on the left is your Recipe List.  You will be making these items.  Use the arrow to hide this information.',
				placement: 'top'
			},
			{
				element: '#obtain-these-items', 
				title: 'Obtain These Items',
				content: 'You will be grabbing the items listed in this section.  Some can be bought, gathered or killed for.',
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
				element: '#Crafted-section tr:first-child',
				title: 'Crafted Section',
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

		gathering_tour.first_run = false;
	}
}*/

$(gathering.init);

Number.prototype.formatMoney = function(decPlaces, thouSeparator, decSeparator) {
    var n = this,
    decPlaces = isNaN(decPlaces = Math.abs(decPlaces)) ? 2 : decPlaces,
    decSeparator = decSeparator == undefined ? "." : decSeparator,
    thouSeparator = thouSeparator == undefined ? "," : thouSeparator,
    sign = n < 0 ? "-" : "",
    i = parseInt(n = Math.abs(+n || 0).toFixed(decPlaces)) + "",
    j = (j = i.length) > 3 ? j % 3 : 0;
    return sign + (j ? i.substr(0, j) + thouSeparator : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thouSeparator) + (decPlaces ? decSeparator + Math.abs(n - i).toFixed(decPlaces).slice(2) : "");
};