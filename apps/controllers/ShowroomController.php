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
			$this->f3->set( 'game_id', $games[$i]->info->game_id );
			$this->f3->set( 'game_turn', $games[$i]->info->game_turn );
			$this->f3->set( 'game_table', $games[$i]->desk );
			$this->f3->set( 'game_team', $games[$i]->info->game_team );
			$desks[] = \Template::instance()->render( 'views/showroom/desk.htm' );
		}
		$this->f3->clear( 'game_id' );
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
		$model = new \Models\DeskModel( $this->f3, $this->db );
		$model->SetState( array( 'order_limit' =>  ShowroomController::MAX_GAMES ) );
		$desks = $model->GetList();
		
		while( !$desks->dry() ) {
			$game = new \stdClass();
			$game->info = clone $desks;
			$game->desk = $model->GetDesk( $desks->game_id );

			$result []= $game;
			$desks->skip(1);
		}
		
		return $result;
	}
}