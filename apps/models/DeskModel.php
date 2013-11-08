<?php

namespace Models;

class DeskModel extends BaseModel{
		
	/*
	 * Resets inner state of the model
	 */
	public function ResetModel() {
		$this->state = array(
			'order_by' => 'game_created DESC',
			'order_limit' => '5',
			'order_offset' => '0',
			'where' => 'game_ended = 0'
		);
	}

	/*
	 * Gets a simple item from this model based on its ID
	 */
	public function GetItem( $id ) {
		$desk = new \DB\SQL\Mapper( $this->db, 'games' );
		$desk->load( $id );

		$game = new \stdClass();
		$game->info = clone $desk;
		$game->desk = $this->GetDesk($id);
		
		return $game;
	}
	
	/*
	 * Fills in desk array with moves from DB
	 */
	public function GetDesk($id) {
		$result  = array();
	
		// set the whole desk to default value	
		for( $row = 0; $row < 3; $row++ ) {
			$result[$row] = array();
			for( $col = 0; $col < 3; $col++ ) {					
				$result[$row][$col] = '';
			}
		}
		// now load moves from the DB
		$moves = new \DB\SQL\Mapper( $this->db, 'moves' );
		$moves->load( array( 'game_id = ?', $id ) );
		while( !$moves->dry() ) {
			$result[$moves->row][$moves->col] = $moves->team;
			$moves->skip(1);
		}
		return $result;
	}
	
	/*
	 * Gets a list of items based on the conditions
	 */
	public function GetList()
	{
		$results = new \DB\SQL\Mapper( $this->db, 'games' );
		$results->load( $this->state['where'], array( 
												'order' => $this->state['order_by'],
												'offset' => $this->state['order_offset'],
												'limit' => $this->state['order_limit'] )
		);

		return $results;
	}
}