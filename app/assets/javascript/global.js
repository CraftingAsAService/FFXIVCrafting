var global = {
	init:function() {
		$('[rel=tooltip]').tooltip();

		$('#buymeabeer').click(function(event) {
			event.preventDefault();

			$('#buymeabeer_button').trigger('click');
		});

		$(document).on('click', '.add-to-list', function() {
			var id = $(this).data('itemId'),
				name = $(this).data('itemName')
				qty = $(this).data('itemQuantity');

			$.ajax({
				url: '/list/add',
				type: 'post',
				data: { 'item-id' : id , 'qty' : qty },
				beforeSend:function() {
					noty({
						text: 'Adding ' + (qty > 1 ? (qty + ' x ') : '') + name + ' to your list',
						type: 'success',
						layout: 'bottomRight',
						timeout: 2500
					});
				}
			});
		});
	},
	notification:function(type, message, id) {
		$('#notifications').append('<div class="alert alert-' + type + '" id="' + id + '">' + message);
	},
	fade_and_destroy:function(el)
	{
		el.fadeOut(500, function() { el.remove(); });
	}
}

$(global.init);