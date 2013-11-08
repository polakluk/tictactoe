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
					return $this->joinGame($param_list);
					break;
				}
			case 'show';
				{
					return $this->showGame($param_list);
					break;
				}
			case 'leave': // player wants to leave a game
				{
					return $this->leaveGame($param_list);
					break;
				}
		}
		return false;
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
			
			
			$game->table[1][0] = '';
			
			$result []= $game;
		}
		
		return $result;
	}
}