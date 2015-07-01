var profile = {
	init:function() {
		$('.vendors').on('click', function() {
			var el = $(this),
				id = el.closest('tr').data('itemId');

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

$(profile.init);