var hunting = {
	init:function() {
		$('.rank-switcher').on('click', hunting.switchRank);
		$('.hunt-switcher').on('change', hunting.switchRow);

		hunting.restoreSession();
	},
	restoreSession:function() {
		var hs = JSON.parse(localStorage.getItem('huntingSections') || '{}');

		for (rankClass in hs)
			if (hs.hasOwnProperty(rankClass))
				$('.rank-switcher[data-class=' + rankClass + ']')
					.data('section', hs[rankClass] - 1)
					.trigger('click');

		var hh = JSON.parse(localStorage.getItem('huntingHidden') || '{}');

		for (key in hh)
			if (hh.hasOwnProperty(key) && key.match('|')) {
				var rankClass = key.split('|')[0],
					rank = key.split('|')[1];
				$('.rank[data-class=' + rankClass + '][data-rank="' + rank + '"] .hunt-switcher')
					.prop('checked', 'checked')
					.trigger('change');
			}
	},
	switchRank:function() {
		var rankEl = $(this),
			rankClass = rankEl.data('class'),
			section = parseInt(rankEl.data('section')),
			rankMax = parseInt(rankEl.data('max'));

		if (section >= rankMax || section < 0) {
			section = 0;
			src = 'mob-inactive';
		}
		else {
			section++;
			src = section;
		}

		rankEl.data('section', section);

		rankEl.attr('src', '/img/' + src + '.png');

		$('.ranks .rank[data-class=' + rankClass + ']').addClass('hidden');
		$('.ranks .rank[data-class=' + rankClass + '][data-section=' + section + ']').removeClass('hidden');

		hunting.setRank(rankClass, section);

		hunting.toggleAreas();
	},
	setRank:function(rankClass, section) {
		var hs = JSON.parse(localStorage.getItem('huntingSections') || '{}');

		hs[rankClass] = section;

		localStorage.setItem('huntingSections', JSON.stringify(hs));
	},
	setHidden:function(rankClass, rank, hidden) {
		var hh = JSON.parse(localStorage.getItem('huntingHidden') || '{}');

		if (hidden)
			hh[rankClass + '|' + rank] = true;
		else (typeof hh[rankClass + rank] !== 'undefined')
			delete hh[rankClass + rank];

		localStorage.setItem('huntingHidden', JSON.stringify(hh));
	},
	switchRow:function() {
		var checkboxEl = $(this),
			isChecked = checkboxEl.is(':checked'),
			panelEl = checkboxEl.closest('.panel'),
			colEl = panelEl.closest('.rank'),
			rowEl = colEl.closest('.row');

		panelEl
			.toggleClass('success')
			.find('.panel-body').toggleClass('hidden', isChecked);

		rowEl.find('.panel.success').each(function() {
			$(this).closest('.rank').appendTo(rowEl);
		});

		hunting.setHidden(colEl.data('class'), colEl.data('rank'), isChecked);
	},
	toggleAreas:function() {
		$('.ranks').each(function() {
			var rankEl = $(this);
			rankEl.closest('.hunting-box').toggleClass('hidden', rankEl.find('.rank:not(.hidden)').length == 0);
		});
	}
}

$(hunting.init);