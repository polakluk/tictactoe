function Game() {
};


// this method initializes and caches all important variables for the game object
Game.Init = function() {
	Game.elements = {};
	Game.elements.game_container = $( 'div.container-game' );
	Game.elements.showroom = $( 'div.container-showroom' );
	Game.elements.panel_msg = $( '#panel-msg', Game.elements.game_container );
	Game.elements.desk = $( 'table.table-desk');
	Game.elements.td_team = $( 'tr#trTeam td:eq(1)' );
	Game.elements.td_turn = $( 'tr#trTurn td:eq(1)' );
	
	
	Game.on = Game.elements.desk.attr( 'data-enabled' ) == '1';
	// init Pusher
	Game.Pusher = new Pusher( pusher_key );
	Game.Pusher.connection.bind( 'connected', function() {
		Game.socket = Game.Pusher.connection.socket_id;
	});
}

// this method determines, whether the game object should be used for this page
Game.Ready = function() {
	return Game.elements.game_container.size() == 1 || Game.elements.showroom.size() == 1;
}

//this method binds all events on elements
Game.BindEvents = function() {
	if( Game.on ) {
		Game.elements.desk.on( 'click', 'td.empty', function( e ) {
			var obj = $( this );
			var post_data = {
				'row' : obj.attr( 'data-row' ),
				'col' : obj.attr( 'data-col' ),
				'socket' : Game.socket,
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
	Game.MapPusher();
}

// what happens after player user clicks on a field
Game.FieldClick = function( data ) {

	switch( data.state ) {
		case 0: // something went wrong so just read the message
			{
				break;
			}
		case 1: // we're good to go so just mark the spot
			{
				Game.MarkField( data.row, data.col, data.game, data.team );
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

// update game statistics
Game.UpdateStats = function( turn, team, game ) {
	var obj = null;
	if( team == 2 ) {
		obj = $( '<span />', { 'class' : 'label label-danger', 'text' : 'Red' } );
	} else {
		obj = $( '<span />', { 'class' : 'label label-primary', 'text' : 'Blue' } );
	}
	
	if( Game.IsShowroom() ) {
		var act = $('div.panel[data-desk="'+game+'"] div.panel-body h3').html( 'Turn: '+turn );
		act.append( obj );
	} else {
		Game.elements.td_team.html( obj );
		Game.elements.td_turn.html( turn );
		Game.elements.td_turn.attr( 'data-turn', turn );		
	}
}

// mark winning fields
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

// mark the field
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


// find the right field on the board
Game.FieldTd = function( row, col, game ) {
	return $( 'table[data-game="'+game+'"] td[data-col="'+col+'"][data-row="'+row+'"]' );
}

// maps events from Pusher
Game.MapPusher = function() {
	Game.channels = {};
	Game.channels.main = [];
	
	Game.elements.desk.each( function( idx, e ) {
		var obj = $(e);
		Game.channels.main[idx] = Game.Pusher.subscribe( 'game-' + obj.attr( 'data-game' ) );
	});
	
	for( i = 0; i < Game.channels.main.length; i++ ) {
		Game.BindPusher( Game.channels.main[i] );
	}

	if( Game.IsShowroom() ) {
		Game.channels.aux = Game.Pusher.subscribe( 'general' );
		Game.channels.aux.bind( 'new_game', function( data ) {
			a_idx = 0;
			a_desk = null;
			// find which desk should be replaced
			Game.elements.desk.each( function( idx, e ) {
				var obj = $(e);
				if( obj.attr('data-game') == data.old ) {
					a_idx = idx;
					a_desk = obj;
				};
			});
			
			// unsubscribe and hide the old desk
			a_desk.fadeOut( 'slow', function() {

				$( 'tr.game td', a_desk).each( function(i, e) {
					$(this).html( '&nbsp;' );
				});
				Game.channels.main[a_idx] = Game.Pusher.subscribe( 'game-' + data.act );
				Game.BindPusher( Game.channels.main[a_idx] );
				a_desk.attr( 'data-game', data.act );
				a_desk.parent().attr( 'data-desk', data.act );
				Game.UpdateStats( data.turn, data.team, data.act );
				$( 'div.panel-heading h3', a_desk.parent() ).html( "Game #"+data.act );
				a_desk.fadeIn('fade');
				
				$('a.btn-primary', a_desk ).attr( 'href', base_url+"/game/join/"+data.act+"/blue" );
				$('a.btn-danger', a_desk ).attr( 'href', base_url+"/game/join/"+data.act+"/red" );
			});
		});
	}
}

Game.BindPusher = function( channel ) {
		// change game stats
		channel.bind( 'update_stats', function( data ) {
			Game.UpdateStats( data.turn, data.team, data.game );
		});
		
		// mark move
		channel.bind( 'move', function( data ) {
			Game.MarkField( data.row, data.col, data.game, data.team );		
		});
		
		// response to Game over 
		channel.bind( 'game_over', function( data ) {
			Game.MarkField( data.row, data.col, data.game, data.team );		
			Game.MarkWinner(data.start_row, data.start_col, data.dir, data.game, data.fields );
			if( !Game.IsShowroom() ) {
				Game.elements.panel_msg.prepend( data.html );			
			}
		
			// unsubscribe from any further msgs
			Game.Pusher.unsubscribe( 'game-'+data.game );
		});
		
		// update number of people
		channel.bind( 'update_players', function( data ) {
			Game.UpdatePlayers( data.red, data.blue, data.game );		
		});	
}

// update number of playes
Game.UpdatePlayers = function( red, blue, game) {
	if( !Game.IsShowroom() ) {
		$('div[data-desk="'+game+'"] tr[data-team="2"] td:eq(1)').html( red );
		$('div[data-desk="'+game+'"] tr[data-team="1"] td:eq(1)').html( blue );		
	}
}

Game.IsShowroom = function() {
	return Game.elements.showroom.size() == 1;
}

$(function(){
	Game.Init();
	if( Game.Ready() ) {
		Game.BindEvents();		
	}	
});
