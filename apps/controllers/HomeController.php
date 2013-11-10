<?php

namespace Controllers;

class HomeController extends BaseController {
	
	/*
	 * Returns path to currently rendered view file or false in case it requested redirection
	 */
	public function Execute() {
		$params = $this->f3->get('PARAMS');
		$task = isset( $params['task'] ) ? strtolower( $params['task'] ) : '';
		
		switch( $task ) {
			case 'create_user':
				{
					return $this->createUser();
					break;
				}
			case 'reset' :
				{
					return $this->resetUser();
					break;
				}
			default:
				{
					$model = new \Models\PlayerModel( $this->f3, $this->db );
					$player = $model->GetCurrentPlayer();
					$this->f3->set( 'username', $player->name );
					return 'views/home/main.htm';
				}
		}
	}
	
	/*
	 * This method takes care of all operations required to create a player
	 */
	 private function createUser() {
	 	return $this->modifyUser( $this->f3->get( 'POST.username' ) );
	 }

	/*
	 * This method takes care of all operations required to reset a player
	 */
	 private function resetUser() {
	 	return $this->modifyUser( '' );
	 }	 
	 
	 /*
	  * This method generalize way of working with player object
	  */
	 private function modifyUser( $username ){
		$model = new \Models\PlayerModel( $this->f3, $this->db );
		$player = $model->GetCurrentPlayer();
		$table = new \DB\SQL\Mapper( $this->db, 'players' );
		$table->load( $player->id );
		if( !$player->id ) { // just came to the site
			$table->player_joined = date( 'Y-m-d H:i:s', time() );
		}
		$table->player_name = $username;
		if( strlen( $username ) ) {
			$table->save();
		} else {
			$table->erase();
		}
		
		$player->id = $table->player_id;
		$player->name = $username;
		$model->StoreCurrentPlayer( $player );
		$this->f3->reroute( '/' );
		return false;	 	
	 }
}