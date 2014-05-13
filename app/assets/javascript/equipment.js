var equipment = {
	options: {
		job: job,
		level: level,
		craftable_only: craftable_only,
		rewardable_too: rewardable_too,
		level_range: 3,
		viewport_differential: 0, // Change level range based on viewport
		boring_stats: false
	},
	init:function() {
		$.fn.inlineStyle = function (prop) {
			return this.prop("style")[$.camelCase(prop)];
		};

		// Tweak for mobile might happen
		$(document).on('viewportchanged', equipment.viewport_changed);
		equipment.viewport_changed();

		equipment.table_events();
		equipment.page_events();
		
		equipment_tour.init();
	},
	viewport_changed:function() {
		// Reaffirm slim mode
		equipment.slim($('#toggle-slim').bootstrapSwitch('status'));
	},
	cell_events:function() {
		equipment.mark_upgrades();
		equipment.same_cell_heights();

		if (typeof(initXIVDBTooltips) != 'undefined')
			initXIVDBTooltips();

		$('.vendors').on('click', function() {
			var el = $(this),
				id = el.closest('.item').data('itemId');

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

		$('#gear tbody td:visible .td-navigation .item-next').on('click', function(event) {
			event.preventDefault();

			var td = $(this).closest('td');
			td.trigger('mouseleave');
			var items = td.find('.items');
			//equipment.el_height(items);
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

			equipment.mark_cannot_equips();

			// Fire off the stat summary
			equipment.stat_summary(td);

			// 
			$('#gear td:visible .role-wrap').css('height', 'inherit');
			equipment.same_cell_heights();
		});

		$('#gear tbody td:visible').on('mouseenter', equipment.compare);

		$('#gear tbody td:visible').on('mouseleave', equipment.uncompare);

		$('[rel=tooltip]').tooltip();

		$('#gear tbody tr:first-child td:visible').each(function() {
			equipment.stat_summary($(this));
		});
	},
	table_events:function() {

		equipment.cell_events();

		$('.previous-gear').click(function() {
			var el = $(this);

			if (el.hasClass('disabled'))
				return;

			$('.previous-gear, .next-gear').addClass('disabled');
			
			var lvl = equipment.options.level - 1;

			if (lvl < 1)
			{
				$('.previous-gear, .next-gear').removeClass('disabled');
				return;
			}

			equipment.options.level = lvl;

			if ($('td[data-level=' + lvl + ']').length > 0)
			{
				equipment.column_display();
				equipment.fix_rows();
				equipment.mark_upgrades();
				equipment.same_cell_heights();

				// preload the next..previous level
				if (lvl - 1 > 0 && $('td[data-level=' + (lvl - 1) + ']').length == 0)
					equipment.load_column(lvl - 1, 'Pre');
				else
					$('.previous-gear, .next-gear').removeClass('disabled');
			}
			else
				equipment.load_column(lvl);
		});

		$('.next-gear').click(function() {
			var el = $(this);

			if (el.hasClass('disabled'))
				return;

			$('.previous-gear, .next-gear').addClass('disabled');
			
			var lvl = equipment.options.level + 1;

			if (lvl > 48)
			{
				$('.previous-gear, .next-gear').removeClass('disabled');
				return;
			}

			equipment.options.level = lvl;

			if ($('td[data-level=' + (lvl + equipment.options.level_range - 1) + ']').length > 0)
			{
				equipment.column_display();
				equipment.fix_rows();
				equipment.mark_upgrades();
				equipment.same_cell_heights();

				// preload the next level
				if (lvl + equipment.options.level_range <= 50 && $('td[data-level=' + (lvl + equipment.options.level_range - 1) + ']').length == 0)
					equipment.load_column(lvl + equipment.options.level_range - 1, 'Pre');
				else
					$('.previous-gear, .next-gear').removeClass('disabled');
			}
			else
				equipment.load_column(lvl + equipment.options.level_range - 1);
		});
	},
	slim:function(no) {
		$('#gear')[(no ? 'add' : 'remove') + 'Class']('slim');
		$('td:visible .role-wrap').css('height', 'inherit');
		equipment.options.level_range = no ? 4 : 3;
		// Change the differential
		equipment.options.viewport_differential = viewport.current == 'mobile' ? 3 - (no ? 0 : 1) : 0;
		equipment.column_display();
		equipment.same_cell_heights();
	},
	same_cell_heights:function() {
		$('#gear tbody tr').each(function() {
			var tr = $(this),
				h = 0;

			$('td:visible .role-wrap', tr).each(function() {
				var sw = $(this);
				var adjust = 0;

				if ($(this).inlineStyle('height') != '')
					adjust = 16;

				var swh = sw.innerHeight() - adjust;

				if (swh > h)
					h = swh;
			});

			$('td:visible .role-wrap', tr).height(h);
		});
	},
	mark_upgrades:function() {
		$('#gear tbody td:visible').each(function() {
			var td = $(this);
			var level = td.data('level');
			var ptd = td.closest('tr').find('td[data-level=' + (level - 1) + ']');

			if (ptd.length == 0 && level != 1)
				return;

			var ilvl = td.find('.item:first-child').data('itemIlvl');
			var pilvl = ptd.find('.item:first-child').data('itemIlvl');

			td[(ilvl == pilvl ? 'remove' : 'add') + 'Class']('upgrade');
		});

		equipment.mark_cannot_equips();
	},
	mark_cannot_equips:function() {
		// Clear any previous equips
		$('#gear tbody td.cannot-equip').removeClass('cannot-equip');
		$('.why-cannot-equip').remove();

		// Go through each cannot equip item, mark other cells as cannot_equip
		$('#gear tbody td:visible .item.active:not([data-cannot-equip=""])').each(function() {
			var itemEl = $(this);
			var name = $('.name-box a', itemEl).html();
			var cannot_equip = ! $.isNumeric(itemEl.data('cannotEquip')) ? itemEl.data('cannotEquip').split(',') : [];

			var td = itemEl.closest('td');
			var level = td.data('level');

			for(var i = 0; i < cannot_equip.length; i++)
			{
				var el = $('.role-' + cannot_equip[i].capitalize().replace(/ /, '-') + '[data-level=' + level + ']');
				el.append('<div class="why-cannot-equip">' + name + '<br>Cannot equip gear to ' + cannot_equip[i].capitalize() + '</div>')
				el.addClass('cannot-equip');
			}

			equipment.stat_summary(td);
		});
	},
	load_column:function(level, verb) {
		if (typeof(verb) == 'undefined') verb = '';

		if (viewport.current == 'mobile') verb = 'Pre';
		
		if (parseInt(level) > 50)
		{
			$('.previous-gear, .next-gear').removeClass('disabled');
			return;
		}

		$.ajax({
			url: '/equipment/load',
			type: 'post',
			dataType: 'json',
			data: {
				'job': equipment.options.job,
				'level': level,
				'craftable_only': equipment.options.craftable_only,
				'rewardable_too': equipment.options.rewardable_too
			},
			beforeSend:function() {
				global.noty({
					type: 'warning',
					text: verb + 'Loading Level ' + level
				});
			},
			success:function(json) {
				$.each(json.gear, function(key, value) {
					$('#gear .role-row[data-role="' + key + '"]').append(value[level]);
				});

				$('#gear thead tr').append(json.head[level]);
				$('#gear tfoot tr').append(json.foot[level]);

				equipment.column_display();
				equipment.fix_rows();

				equipment.cell_events();

				$('.previous-gear, .next-gear').removeClass('disabled');
			}
		});
	},
	fix_rows:function() {
		// Fix the cells to a proper order
		$('#gear tr').each(function() {
			var tr = $(this);
			
			var list = tr.find('th:visible, td:visible').get();
			
			list.sort(function(a, b) {
				var al = parseInt($(a).data('level')),
					bl = parseInt($(b).data('level'));
				return bl > al ? -1 : bl < al ? 1 : 0;
			});

			for (var i = 0; i < list.length; i++)
				list[i].parentNode.appendChild(list[i]);
		});
	},
	column_display:function(level) {
		var start = equipment.options.level;

		if (start > 50 - equipment.options.level_range + 1)
			start = 50 - equipment.options.level_range + 1;

		$('#gear td[data-level]').addClass('hidden');
		$('#gear th[data-level]').addClass('hidden');

		//console.log(equipment.options.level, equipment.options.level_range, equipment.options.viewport_differential);

		for (var i = start; i < equipment.options.level + equipment.options.level_range - equipment.options.viewport_differential; i++)
		{
			$('#gear td[data-level=' + i + ']').removeClass('hidden');
			$('#gear th[data-level=' + i + ']').removeClass('hidden');
		}
	},
	compare:function() {
		var td = $(this);

		//equipment.el_height(td.find('.items'));

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
				$('span', statEl)
					.html(statEl.data('amount'))
					.addClass('text-success');
			else
			{
				var new_amount = parseInt(statEl.data('amount')) - parseInt(prevEl.data('amount'));

				$('span', statEl)
					.html(new_amount)
					.addClass('text-' + (new_amount > 0 ? 'success' : (new_amount < 0 ? 'danger' : 'warning')));
			}
		});
	},
	uncompare:function() {
		$(this).find('.stats-box .stat').each(function() {
			var statEl = $(this);
			$('span', statEl)
				.html(statEl.data('amount'))
				.removeClass('text-success')
				.removeClass('text-danger')
				.removeClass('text-warning');
		});
	},
	stat_summary:function(td) {
		var level = $(td).data('level');

		var record = [],
			hidden = [];

		$('#gear tbody tr td[data-level=' + level + ']:not("cannot-equip")').each(function() {
			$(this).find('.item.active .nq.stat').each(function() {
				var statEl = $(this);
				var stat_data = statEl.data();

				if (statEl.hasClass('hidden'))
					hidden[hidden.length] = stat_data.stat;

				if (typeof(record[stat_data.stat]) === 'undefined')
					record[stat_data.stat] = 0;
				
				record[stat_data.stat] += stat_data.amount;
			});
		});

		var box = $('#gear tfoot tr:first-child th[data-level=' + level + '] .stats-box');

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
				.attr('src', '/img/stats/nq/' + stat + '.png')
				.addClass('stat-icon')
				.attr('rel', 'tooltip')
				.attr('title', stat);

			div.append(span);
			div.append(img);

			box.append(div);
		}
	},
	all_stats:function() {
		// equipment.options.boring_stats
		$('#gear td:visible .stats-box .boring').toggleClass('hidden');
		$('#gear td:visible .role-wrap').css('height', 'inherit');
		equipment.same_cell_heights();
	},
	page_events:function() {
		$('#toggle-slim').on('switch-change', function(e, data) {
			equipment.slim(data.value);
			$('html, body').animate({ scrollTop: $('#table-options').offset().top }, 'slow');
		});

		if ($('#toggle-slim').bootstrapSwitch('status'))
			equipment.options.level_range = 4;

		$('#toggle-all-stats').on('switch-change', function(e, data) {
			equipment.options.boring_stats = data.value;
			equipment.all_stats();
			$('html, body').animate({ scrollTop: $('#table-options').offset().top }, 'slow');
		});

		$('#craftable_only_switch').change(function() {
			$(this).closest('form').submit();
		});
	}
}

