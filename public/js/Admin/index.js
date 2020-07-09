var INDEX = {
	init: function() {
		var width = $(window).width();
		var height = $(window).height();
		var topH = $('#header').height();
		$('#left').css({height: height - topH - 1 + 'px'});

		var iframeTop = $('#iframe-list').height();
		$('iframe').css({height: height - topH - iframeTop - 5 + 'px'})

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

		//切换iframe 
		$('#left-two .menu-content li').on('click', function(){
			var id = $(this).data('id');
			if ($('#iframe-list-content [data-id="'+id+'"]').length > 0) {
				$('#iframe-list [data-id="'+id+'"]').addClass('select');
				$('#iframe-list-content [data-id="'+id+'"]').show();
			} else {
				var src = $(this).data('src');
				var html = '<iframe src="'+src+'" frameborder="0" width="100%" height="100%" data-id="'+id+'"></iframe>';
				$('#iframe-list-content').append(html);
				var name = $(this).text();
				html = '<li class="select" data-id="'+id+'">'+name+'</li>';
				$('#iframe-list ul').append(html);
			}

			$('#iframe-list [data-id="'+id+'"]').siblings().removeClass('select');
			$('#iframe-list-content [data-id="'+id+'"]').siblings().hide();
		});

		//顶部切换页面
		$('#iframe-list').on('click', 'li', function(){
			var id = $(this).data('id');
			$(this).addClass('select').siblings().removeClass('select');
			$('#iframe-list-content [data-id="'+id+'"]').show().siblings().hide();
		});
	}
};