function Game() {
};


// this method initializes and caches all important variables for the game object
Game.Init = function() {
	Game.elements = {};
	Game.elements.main_container = $( 'div.container-game' );
	Game.elements.panel_msg = $( '#panel-msg', Game.elements.main_container );
	Game.elements.desk = $( 'table.table-desk', Game.elements.main_container );
	Game.elements.td_team = $( 'tr#trTeam td:eq(1)' );
	Game.elements.td_turn = $( 'tr#trTurn td:eq(1)' );
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
			'row' : obj.attr( 'data-row' ),
			'col' : obj.attr( 'data-col' ),
			'turn' : Game.elements.td_turn.attr( 'data-turn' )
					};
		
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

	switch( data.state ) {
		case 0: // something went wrong so just read the message
			{
				break;
			}
		case 1: // we're good to go so just mark the spot
			{
				Game.MarkField( data.row, data.col, data.game, data.team );
				Game.UpdateStats( data.turn, data.team );
				break;
			}
		case 2: // game is over
			{
				Game.MarkField( data.row, data.col, data.game, data.team );
				Game.MarkWinner(data.start_row, data.start_col, data.dir, data.game, data.fields );
				Game.UpdateStats( data.turn, data.team );
				break;
			}
		case 3: // the poll is on
			{
				break;
			}
	}
	Game.elements.panel_msg.prepend( data.html );
}	

Game.UpdateStats = function( turn, team ) {
	var obj = null;
	if( turn == 1 ) {
		obj = $( '<span />', { 'class' : 'label label-danger', 'text' : 'Red' } );
	} else {
		obj = $( '<span />', { 'class' : 'label label-primary', 'text' : 'Blue' } );
	}
	Game.elements.td_team.html( obj );
	Game.elements.td_turn.html( turn );
	Game.elements.td_turn.attr( 'data-turn', turn );
}

Game.MarkWinner = function( row, col, d, game, num ) {
	var dir = [ // row, col, dir
			[ 1, 0 ], //vertical line
			[ 0, -1 ], // horizontal line
			[ 1, 1 ], // top-left => bottom-right
			[ -1, 1 ] // bottom-left => top-right
				];
	
	for( i = 0; i < num; i++ ) {
		Game.FieldTd( row, col, game ).addClass( 'winner' );
		row += dir[d][0];
		col += dir[d][1];		
	}
}

Game.MarkField = function( row, col, game, team) {
	var obj;
	if( team == 1 ){
		obj = $('<img/>', { 'alt' : 'Blue', 'src' : base_url+'/media/images/blue.png' } );
	} else {
		obj = $('<img/>', { 'alt' : 'Red', 'src' : base_url+'/media/images/red.png' } );		
	}
	obj_td = Game.FieldTd( row, col, game );
	obj_td.html( obj );
	obj_td.removeClass( 'empty' );
}

Game.FieldTd = function( row, col, game ) {
	return $( 'table[data-game="'+game+'"] td[data-col="'+col+'"][data-row="'+row+'"]' );
}

$(function(){
	Game.Init();
	if( Game.Ready() ) {
		Game.BindEvents();		
	}	
});
