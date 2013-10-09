var global = {
	init:function() {
		$('[rel=tooltip]').tooltip();

		$('#buymeabeer').click(function(event) {
			event.preventDefault();

			$('#buymeabeer_button').trigger('click');
		});

		// Add to "cart"
		$(document).on('click', '.add-to-list', function() {
			var id = $(this).data('itemId'),
				name = $(this).data('itemName');

			$.ajax({
				url: '/list/add',
				type: 'post',
				data: { 'item-id' : id },
				beforeSend:function() {
					global.noty({
						type: 'success',
						text: 'Adding ' + (qty > 1 ? (qty + ' x ') : '') + name + ' to your list'
					});
				}
			});
		});
	},
	noty:function(options) {
		noty({
			text: options.text,
			type: options.type,
			layout: 'bottomCenter',
			timeout: 2500
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

// Tour Moogle Addition
$(function() {
	var tma = $.fn.popover.Constructor.prototype.show;
	$.fn.popover.Constructor.prototype.show = function() {
		// Get original template
		var template = $('<div>').html('<div class="popover tour-tour">          <div class="arrow"></div>          <h3 class="popover-title"></h3>          <div class="popover-content"></div>          <nav class="popover-navigation">            <div class="btn-group">              <button class="btn btn-sm btn-default disabled" data-role="prev">« Prev</button>              <button class="btn btn-sm btn-default" data-role="next">Next »</button>            </div>            <button class="btn btn-sm btn-default" data-role="end">End tour</button>          </nav>        </div>');

		// Add Moogle
		template.find('h3').before('<img src="/img/tour_moogle.png" class="tour_moogle">');

		this.options.template = template.html();

		// Call
		tma.call(this);
	};
});

String.prototype.capitalize = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
}
