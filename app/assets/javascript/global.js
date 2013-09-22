var global = {
	init:function() {
		$('[rel=tooltip]').tooltip();

		$('#buymeabeer').click(function(event) {
			event.preventDefault();

			$('#buymeabeer_button').trigger('click');
		})
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