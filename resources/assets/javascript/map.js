var map = {
	init:function() {
		$('a[data-toggle=tab]').on('shown.bs.tab', function(e) {
			var href = $(e.target).attr('href');
			$(href).find('.globe:not(.overscroll)').overscroll();
			$(href).find('.globe').addClass('overscroll');
		});

		$('.active a[data-toggle=tab]').trigger('shown.bs.tab');

		// Go through the item_level, look for items.  No nodes, delete the item_level
		// Same all the way down: region_level, cluster_level
		$('.item_level, .region_level, .cluster_level').each(function() {
			var el = $(this);
			if (el.find('.node_level').length == 0)
				el.remove();
			return;
		});

		$('.item_level > div:first-child, .region_level > div:first-child, .cluster_level > div:first-child').click(function() {
			var el = $(this);
			el.parent('li').toggleClass('open');
			return;
		});

		$('.cluster, .vendor, .beast').click(map.icon_click);

		$('.clear-selected').click(map.clear_selected);

		$('.globe_list input[type=checkbox]').change(map.toggle_level);

		return;
	},
	toggle_node:function(el, wanted) {
		var id = el.data('id'),
			type = el.data('type');
			map_item = $('.map-item[data-id=' + id + '][data-type=' + type + ']');

		map_item[(wanted ? 'remove' : 'add') + 'Class']('hidden');

		return;
	},
	toggle_level:function(event, handle_items) {
		if (event) event.stopPropagation();

		var el = $(this),
			par = el.closest('.list-group-item'),
			node_item = par.find('.node-item'),
			id = node_item.data('id'),
			type = node_item.data('type');

		if (el.prop('checked') == false)
			map.resort_to_last(par);

		// We're going to skip a step and just assume the default for handle_items is empty
		if (typeof(handle_items) === 'undefined' || handle_items === true)
		{
			par.find('.node-item').each(function() {
				return map.toggle_node($(this), el.prop('checked'), $('.node-item[data-id=' + id + '][data-type=' + type + ']').length);
			});

			// If an item is turned off in one place, also turn off the other items
			$('.node-item[data-id=' + id + '][data-type=' + type + ']').not(node_item).each(function() {
				var box_el = $(this),
					box = box_el.closest('.list-group-item').find('input[type=checkbox]');
				box.prop('checked', el.prop('checked'));
				return map.toggle_level.apply(box, [ false, false ]);
			});
		}

		return;
	},
	clear_selected:function(event) {
		if (event) event.preventDefault();

		// Hide the clear selection item
		$('.clear-selected').addClass('hidden');

		// Un-highlight any items
		$('.list-group-item.alert-success').removeClass('alert-success')
		$('.list-group-item.item_level.alert-info').removeClass('alert-info');

		return;
	},
	icon_click:function() {
		var icon = $(this),
			id = icon.data('id'),
			type = icon.data('type');

		// Close all open items
		$('.list-group-item.open').removeClass('open');

		// Unhighlight any items
		map.clear_selected();

		// Find parents, open them
		$('.node-item[data-id=' + id + '][data-type=' + type + ']').parents().map(function() {
			var el = $(this);
			if (el.hasClass('list-group-item'))
				el.addClass('open');

			return;
		});

		// Highlight the base parent
		$('.node-item[data-id=' + id + '][data-type=' + type + ']').parents('.list-group-item.item_level').addClass('alert-info');
		// Highlight the opened items
		$('.node-item[data-id=' + id + '][data-type=' + type + ']').closest('.list-group-item').removeClass('alert-info').addClass('alert-success');

		// Resort at each level, move relevant items to the top
		$('.node-item[data-id=' + id + '][data-type=' + type + ']').closest('.list-group-item').each(function() {
			return map.resort_to_first_recursive($(this));
		});

		// Show the clear selection item
		$('.clear-selected').removeClass('hidden');

		return;
	},
	resort_to_first_recursive:function(el) {
		var par = el.closest('.list-group');
		
		par.prepend(el);

		var closest = par.closest('.list-group-item');
		
		if (closest.length > 0)
			map.resort_to_first_recursive(closest);

		return;
	},
	resort_to_last:function(el) {
		var par = el.closest('.list-group');
		
		par.append(el);

		return;
	}
}

$(map.init);