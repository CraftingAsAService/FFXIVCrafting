var leves = {
	init:function() {
		$('.class-selector').click(function() {
			$('.leve-table').addClass('hidden');
			$('#' + $(this).data('job')).removeClass('hidden');
		});

		$('.class-selector').first().trigger('click');
	}
}

$(leves.init);