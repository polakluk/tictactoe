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
		$row = $this->f3->get( "POST.row" );
		$col = $this->f3->get( "POST.col" );
		
		$model = new \Models\MoveModel( $this->f3, $this->db );
		$exists = $model->CheckExistence( $this->player->game, $row, $col );
		$this->output_format = \Tools::OUTPUT_FORMAT_RAW;

		$model_desk = new \Models\DeskModel( $this->f3, $this->db );
		$players = $model_desk->GetNumberPlayers( $this->player->game, $this->player->team );
		
		$msg = array();
		switch( $exists )
		{
			case \Tools::MOVE_STATE_NONE:
				{
					if( $players > 1 ) {
						$msg[0] = 'Your move has been announced to your teammates. Wait for their evaluation!';
						$msg[1] = 'primary';						
					} else {
						$msg[0] = 'Your move has been successfully recorded!';
						$msg[1] = 'success';
						
						// save the move
						$table = new \DB\SQL\Mapper( $this->db, 'moves' );
						$table->game_id = $game_id;
						$table->row = $row;
						$table->col = $col;
						$table->player_id = 1;
						$table->team = 1;
						$table->created = date( 'Y-m-d H:i:s', time() );
						$table->state = \Tools::MOVE_STATE_DONE;
						$table->save();
					}
					break;
				}
			case \Tools::MOVE_STATE_TMP:
				{
					$msg[0] = 'Somebdy from your team has alraedy selected this field!';
					$msg[1] = 'warning';
					break;
				}
			case \Tools::MOVE_STATE_DONE:
			default:
				{
					$msg[0] = 'This field is already occupied!';
					$msg[1] = 'danger';
					break;
				}
		}
		
		$result = new \stdClass();
		$result->state = $exists;
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
}