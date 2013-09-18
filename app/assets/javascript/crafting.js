var crafting = {
	init:function() {
		$('#self_sufficient_switch').change(function() {
			$(this).closest('form').submit();
		});

		$('.reagent').click(function(event) {
			var tr = $(this);

			// Prevent HREF or Checkbox click
			if (event.target.nodeName == 'A' || event.target.nodeName == 'TH')
				return;

			if (event.target.nodeName == 'INPUT')
				event.preventDefault();

			tr.toggleClass('success');

			var checkbox = tr.find('[type=checkbox]')[0];

			checkbox.checked = ! checkbox.checked;
		});

		$('.item-list .collapse').click(function() {
			var button = $(this);

			button.toggleClass('glyphicon-chevron-down').toggleClass('glyphicon-chevron-up');

			var tbody = $(this).closest('tbody');

			var trEls = tbody.find('tr:not(:first-child)');

			trEls.toggleClass('hidden');
		});

		$('.crafting-list .collapse').click(function() {
			var button = $(this);

			button.toggleClass('glyphicon-chevron-down').toggleClass('glyphicon-chevron-up');

			var h3 = $(this).closest('h3');

			var trEls = h3.next();

			trEls.toggleClass('hidden');

		});
	}
}

$(crafting.init);