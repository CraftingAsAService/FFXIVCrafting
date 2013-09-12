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

		$('.collapse').click(function() {
			var button = $(this);

			button.toggleClass('glyphicon-chevron-down').toggleClass('glyphicon-chevron-up');

			var tbody = $(this).closest('tbody');

			var trEls = tbody.find('tr:not(:first-child)');

			trEls.toggleClass('hidden');
		})
	}
}

$(crafting.init);