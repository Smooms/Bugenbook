$( document ).ready(function() {
	
	setInterval( function() {
		var html = getAjaxObject('/entwicklung/Bugenbook/ajax/?bereich=profil_timeline&action=update');
		$('div.timeline').html( html );
	}, 10000);
	
	
	$('#delete').live( "click" , function() {
		
		var beitrag_id;
		beitrag_id = $(this).attr('name');
		
		var erfolg = getAjaxObject('/entwicklung/Bugenbook/ajax/?bereich=beitraege&action=delete&beitrag_id=' + beitrag_id );
		
		if (erfolg === 1) {
			$('#beitrag_' + beitrag_id).fadeOut().done(css( 'display', 'none'));
		}
		
	});
	
	function getAjaxObject( url ) {
		
		var AjaxObj = null;
		
		$.ajax({ url: url, async: false }).done(function( data ) {
			
			AjaxObj = JSON.parse( data );
			
			return AjaxObj;
		
		});
		return AjaxObj;
	}
	
	
});