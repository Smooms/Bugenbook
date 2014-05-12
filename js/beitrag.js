$( document ).ready(function() {
	
	fancybox();
	
	setInterval( function() {
		var html = getAjaxObject('/entwicklung/Bugenbook/ajax/?bereich=beitraege&action=update');
		$('div.timeline').html( html );
		fancybox();
	}, 10000);
	
	
	$('#delete').live( "click" , function() {
		
		var beitrag_id;
		beitrag_id = $(this).attr('name');
		
		var erfolg = getAjaxObject('/entwicklung/Bugenbook/ajax/?bereich=beitraege&action=delete&beitrag_id=' + beitrag_id );
		
		if (erfolg === 1) {
			$('#beitrag_' + beitrag_id).fadeOut().done(css( 'display', 'none'));
		}
		
	});
	
	
	$('#up').live( "click" , function() {
		
		var beitrag_id;
		beitrag_id = $(this).attr('name');
		
		var erfolg = getAjaxObject('/entwicklung/Bugenbook/ajax/?bereich=beitraege&action=up&beitrag_id=' + beitrag_id );
		
		var html = $("#up_" + beitrag_id).html();
		var html_up = parseInt(html);
		
		if (erfolg === 1) {
			html_up++;
			
			$(this).toggleClass( 'up_no', false);
			$(this).toggleClass( 'up_yes', true);
		}
		else if (erfolg === 0){
			html_up--;
			$(this).toggleClass( 'up_yes', false);
		}
		else if (erfolg === 2) {
			html_up++;
			
			var html2 = $("#down_" + beitrag_id).html();
			var html_down = parseInt(html2);
			html_down--;
			$("#down_" + beitrag_id).html( html_down );
			$(this).toggleClass( 'up_no', false);
			$(this).toggleClass( 'up_yes', true);
			
			var brother = $(this).parent().find('#down');
			console.log(brother);
			$(brother).toggleClass( 'down_no', true);
			$(brother).toggleClass( 'down_yes', false);
		}
		
		$("#up_" + beitrag_id).html( html_up );
	});
	
	$('#down').live( "click" , function() {
		
		var beitrag_id;
		beitrag_id = $(this).attr('name');
		
		var erfolg = getAjaxObject('/entwicklung/Bugenbook/ajax/?bereich=beitraege&action=down&beitrag_id=' + beitrag_id );
		
		var html = $("#down_" + beitrag_id).html();
		var html_down = parseInt(html);
		
		if (erfolg === 1) {
			html_down++;
			$(this).toggleClass( 'down_no', false);
			$(this).toggleClass( 'down_yes', true);;
		}
		else if (erfolg === 0) {
			html_down--;
			$(this).toggleClass( 'down_yes', false);
		}
		else if (erfolg === 2) {
			html_down++;
			
			var html2 = $("#up_" + beitrag_id).html();
			var html_up = parseInt(html2);
			html_up--;
			$("#up_" + beitrag_id).html( html_up );
			$(this).toggleClass( 'down_no', false);
			$(this).toggleClass( 'down_yes', true);
			
			var brother = $(this).parent().find('#up');
			$(brother).toggleClass( 'up_no', true);
			$(brother).toggleClass( 'up_yes', false);
		}
		
		$("#down_" + beitrag_id).html( html_down );
		
	});
	
	
	function fancybox()
	{
		$('#comment').fancybox({
			'transitionIn'		: 'none',
			'transitionOut'		: 'none',
			'titlePosition' 	: 'over',
			'type'				: 'iframe',
			'content'			: 'erwgfer'
		});
	};
	
	
	
	function getAjaxObject( url ) {
		
		var AjaxObj = null;
		
		$.ajax({ url: url, async: false }).done(function( data ) {
			
			AjaxObj = JSON.parse( data );
			
			return AjaxObj;
		
		});
		return AjaxObj;
	}
	
	
});