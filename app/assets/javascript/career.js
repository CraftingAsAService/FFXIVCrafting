var career = {
	init:function() {

		if ($('#supporter-producer-class, #receiver-recipient-class, #gatherer-class').length > 0)
			$('#supporter-producer-class, #receiver-recipient-class, #gatherer-class').multiselect({
				buttonClass: 'btn btn-sm btn-primary',
				buttonWidth: 'auto',
				buttonContainer: '<div class="btn-group" />',
				maxHeight: false,
				buttonText: function(options) {
					if (options.length == 0) {
						return 'None selected <b class="caret"></b>';
					}
					else {
						var selected = '';
						options.each(function() {
							selected += $(this).text() + ', ';
						});
						return selected.substr(0, selected.length - 2) + ' <b class="caret"></b>';
					}
				}
			});

		if ($('#supporter-supported-classes, #receiver-producer-classes, #gathering-supported-classes, #battling-supported-classes').length > 0)
			$('#supporter-supported-classes, #receiver-producer-classes, #gathering-supported-classes, #battling-supported-classes').multiselect({
				buttonClass: 'btn btn-sm btn-primary',
				buttonWidth: 'auto',
				buttonContainer: '<div class="btn-group" />',
				maxHeight: false,
				buttonText: function(options) {
					if (options.length == 0) {
						return 'None selected <b class="caret"></b>';
					}
					else if (options.length > 1) {
						return 'these ' + options.length + ' Classes  <b class="caret"></b>';
					}
					else {
						var selected = '';
						options.each(function() {
							selected += $(this).text() + ', ';
						});
						return selected.substr(0, selected.length - 2) + ' <b class="caret"></b>';
					}
				}
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

		$('.clusters').on('click', function(event) {
			event.preventDefault();
			
			var el = $(this),
				id = el.data('itemId');

			if (el.hasClass('loading'))
				return;

			var modal = $('#clusters_for_' + id);

			if (modal.length == 0)
			{

				$.ajax({
					url: '/gathering/clusters/' + id,
					dataType: 'json',
					beforeSend:function() {

						el.addClass('loading');

						global.noty({
							type: 'warning',
							text: 'Loading Clusters'
						});
					},
					success:function(json) {
						$('body').append(json.html);

						$('#clusters_for_' + id).modal();

						el.removeClass('loading');
					}
				});
			}
			else
			{
				$('#clusters_for_' + id).modal('show');
			}

			return;
		});

		$('.beasts').on('click', function(event) {
			event.preventDefault();
			
			var el = $(this),
				id = el.data('itemId');

			if (el.hasClass('loading'))
				return;

			var modal = $('#beasts_for_' + id);

			if (modal.length == 0)
			{

				$.ajax({
					url: '/gathering/beasts/' + id,
					dataType: 'json',
					beforeSend:function() {

						el.addClass('loading');

						global.noty({
							type: 'warning',
							text: 'Loading Beasts'
						});
					},
					success:function(json) {
						$('body').append(json.html);

						$('#beasts_for_' + id).modal();

						el.removeClass('loading');
					}
				});
			}
			else
			{
				$('#beasts_for_' + id).modal('show');
			}

			return;
		});
	}
}

$(career.init);