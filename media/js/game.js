$(function(){
	var main_container = $( 'div.container-game' );
	if( main_container.size() ) { // consider following code only if we're playing the game
		var desk = $( 'table.table-desk', main_container );
		desk.on( 'click', 'td.empty', function( e ) {
			var obj = $( e );
			var data = {
				'row' : e.data( 'row' ),
				'col' : e.data( 'col' )
						};
			
			$.post( '{{ @BASE/game/makeMove}}', {  } ).success( function( data ) {
				alert( data );
			} );
		});
	}
	
});
