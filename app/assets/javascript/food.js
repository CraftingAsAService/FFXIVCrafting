var food = {
	init:function() {
		$('.collapse').click(function() {
			var el = $(this);

			el.toggleClass('glyphicon-chevron-up').toggleClass('glyphicon-chevron-down');

			el.closest('table').find('tbody').toggleClass('hidden');
		});
		
		$('.vendors').on('click', function(event) {
			event.preventDefault();
			var el = $(this),
				id = el.data('itemId');

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
	}
}

$(food.init);