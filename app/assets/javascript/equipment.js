var equipment = {
	init:function() {
		this.events();
	},
	events:function() {
		$('.td-navigation .next').click(function() {
			var td = $(this).closest('td');
			td.trigger('mouseleave');
			var items = td.find('.items');
			equipment.el_height(items);
			var active = items.find('.item.active');
			
			var next = active.next();

			if (next.length != 1)
				next = items.children(':first-child');

			active.removeClass('active').addClass('hidden');
			next.removeClass('hidden').addClass('active');

			td.trigger('mouseenter');

			// Update 
			var total = parseInt(td.find('.td-navigation .total').html());
			var currentEl = td.find('.td-navigation .current');
			var current = parseInt(currentEl.html());

			current++;
			if (current > total)
				current = 1;

			currentEl.html(current);

			// Fire off the stat summary
			equipment.stat_summary(td);
		});

		$('.td-navigation .previous').click(function() {
			var td = $(this).closest('td');
			td.trigger('mouseleave');
			var items = td.find('.items');
			equipment.el_height(items);
			var active = items.find('.item.active');
			
			var previous = active.prev();
			
			if (previous.length != 1)
				previous = items.children(':last-child');

			active.removeClass('active').addClass('hidden');
			previous.removeClass('hidden').addClass('active');

			td.trigger('mouseenter');

			// Update 
			var total = parseInt(td.find('.td-navigation .total').html());
			var currentEl = td.find('.td-navigation .current');
			var current = parseInt(currentEl.html());

			current--;
			if (current == 0)
				current = total;

			currentEl.html(current);

			// Fire off the stat summary
			equipment.stat_summary(td);
		});

		$('td').mouseenter(this.compare);

		$('td').mouseleave(this.uncompare);

		$('#craftable_only_switch').change(function() {
			$(this).closest('form').submit();
		});

		// Trigger Stat Summary for the first cell of every column
		$('tbody tr:first-child td').each(function() {
			equipment.stat_summary($(this));
		});

		$('.stat-toggle').click(function() {
			var statEl = $(this);
			var stat = statEl.data('stat');

			statEl.toggleClass('opaque');
			$('.item .stats-box .stat[data-stat="' + stat + '"]').toggleClass('hidden');
			$('tfoot .stat[data-stat="' + stat + '"]').toggleClass('hidden');
		});

		$('#craft_these').click(function() {
			var column = $('#craft_level').val(),
				row  = $('#craft_slot').val(),
				status = $('#craft_status').val();

			// Which columns are we going to analyze
			var columns = [];
			if (column == 'all')
				$('#craft_level option:not(:first-child)').each(function() {
					columns[columns.length] = $(this).val();
				});
			else
				columns[0] = column;

			// Which rows are we going to analyze
			var rows = [];
			if (row == 'all')
				$('#craft_slot option:not(:first-child)').each(function() {
					rows[rows.length] = $(this).val();
				});
			else
				rows[0] = row;

			var ids = [];

			for (var i = 0; i < columns.length; i++)
				for (var j = 0; j < rows.length; j++) {
					var cell = $('#gear tbody tr:nth-child(' + rows[j] + ') td:nth-child(' + columns[i] + ')');
					console.log(status, cell);
					if (status == 'new' && ! cell.hasClass('alert'))
						continue;

					if (cell.find('.item.active.craftable').length > 0)
						ids[ids.length] = cell.find('.item.active').data('itemId');
				}

			ids = unique(ids);

			window.location = '/crafting/gear?' + ids.join(':') + ':1';
		});
	},
	el_height:function(el) {
		el.css('min-height', el.height());
	},
	compare:function() {
		var td = $(this);

		equipment.el_height(td.find('.items'));

		var current_box = td.find('.item.active .stats-box');
		var previous_stats = td.prev('td').find('.item.active .stats-box');

		// Loop through stats, and look at previous td's stats
		// both stats exist, do math, throw + or - in front
		// only new stat exists, throw a + in front of the amount
		// Only old stat exists, duplicate structure, throw a - in front of it

		// Check both/new scenario
		current_box.find('.stat').each(function() {
			var statEl = $(this);

			var prevEl = previous_stats.find('.stat[data-stat="' + statEl.data('stat') + '"]');

			if (prevEl.length == 0)
				statEl.find('span').html('+' + statEl.data('amount'));
			else
			{
				var new_amount = parseInt(statEl.data('amount')) - parseInt(prevEl.data('amount'));

				statEl.find('span').html((new_amount >= 0 ? '+' : '') + new_amount);
			}
		});
	},
	uncompare:function() {
		$(this).find('.stats-box .stat').each(function() {
			var statEl = $(this);
			statEl.find('span').html(statEl.data('amount'));
			//if ( ! statEl.hasClass('always_hidden'))
			//	statEl.removeClass('hidden');
		});
	},
	stat_summary:function(td) {
		if (td.hasClass('no-summary'))
			return;

		// Get the index of this td
		var index = td.closest('tr').children().index(td);

		var record = [],
			hidden = [];

		$('tbody tr td:nth-child(' + (index + 1) + ')').each(function() {
			$(this).find('.item.active .stat').each(function() {
				var statEl = $(this);
				var stat_data = statEl.data();

				if (statEl.hasClass('hidden'))
					hidden[hidden.length] = stat_data.stat;

				if (typeof(record[stat_data.stat]) === 'undefined')
					record[stat_data.stat] = 0;
				
				record[stat_data.stat] += stat_data.amount;
			});
		});

		var box = $('tfoot tr:first-child th:nth-child(' + (index + 1) + ') .stats-box');

		box.html('');

		// Loop through, put it up there
		for(var stat in record) {
			var amount = record[stat];

			var div = $('<div>');

			if (hidden.indexOf(stat) > -1)
				div.addClass('hidden');

			div
				.addClass('col-sm-6')
				.addClass('text-center')
				.addClass('stat')
				.attr('data-stat', stat)
				.attr('data-amount', amount);

			var span = $('<span>');

			span.html('+' + amount + ' &nbsp; ');

			var img = $('<img>');

			img
				.attr('src', '/img/stats/' + stat + '.png')
				.addClass('stat-icon')
				.attr('rel', 'tooltip')
				.attr('title', stat);

			div.append(span);
			div.append(img);

			box.append(div);
		}
	}
}

$(equipment.init());

function unique(array){
    return $.grep(array,function(el,index){
        return index == $.inArray(el,array);
    });
}