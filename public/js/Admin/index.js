var INDEX = {
	init: function() {
		var width = $(window).width();
		var height = $(window).height();
		var topH = $('#header').height();
		$('#left').css({height: height - topH - 1 + 'px'});

		// $('#iframe-content iframe').css({height: height - 65 + 'px'});

		$('#left-one .toggle').on('click', function(){
			var ow = $('#left-one').width();
			if (ow == 40) {
				$(this).removeClass('close').addClass('open');
				ow = 110;
				aw = 185 + 70;
			} else {
				$(this).removeClass('open').addClass('close');
				ow = 40;
				aw = 185;
			}
			$('#left-one').css({'width': ''+ow+'px'});
			$('#left-two').css({'width': 'calc(100% - '+ow+'px);'});

			$(this).parents('td').attr('width', aw);
		});

		$('#left-one [data-title]').on('mouseover', function(){
			if ($('#left-one .toggle').hasClass('open')) return false;
			var offTop = $(this).position().top;
			var oh = $(this).height();
			var diff = (oh - 24) / 2;
			$(this).parent().find('.tooltips').remove();
			$(this).parent().append('<div class="tooltips" style="top:'+(parseInt(offTop)+diff)+'px">'+$(this).data('title')+'</div>');
		}).on('mouseleave', function(){
			$(this).parent().find('.tooltips').remove();
		});
	}
};