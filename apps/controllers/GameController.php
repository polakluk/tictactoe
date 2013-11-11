<?php

namespace Controllers;

class GameController extends BaseController {
	
	private $player = null;
	private $model_player = null;
	const MAX_GAMES = 2;
	
	/*
	 * Returns path to currently rendered view file or false in case it requested redirection
	 */
	public function Execute() {
		$this->model_player = new \Models\PlayerModel( $this->f3, $this->db );
		$this->player = $this->model_player->GetCurrentPlayer();

		$params = $this->f3->get('PARAMS');
		$param_list = isset( $params[3] ) ? explode( '/', $params[3] ) : array();
		$task = isset( $params['task'] ) ? $params['task'] : '';
		
		$spectator = $this->player->name == '';
		$this->f3->set( 'spectator', $spectator );
		$this->f3->set('game_username', $spectator ? '<<spectator>>' : $this->player->name );

		switch( $task ) {
			case 'join' : // player wants to join a game
				{
					return $this->joinGame( $param_list );
					break;
				}
			case 'show';
				{
					return $this->showGame( $param_list );
					break;
				}
			case 'leave': // player wants to leave a game
				{
					return $this->leaveGame( $param_list );
					break;
				}
			case 'makeMove' :
				{
					return $this->makeMove( $param_list );
					break;
				}
		}
		return false;
	}

	/*
	 * This method tries to save the move. If it's not possible, it notifies use about the issue
	 */
	private function makeMove( $param_list ) {
		$state = 0;
		$row = (int)$this->f3->get( "POST.row" );
		$col = (int)$this->f3->get( "POST.col" );
		$turn = (int)$this->f3->get( 'POST.turn' );

		$model = new \Models\MoveModel( $this->f3, $this->db );
		$exists = $model->CheckExistence( $this->player->game, $row, $col, $turn );
		$this->output_format = \Tools::OUTPUT_FORMAT_RAW;
		
		if( $exists == -1 ) { // somebody already marked a move
			$result = new \stdClass();
			$result->state = 0;
			$this->f3->set( 'msg_text', 'Sorry mate, but somebody from your team has alraedy marked a move!' );
			$this->f3->set( 'msg_type', 'danger' );
			$result->html = \Template::instance()->render( 'views/game/msg.htm' );
			$this->f3->clear( 'msg_text' );
			$this->f3->clear( 'msg_type' );
			return json_encode($result);			 
		}

		$model_desk = new \Models\DeskModel( $this->f3, $this->db );
		$game = $model_desk->GetItem( $this->player->game, true );
		if( $game->game_team != $this->player->team ) { // not your turn dude
			$result = new \stdClass();
			$result->state = 0;
			$this->f3->set( 'msg_text', 'It\'s not your turn dude! :)' );
			$this->f3->set( 'msg_type', 'danger' );
			$result->html = \Template::instance()->render( 'views/game/msg.htm' );
			$this->f3->clear( 'msg_text' );
			$this->f3->clear( 'msg_type' );
			return json_encode($result);
		}

		if( $game->game_ended == 1 ) { // the game already ended
			$result = new \stdClass();
			$result->state = 2;
			$this->f3->set( 'msg_text', 'The game is already done' );
			$this->f3->set( 'msg_type', 'success' );
			$result->html = \Template::instance()->render( 'views/game/msg.htm' );
			$this->f3->clear( 'msg_text' );
			$this->f3->clear( 'msg_type' );
			return json_encode($result);
		}

		
		$players = $model_desk->GetNumberPlayers( $this->player->game, $this->player->team );
		
		$msg = array();
		switch( $exists )
		{
			case \Tools::MOVE_STATE_NONE:
				{
					if( $players > 1 ) {
						$msg[0] = 'Your move has been announced to your teammates. Wait for their evaluation!';
						$msg[1] = 'warning';
						$state = 3;			
					} else {
						$msg[0] = 'Your move has been successfully recorded!';
						$msg[1] = 'success';
						
						// save the move
						$table = new \DB\SQL\Mapper( $this->db, 'moves' );
						$table->game_id = $this->player->game;
						$table->row = $row;
						$table->col = $col;
						$table->player_id = $this->player->id;
						$table->team = $this->player->team;
						$table->created = date( 'Y-m-d H:i:s', time() );
						$table->state = \Tools::MOVE_STATE_DONE;
						$table->turn = $game->game_turn;
						$table->save();
						
						// check, if the game is not over
						$res = $this->checkGame( $this->player->game, $row, $col );
						
						if( $res->done ) { // we won :)
							$result = new \stdClass();
							$result->state = 2;
							$result->fields = \Tools::CONNECT_FIELDS;
							$result->start_row = $res->row;
							$result->start_col = $res->col;
							$result->dir = $res->dir;
							$result->row = $row;
							$result->col = $col;
							$result->turn = $game->game_turn;
							$result->team = $this->player->team;
							$result->game = $this->player->game;

							$this->f3->set( 'msg_text', 'Game is over! The winner is team '.$res->winner.'. Feel free to play another game.' );
							$this->f3->set( 'msg_type', 'success' );
							$result->html = \Template::instance()->render( 'views/game/msg.htm' );
							$this->f3->clear( 'msg_text' );
							$this->f3->clear( 'msg_type' );
							return json_encode($result);
						}
						
						// update game stats
						$game->game_turn++;
						$game->game_team = 1 + (2 - $game->game_team);
						$game->save();
						$state = 1;
					}
					break;
				}
			case \Tools::MOVE_STATE_TMP:
				{
					$msg[0] = 'Somebdy from your team has alraedy selected this field!';
					$msg[1] = 'warning';
					$state = 0;
					break;
				}
			case \Tools::MOVE_STATE_DONE:
			default:
				{
					$msg[0] = 'This field is already occupied!';
					$msg[1] = 'danger';
					$state = 0;
					break;
				}
		}
		
		$result = new \stdClass();
		$result->state = $state;
		$result->turn = $game->game_turn;
		$result->team = $this->player->team;
		$result->row = $row;
		$result->col = $col;
		$result->game = $this->player->game;
		$this->f3->set( 'msg_text', $msg[0] );
		$this->f3->set( 'msg_type', $msg[1] );
		$result->html = \Template::instance()->render( 'views/game/msg.htm' );
		$this->f3->clear( 'msg_text' );
		$this->f3->clear( 'msg_type' );
		return json_encode($result);
	}

