var gathering = {
	init:function() {
		$('.collapse').click(gathering.collapse_row);

		$('.level_checkbox').click(gathering.toggle_levels);

		$('.job_checkbox').click(gathering.toggle_classes);

		$('#hide_shards').click(function() {
			$('.shard')[($(this).is('checked') ? 'add' : 'remove') + 'Class']('hidden');
		});
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

			var costEl = prow.find('.total_cost');

			var per = parseInt(costEl.data('per'));

			if (per > 0)
				costEl.find('span').html((tally * per).formatMoney(0, ',', ''));
		});
	}
}

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