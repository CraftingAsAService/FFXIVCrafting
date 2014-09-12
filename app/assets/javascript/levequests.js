var levequests = {
	init:function() {
		levequests.events();

		levequests.decipher_hash();

		return;
	},
	decipher_hash:function() {
		var hash = document.location.hash;

		if (hash == '')
			return false;

		// Take off the #, explode
		// hash = hash.slice(1).split('-');

		// $('a[href=#' + hash[0] + '-tab]').trigger('click');

		// $('th[data-section=#' + hash[0] + '-' + hash[1] + ']').trigger('click');

		return true;
	},
	events:function() {

		// On clicking a class icon, fill in the level
		$('.class-selector').click(function() {
			var el = $(this),
				level = parseInt(Math.floor(el.data('level') / 5) * 5);

			if (level == 0)
				level = 1;

			var level_el = $('.leve-level-select a[data-level=' + level + ']');

			$('.class-selector.active').removeClass('active');
			el.addClass('active');

			$('.leve-level-select a.active').removeClass('active');
			level_el.addClass('active');

			levequests.load_leves();
			
			return;
		});

		$('.leve-level-select a').click(function(event) {
			event.preventDefault();

			var el = $(this);

			$('.leve-level-select a.active').removeClass('active');
			el.addClass('active');

			levequests.load_leves();

			return;
		});

		$('.class-selector.active').trigger('click');

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
	},
	load_leves:function() {
		var job = $('.jobs-list label.active').data('class'),
			level = $('.leve-level-select a.active').data('level'),
			// section = $('#' + job + '-leves'),
			subsection = $('#' + job + '-' + level + '-leves');

		// // Hide/show leve sections
		// $('.leve-section').addClass('hidden');
		// section.removeClass('hidden');

		// Hide/show leves
		$('.leve-section .table-responsive').addClass('hidden');
		subsection.removeClass('hidden');

		// Reveal the images
		$('img[src=""]', subsection).each(function() {
			this.src = $(this).data('src');
			return;
		});

		return;
	}
}

$(levequests.init);