	/*
	 * This method takes care of tasks when a player is leaving a game
	 */
	private function leaveGame( $param_list ) {
		$id = (int)$param_list[0];

		if( $this->player->game == 0 ) {
			\Tools::EnqueueMessage( 'Currently, you are not playing any game which you could leave.', 'danger' );
			$this->f3->reroute( '/showroom' );
			return false;			
		}
		
		$this->player->team = '';
		$this->player->game = 0;
		$this->model_player->StoreCurrentPlayer( $this->player );

		// delete the xref record for the game and player
		$table_xref = new \DB\SQL\Mapper( $this->db, 'games_players_xref' );
		$table_xref->load( array( 'game_id = ? AND player_id = ?', $id, $this->player->id ) );
		$table_xref->erase();

		\Tools::EnqueueMessage( 'You left the game' );
		$this->f3->reroute( '/showroom' );
		return false;
	}

	/*
	 * This method takes care of tasks when a player is joining a game
	 */
	private function joinGame( $param_list ) {
		$id = (int)$param_list[0];
		$team = strtolower($param_list[1]);

		if( $this->player->name == '' ) {
			\Tools::EnqueueMessage( 'You cannot play the game unless you create your name. At this point, you can only spectate games.', 'danger' );
			$this->f3->reroute( '/showroom' );
			return false;
		}
		$this->player->team = $team == \Tools::TEAM_RED ? \Tools::TEAM_RED_SQL : \Tools::TEAM_BLUE_SQL;
		$this->player->game = $id;
		$this->model_player->StoreCurrentPlayer( $this->player );

		// store the xref record for the game and player
		$table_xref = new \DB\SQL\Mapper( $this->db, 'games_players_xref' );
		$table_xref->load( array( 'game_id = ? AND player_id = ?', $id, $this->player->id ) );
		$table_xref->game_id = $id;
		$table_xref->player_id = $this->player->id;
		$table_xref->player_team = $this->player->team == \Tools::TEAM_RED_SQL ? \Tools::TEAM_RED_SQL : \Tools::TEAM_BLUE_SQL;
		$table_xref->save();
		
		\Tools::EnqueueMessage( 'You joined the game.' );
		$this->f3->reroute( '/game/show/'.$id );
		return false;
	}

