<?php

namespace Controllers;

class GameController extends BaseController {
	
	const MAX_GAMES = 2;
	
	/*
	 * Returns path to currently rendered view file or false in case it requested redirection
	 */
	public function Execute() {
		$this->f3->set( 'username', 'elf' );
		$params = $this->f3->get('PARAMS');
		$param_list = isset( $params[3] ) ? explode( '/', $params[3] ) : array();
		$task = isset( $params['task'] ) ? $params['task'] : '';
		
		$spectator = $this->f3->get( 'SESSION.username' ) == '';
		$this->f3->set( 'spectator', $spectator );
		$this->f3->set('game_username', $spectator ? '<<spectator>>' : $this->f3->get( 'SESSION.username' ) );

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
		$game_id = $this->f3->get( "POST.game" );
		
		$model = new \Models\MoveModel( $this->f3, $this->db );
		$exists = $model->CheckExistence( $game_id, $row, $col );
		$this->output_format = \Tools::OUTPUT_FORMAT_RAW;
		$players = 1;
		
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
		$this->f3->push( 'SESSION.msgs', array( 'You left the game', 'info' ) );
		$this->f3->reroute( '/showroom' );
		return false;
	}

	/*
	 * This method takes care of tasks when a player is joining a game
	 */
	private function joinGame( $param_list ) {
		$id = (int)$param_list[0];
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
			$this->f3->push( 'SESSION.msgs', array( 'Requested game has not been found!', 'danger' ) );
			$this->f3->reroute( '/' );
			return false;
		}

		$this->f3->set( 'game_id', $game->info->game_id );
		$this->f3->set( 'game_turn', $game->info->game_turn );
		$this->f3->set( 'game_table', $game->desk );
		$this->f3->set( 'game_team', $game->info->game_team );
		$desk = \Template::instance()->render( 'views/game/desk.htm' );
		$this->f3->set( 'desk', $desk );
		$this->f3->clear( 'game_table' );
		
		$members = array( 3, 4, 5 );
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