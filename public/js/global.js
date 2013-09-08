var global = {
	init:function() {
		$('[rel=tooltip]').tooltip();

		$('#buymeabeer').click(function(event) {
			event.preventDefault();

			$('#buymeabeer_button').trigger('click');
		})
	}
}

$(global.init);