	/*
	 * This method takes care of rendering the desk and the game UI
	 */
	private function showGame( $param_list ) {
		$model = new \Models\DeskModel( $this->f3, $this->db );
		$id = (int)$param_list[0];
		$game = $model->GetItem( $id );
		if( !$game ) { // game not found
			\Tools::EnqueueMessage( 'Requested game has not been found!', 'danger' );
			$this->f3->reroute( '/' );
			return false;
		}

		if( $this->f3->get( 'spectator' ) ) {
			$this->player->game = $id;
			$this->model_player->StoreCurrentPlayer( $this->player );
		} else {			
			$table_xref = new \DB\SQL\Mapper( $this->db, 'games_players_xref' );
			$table_xref->load( array( 'game_id = ? AND player_id = ?', $id, $this->player->id ) );
			$this->player->team = $table_xref->player_team;
			$this->player->game = $table_xref->game_id;
			$this->model_player->StoreCurrentPlayer( $this->player );
		}

		$this->f3->set( 'game_id', $game->info->game_id );
		$this->f3->set( 'game_turn', $game->info->game_turn );
		$this->f3->set( 'game_table', $game->desk );
		$this->f3->set( 'game_team', $game->info->game_team );
		$this->f3->set( 'player_team', $this->player->team );
		$desk = \Template::instance()->render( 'views/game/desk.htm' );
		$this->f3->set( 'desk', $desk );
		$this->f3->clear( 'game_table' );
		
		
		$members = $model->GetNumberPlayers( $id );
		$this->f3->set( 'members', $members );
		return 'views/game/main.htm';
	}
	
	/*
	 * Gets all games which are currently played
	 */
	private function getGames() {
		$result = array();
		
		for( $i = 0; $i < ShowroomController::MAX_GAMES; $i++ ) {
			$game = new \stdClass();
			$game->num = $i + 1;
			$game->turn = $i + 4;
			$game->team = $i ? 'red' : 'blue';
			
			$result []= $game;
		}
		
		return $result;
	}

	/*
	 * Checks, if the game is not over at this point
	 */
	private function checkGame( $id, $row, $col ) {
		$result = new \stdClass();
		$result->row = 0;
		$result->col = 0;
		$result->dir = 0;
		$result->winner = '';
		$result->done = false;
		
		$model = new \Models\DeskModel( $this->f3, $this->db );
		$game = $model->GetItem( $id );
		$team = $this->player->team;
		
		$dir = array( // row, col, dir
				array( -1, 0), //vertical line
				array( 0, 1), // horizontal line
				array( -1, -1), // top-left => bottom-right
				array( 1, -1) // bottom-left => top-right
					);
					
		// check moves in all directions
		for( $i = 0, $c = count( $dir ); $i < $c; $i++ ) {
			$fields = 1;
			$act_row = $row + $dir[$i][0];
			$act_col = $col + $dir[$i][1];
			while( isset( $game->desk[$act_row][$act_col] ) && $game->desk[$act_row][$act_col] == $team ){
				$fields++;
				$act_row += $dir[$i][0];
				$act_col += $dir[$i][1];
			}
			$result->row = $act_row - $dir[$i][0];
			$result->col = $act_col - $dir[$i][1];
			
			$act_row = $row - $dir[$i][0];
			$act_col = $col - $dir[$i][1];
			while( isset( $game->desk[$act_row][$act_col] ) && $game->desk[$act_row][$act_col] == $team ){
				$fields++;
				$act_row -= $dir[$i][0];
				$act_col -= $dir[$i][1];
			}
			
			if( $fields >= \Tools::CONNECT_FIELDS ){
				$result->dir = $i;
				$result->winner = $team == \Tools::TEAM_RED_SQL ? \Tools::TEAM_RED : \Tools::TEAM_BLUE;
				$result->done = true;
				return $result;	
			}
		}
		
		return $result;
	}
}