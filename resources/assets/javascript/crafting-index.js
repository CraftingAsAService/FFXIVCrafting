var basic = {
	init:function() {
		$('.bootswitch').bootstrapSwitch();

		$('.recipe-level-select a').click(function(event) {
			event.preventDefault();

			var el = $(this);

			$('.recipe-level-select a.active').removeClass('active');
			el.addClass('active');

			$('#recipe-level-start').val(el.data('start'));
			$('#recipe-level-end').val(el.data('end'));

			return;
		});
	
		return
	}
}

$(basic.init);