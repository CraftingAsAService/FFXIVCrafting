var global = {
	init:function() {
		$('[rel=tooltip]').tooltip();

		$('.toggle-origin').click(function() {
			$('.not-new').toggleClass('hidden');
		});

		$('.toggle-changes').click(function() {
			var btn = $(this);

			var ison = Boolean(btn.data('on'));
			btn.data('on', ! ison);

			$('th, td').removeClass('invisible').removeClass('hidden');

			$('tbody tr').removeClass('hidden');
			$('tbody td')[(ison ? 'remove' : 'add') + 'Class']('invisible');
			$('tbody td.alert').removeClass('invisible');
			$('tbody td:first-child').removeClass('invisible');

			$('tbody tr').each(function() {
				var tr = $(this);

				if (tr.find('td.invisible').length + 1 == tr.find('td').length)
					tr.addClass('hidden');
			});

			$('thead th:not(:first-child)').each(function() {
				var th = $(this);

				var index = th.index('thead th');

				if ($('tbody').find('td:nth-child(' + (index + 1) + ').invisible').length == $('tbody').find('td:nth-child(' + (index + 1) + ')').length)
					$('td:nth-child(' + (index + 1) + '), th:nth-child(' + (index + 1) + ')').addClass('hidden');

			});
		});

		$('.toggle-range').click(function() {
			var btn = $(this);

			var ison = Boolean(btn.data('on'));
			btn.data('on', ! ison);

			$('tbody td, thead th, tfoot th').removeClass('hidden');

			if (ison)
				return;

			$('tbody td, thead th, tfoot th').addClass('hidden');
			$('tbody td:first-child, thead th:first-child, tfoot th:first-child').removeClass('hidden');
			$('tbody td.alert-success, thead th.alert-success').removeClass('hidden');
			$('tfoot th:nth-child(' + ($('.alert-success').index('thead th') + 1) + ')').removeClass('hidden');
		});
	}
}

$(global.init);