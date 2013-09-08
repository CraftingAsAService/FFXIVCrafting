var calculate = {
	init:function() {
		this.events();
	},
	events:function() {
		$('.td-navigation .next').click(function() {
			var td = $(this).closest('td');
			td.trigger('mouseleave');
			var items = td.find('.items');
			calculate.el_height(items);
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
		});

		$('.td-navigation .previous').click(function() {
			var td = $(this).closest('td');
			td.trigger('mouseleave');
			var items = td.find('.items');
			calculate.el_height(items);
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
		});

		$('td').mouseenter(this.compare);

		$('td').mouseleave(this.uncompare);
	},
	el_height:function(el) {
		el.css('min-height', el.height());
	},
	compare:function() {
		var td = $(this);

		calculate.el_height(td.find('.items'));

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

				//if (new_amount == 0)
				//	statEl.addClass('hidden');
				//else
				//{
					statEl.find('span').html((new_amount >= 0 ? '+' : '') + new_amount);
				//}
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
	}
}

$(calculate.init());