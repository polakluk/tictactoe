<?php

namespace Models;

class PlayerModel extends BaseModel{
		
	public $table = 'players';
	/*
	 * Resets inner state of the model
	 */
	public function ResetModel() {
		$this->state = array();
	}
	
	/*
	 * This method gets you the currently selected user (in case there's none, it returns instance of the players table)
	 */
	public function GetCurrentPlayer(  ){
	 	if( $this->f3->exists( 'SESSION.player' ) ) { // let's get the current player from the  session
	 		return $this->f3->get( 'SESSION.player' );
	 	} else { // there's none so we have to create one
	 		return $this->createEmptyPlayerObject();
	 	}
		return $user;
	}
	
	/*
	 * Creates an empty instance of player object
	 */
	private function createEmptyPlayerObject(){
		$obj = new \stdClass();
		$obj->id = 0;
		$obj->name = '';
		$obj->team = '';
		$obj->game = 0;
		
		return $obj;
	}
	
	/*
	 * Sets data for instance of player object
	 */
	public function StoreCurrentPlayer( $data ) {
		$this->f3->set( 'SESSION.player', $data );
	}
}