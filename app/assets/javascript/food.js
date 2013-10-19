var food = {
	init:function() {
		$('.collapse').click(function() {
			var el = $(this);

			el.toggleClass('glyphicon-chevron-up').toggleClass('glyphicon-chevron-down');

			el.closest('table').find('tbody').toggleClass('hidden');
		});
	}
}

$(food.init);