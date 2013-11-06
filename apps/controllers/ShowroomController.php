<?php

namespace Controllers;

class ShowroomController extends BaseController {
	
	const MAX_GAMES = 2;
	
	/*
	 * Returns path to currently rendered view file or false in case it requested redirection
	 */
	public function Execute() {
		$this->f3->set( 'username', 'elf' );
		$params = $this->f3->get('PARAMS');
		$task = isset( $params['task'] ) ? $params['task'] : '';
		
		$spectator = !empty( $task ) || $this->f3->get( 'SESSION.username' ) == '';
		
		$this->f3->set( 'join_game', !$spectator );
		$this->f3->set('game_username', $spectator ? '<<spectator>>' : $this->f3->get( 'SESSION.username' ) );
		$desks = array();
		$games = $this->getGames();
		for( $i = 0; $i < 2; $i++ ) {
			$this->f3->set( 'game_id', $i );
			$this->f3->set( 'game_number', $games[$i]->num );
			$this->f3->set( 'game_turn', $games[$i]->turn );
			$this->f3->set( 'game_table', $games[$i]->table );
			$this->f3->set( 'game_team', $games[$i]->team );
			$desks[] = \Template::instance()->render( 'views/showroom/desk.htm' );
		}
		$this->f3->clear( 'game_id' );
		$this->f3->clear( 'game_number' );
		$this->f3->clear( 'game_turn' );
		$this->f3->clear( 'game_table' );
		$this->f3->clear( 'game_team' );
		$this->f3->set( 'desks', $desks );
		
		return 'views/showroom/main.htm';
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
			
			$game->table = array();
			
			for( $row = 0; $row < 3; $row++ ) {
				$game->table[$row] = array();
				for( $col = 0; $col < 3; $col++ ) {					
					$game->table[$row][$col] = ( $row + $col ) % 2;
				}
			}
			
			$game->table[1][0] = '';
			
			$result []= $game;
		}
		
		return $result;
	}
}