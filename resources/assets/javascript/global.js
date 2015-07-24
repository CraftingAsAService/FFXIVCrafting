var global = {
	init:function() {
		global.detect_systems();

		global.add_token_to_ajax_calls();

		$('[rel=tooltip]').tooltip({
			container: 'body'
		});

		global.reset_popovers();

		$('#buymeabeer').click(function(event) {
			event.preventDefault();

			$('#buymeabeer_button').trigger('click');
		});

		// Add to "cart"
		$(document).on('click', '.add-to-list', function() {
			var el = $(this),
				id = el.data('itemId'),
				name = el.data('itemName')
				qty = el.data('itemQuantity');

			$.ajax({
				url: '/list/add',
				type: 'post',
				data: { 'item-id' : id , 'qty' : qty },
				beforeSend:function() {
					global.noty({
						type: 'success', 
						text: 'Adding ' + (qty > 1 ? (qty + ' x ') : '') + name + ' to your list'
					});
					if (el.hasClass('success-after-add'))
					{
						el.removeClass('btn-default').addClass('btn-success');
						el.find('.glyphicon-plus').removeClass('glyphicon-plus').addClass('glyphicon-ok');
					}
				}
			});
		});

		$(document).on('click', '.click-to-view', global.click_to_view);

		$('.toggle-mobile-nav').click(function() {
			$('body').toggleClass('mobile-nav-enabled');
		});

		// /* Android mobile menu support */
		// if (/Android/i.test(navigator.userAgent)) {
		// 	$('#nav-handler').on('click', function(e) {
		// 		e.preventDefault();
		// 		var mainContainer = $('#main-container');
		// 		mainContainer[(mainContainer.hasClass('nav-handler-checked') ? 'remove' : 'add') + 'Class']('nav-handler-checked');
		// 	});
		// }
	},
	add_token_to_ajax_calls:function() {
		var csrf_token = $('meta[name="csrf-token"]').attr('content');
		$.ajaxPrefilter(function(options, originalOptions, jqXHR) {
			if (options.type.toLowerCase() === "post") {
				// add leading ampersand if `data` is non-empty
				options.data += options.data ? "&" : "";
				// add _token entry
				options.data += "_token=" + csrf_token;
			}
		});
		return;
	},
	visible_popover:null,
	reset_popovers:function(el) {
		$('[data-toggle=popover][data-content-id]').each(function() {
			$(this).data('content', $($(this).data('contentId')).html());
			$($(this).data('contentId')).remove();
		})

		// thanks to http://fuzzytolerance.info/blog/quick-hack-one-bootstarp-popover-at-a-time/

		var popovers = $('[data-toggle=popover]');

		// enable popovers
		popovers.popover({ 
			'container': 'body',
			'html': 'true'
		});

		// only allow 1 popover at a time
		// all my popovers hav
		popovers.on('click', function(event) {
			event.stopPropagation();

			var el = $(this);

			if (global.visible_popover !== null && global.visible_popover.data('contentId') == el.data('contentId'))
			{
				global.visible_popover = null;
				el.popover('hide');
			}
			else
			{
				if (global.visible_popover !== null)
					global.visible_popover.popover('hide');

				global.visible_popover = el;

				el.popover('show');

				$('[rel=tooltip]', el).tooltip();
			}
		});

		$(document).click(function(event) {
			if ($(event.target).closest('.popover').length == 0 && $(event.target).data('toggle') != 'popover')
			{
				if (global.visible_popover !== null)
					global.visible_popover.trigger('click');
				
				global.visible_popover = null;
				popovers.popover('hide');
			}
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
	},
	get_cookie:function(name) {
		name += '=';
		var cookie_array = document.cookie.split(';');

		console.log(cookie_array);

		for (var x = 0; x < cookie_array.length; x++) {
			var cookie = cookie_array[x];
			while (cookie.charAt(0) == ' ')
				cookie = cookie.substring(1, cookie.length);

			if (cookie.indexOf(name) === 0)
				return cookie.substring(name.length, cookie.length);
		}

		return null;
	},
	set_cookie:function(name, val) {
		var expire = new Date();
		expire.setDate(expire.getDate() + 365);

		val = escape(val) + '; expires=' + expire.toUTCString() + ';path=/';

		document.cookie = name + '=' + val;

		return true;
	},
	// Special thanks: http://stackoverflow.com/a/24922761/286467
	exportToCsv:function(filename, rows) {
		var processRow = function (row) {
			var finalVal = '';
			for (var j = 0; j < row.length; j++) {
				var innerValue = row[j] === null ? '' : row[j].toString();
				var result = innerValue.replace(/"/g, '""');
				if (result.search(/("|,|\n)/g) >= 0)
					result = '"' + result + '"';
				if (j > 0)
					finalVal += ',';
				finalVal += result;
			}
			return finalVal + '\n';
		};

		var csvFile = '';
		for (var i = 0; i < rows.length; i++) {
			csvFile += processRow(rows[i]);
		}

		var blob = new Blob([csvFile], { type: 'text/csv;charset=utf-8;' });
		if (navigator.msSaveBlob) { // IE 10+
			navigator.msSaveBlob(blob, filename);
		} else {
			var link = document.createElement("a");
			if (link.download !== undefined) { // feature detection
				// Browsers that support HTML5 download attribute
				var url = URL.createObjectURL(blob);
				link.setAttribute("href", url);
				link.setAttribute("download", filename);
				link.style = "visibility:hidden";
				document.body.appendChild(link);
				link.click();
				document.body.removeChild(link);
			}
		}
	},
	detect_systems:function() {
		// Browser & OS Detection
		var N= navigator.appName, ua= navigator.userAgent, tem;
		var M= ua.match(/(opera|chrome|safari|firefox|msie)\/?\s*(\.?\d+(\.\d+)*)/i);
		if(M && (tem= ua.match(/version\/([\.\d]+)/i))!= null) M[2]= tem[1];
		M= M? [M[1], M[2]]: [N, navigator.appVersion,'-?'];
		$('body').addClass(M[0]);

		// Test for IE8
		if (M[0] == 'MSIE')
			$('body').addClass('msie' + parseFloat(M[1]));

		// Test for Windows, Mac, Linux, Android
		if (navigator.platform.toUpperCase().indexOf('WIN') >= 0)
			$('body').addClass('Windows');
		if (navigator.platform.toUpperCase().indexOf('MAC') >= 0)
			$('body').addClass('Mac');
		if (navigator.platform.toUpperCase().indexOf('LINUX') >= 0)
			$('body').addClass('Linux');

		// Test for IOS, Android
		if (navigator.platform.match(/(iPhone|iPod|iPad|Pike)/i))
			$('body').addClass('IOS');
		if (navigator.platform.match(/Android/i))
			$('body').addClass('Android');

		global.is_handheld_device = navigator.userAgent.match(/Android|BlackBerry|iPhone|iPad|iPod|Opera Mini|IEMobile/i);
		$('body').addClass(global.is_handheld_device ? 'is_handheld' : 'not_handheld');

		// Special MSIE11 check
		if (!!navigator.userAgent.match(/Trident.*rv[ :]*11\./))
			$('body').addClass('msie11');

		return;
	},
	click_to_view:function(event) {
		event.preventDefault();
		
		var el = $(this),
			type = el.data('type'),
			id = el.closest('[data-item-id]').data('itemId');

		if (el.hasClass('loading'))
				return;

		var modal = $('#' + type + '-for-' + id);

		if (modal.length > 0)
			return $('#' + type + '-for-' + id).modal('show');

		// Load the entity
		$.ajax({
			url: '/entity/' + id + '/' + type,
			dataType: 'html',
			beforeSend:function() {

				el.addClass('loading');

				global.noty({
					type: 'warning',
					text: 'Loading ' + type.substring( 0, 1 ).toUpperCase() + type.substring(1)
				});

			},
			success:function(response) {
				
				$('body').append(response);

				$('#' + type + '-for-' + id).modal();

				el.removeClass('loading');

			}
		});

		return;
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