var global = {
	init:function() {
		$('[rel=tooltip]').tooltip();

		$('.toggle-origin').click(function() {
			$('.not-new').toggleClass('hidden');
		});
	}
}

$(global.init);