var equipment_tour = {
	tour: null,
	first_run: true,
	init:function() {
		var startEl = $('#start_tour');

		equipment_tour.tour = new Tour({
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

			if (equipment_tour.first_run == true)
				equipment_tour.build();
			
			if ($(this).hasClass('disabled'))
				return;

			equipment_tour.tour.restart();
		});
	},
	build:function() {
		var upgradeEl = $('#gear td:visible.upgrade')[0];
		var statsBoxEl = $('.item.active .stats-box .stat', upgradeEl)[0];
		var craftingEl = $('#gear td:visible .item.active .crafted_by')[0];

		equipment_tour.tour.addSteps([
			{
				element: upgradeEl, 
				title: 'Upgrade',
				content: 'Boxes with a border and circle icon indicate an upgrade.'
			},
			{
				element: statsBoxEl, 
				title: 'Stats',
				content: 'These are the stats for the item.  Hovering on them will tell you how much of an upgrade it is from the previous item.',
				placement: 'bottom'
			},
			{
				element: craftingEl,
				title: 'Crafted By & Buyable',
				content: '<p>This item can be crafted by the class indicated. <strong>Clicking on it will add that item to your Crafting Cart!</strong></p>' + 
							'<p>The coins indicate that you can buy them from a vendor.</p>'
			},
			{
				element: '.previous-gear', 
				title: 'Level Traversing',
				content: 'Click this bar (and the one on the right) to decrease (or increase) the gear level.'
			},
			{
				element: '#toggle-slim',
				title: 'Slim Mode',
				content: 'If you don\'t want to see stats, turn this mode on.',
				placement: 'top'
			},
			{
				element: '#toggle-all-stats',
				title: 'Boring Stats',
				content: 'Boring stats are hidden by default.  Use this option to turn them on.  An example of a boring stat would be Magic Damage to a Disciple of War class.',
				placement: 'top'
			}
		]);

		equipment_tour.first_run = false;
	}
}

$(equipment.init);

function unique(array){
    return $.grep(array,function(el,index){
        return index == $.inArray(el,array);
    });
}