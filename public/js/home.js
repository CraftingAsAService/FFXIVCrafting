var home = {
	init:function() {
		$('.class-selector').click(function() {
			$('.class-selector.active').removeClass('active');
		});

		$('#slider-range-min').slider({
			range: 'min',
			value: $('#forecast').val(), // When pressing "back" the value is saved.  Preserve it
			min: 0, 
			max: 5,
			slide:function(event, ui) {
				$('#forecast').val(ui.value);
				$('#forecast_plural')[(ui.value != 1 ? 'remove' : 'add') + 'Class']('hidden');
			}
		});

		$('#forecast').val($('#slider-range-min').slider('value'));
	}
}

$(home.init);