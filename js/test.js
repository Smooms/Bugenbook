$( document ).ready(function() {
	
	
	$("#freunde").live( "click" , function() {
		
		var dataObj;
		dataObj = 	getAjaxObject('http://127.0.0.1/entwicklung/Bugenbook/ajax.php?action=freunde&nutzer_id=5');
		
		var html = '';
		$.each( dataObj, function( key, value ) {
			html = html + "Der Name vom Freund ist: " + value.vorname + ' ' + value.nachname + '<br>';
		});
		
		$("#div").html( html );
		
	});
	
	$("#beitraege").live( "click" , function() {
		
		var dataObj;
		dataObj = 	getAjaxObject('http://127.0.0.1/entwicklung/Bugenbook/ajax.php?action=beitraege&nutzer_id=5');
		
		var html = '';
		$.each( dataObj, function( key, value ) {
			html = html + value.vorname + ' ' + value.nachname + " hat geschrieben: " + value.beitrag + '<br>';
		});
		
		$("#div").html( html );
		
	});
	
	
	$('#delete').live( "click" , function() {
		
		var action = 'delete';
		var beitrag_id;
		beitrag_id = $(this).attr('name');
		
		
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