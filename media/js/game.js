function Game() {
};


// this method initializes and caches all important variables for the game object
Game.Init = function() {
	Game.elements = {};
	Game.elements.main_container = $( 'div.container-game' );
	Game.elements.panel_msg = $( '#panel-msg', Game.elements.main_container );
	Game.elements.desk = $( 'table.table-desk', Game.elements.main_container );
}

// this method determines, whether the game object should be used for this page
Game.Ready = function() {
	return Game.elements.main_container.size() == 1;
}

//this method binds all events on elements
Game.BindEvents = function() {
	Game.elements.desk.on( 'click', 'td.empty', function( e ) {
		var obj = $( this );
		var post_data = {
			'game' : Game.elements.desk.attr( 'data-game' ),
			'row' : obj.attr( 'data-row' ),
			'col' : obj.attr( 'data-col' )
					};
		console.log( post_data );
		
		
		$.ajax( {
			'type' : 'POST',
			'url' : (base_url+'/game/makeMove'),
			'data' : post_data,
			'success' : Game.FieldClick,
			'dataType' : 'JSON'
			})
			.fail( function() { alert( 'Something went terribly wrong :( ' ); });
	});	
}

Game.FieldClick = function( data ) {
	Game.elements.panel_msg.prepend( data.html );
}	

$(function(){
	Game.Init();
	if( Game.Ready() ) {
		Game.BindEvents();		
	}	
});
