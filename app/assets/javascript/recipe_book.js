var recipe_book = {
	init:function() {
		recipe_book.events();

		recipe_book.decipher_hash();

		return;
	},
	hash_per_page: null,
	decipher_hash:function() {
		var hash = document.location.hash;

		if (hash == '')
			return false;

		// Take off the #, explode
		hash = hash.slice(1).split('|');

		// Fill in the fields
		$('#name-search input').val(hash[0]);
		$('#min-level').val(hash[2]);
		$('#max-level').val(hash[3]);

		recipe_book.hash_per_page = hash[4];

		if ($('#class-search button').data('class') == hash[1])
			recipe_book.search();
		else
			$('#class-search [data-class=' + hash[1] + ']').trigger('click');

		recipe_book.hash_per_page = null;

		return true;
	},
	events:function() {
		$('#name-search input').keyup(function(e) {
			if (e.which != 13)
				return true;

			if (recipe_book.check_input_length() == true)
				recipe_book.search();
		});
		
		$('#name-search button').click(function() {
			if (recipe_book.check_input_length() == true)
				recipe_book.search();
		});

		$('#min-level, #max-level').change(function() {
			var el = $(this);
			var this_min = parseInt(el.attr('min')),
				this_max = parseInt(el.attr('max')),
				val = parseInt(el.val());

			// Prevent overlapping inputs
			if (el.is('#max-level'))
			{
				var min_el_val = parseInt($('#min-level').val());
				if (val < min_el_val)
				{
					el.val(min_el_val);
					val = min_el_val;
				}
			}
			else
			{
				var max_el_val = parseInt($('#max-level').val());
				if (val > max_el_val)
				{
					el.val(max_el_val);
					val = max_el_val;
				}
			}
			
			// Prevent going over/under min/max attributes
			if (val < this_min) val = this_min;
			if (val > this_max) val = this_max;

			el.val(val);

			recipe_book.search();
		});

		$('#class-search li a').click(function(e) {
			e.preventDefault();
			var el = $(this),
				main_button = $('#class-search button');

			var new_cls = el.data('class'),
				new_img = $('img', el),
				old_cls = main_button.data('class')
				old_img = $('img', main_button);

			if (new_cls != old_cls)
			{
				new_img.clone().insertAfter(old_img);
				old_img.remove();

				main_button.data('class', new_cls);

				recipe_book.search();
			}
		});

		$(document).on('change', '#per_page', function() {
			recipe_book.search();
		});
	},
	check_input_length:function() {
		var val = $('#name-search input').val();
		
		$('#name-search').removeClass('has-error');

		if (val.length < 3 && val.length > 0)
		{
			$('#name-search').addClass('has-error');

			global.noty({
				type: 'error',
				text: 'Minimum 3 letter search limit'
			});

			return false;
		}

		return true;
	},
	search:function(qs) {
		// Get info
		var name = $('#name-search input').val(),
			min = $('#min-level').val(),
			max = $('#max-level').val(),
			cls = $('#class-search button').data('class'),
			per_page = $('#per_page').val();

		if (recipe_book.hash_per_page != null)
			per_page = recipe_book.hash_per_page;

		if (typeof(qs) === 'undefined') 
			qs = '';

		document.location.hash = [
				name,
				cls,
				min, 
				max, 
				per_page
			].join('|');

		$.ajax({
			url: '/recipes/search' + qs,
			type: 'post',
			dataType: 'json',
			data: { 
				name : name,
				min : min,
				max : max,
				'class' : cls,
				'per_page' : per_page
			},
			beforeSend:function() {
				recipe_book.disable();

				global.noty({
					type: 'information',
					text: 'Searching Recipe Book'
				});
			},
			success:function(json) {
				$('#recipe-book tbody').html(json.tbody);
				$('#recipe-book tfoot').html(json.tfoot);
				recipe_book.table_events();
			},
			complete:function() {
				recipe_book.enable();
			}
		});
	},
	table_events:function() {
		$('#recipe-book tfoot .pagination a').click(function(e) {
			e.preventDefault();
			recipe_book.search(e.target.search);
		});
		
		if (typeof(initXIVDBTooltips) != 'undefined')
			initXIVDBTooltips();
	},
	disable:function() {
		$('#name-search input, #name-search button, #min-level, #max-level, #class-search button').addClass('disabled');
		$('#name-search input, #min-level, #max-level').prop('disabled', true);
	},
	enable:function() {
		$('#name-search input, #name-search button, #min-level, #max-level, #class-search button').removeClass('disabled');
		$('#name-search input, #min-level, #max-level').prop('disabled', false);
	}
}

$(recipe_book.init)