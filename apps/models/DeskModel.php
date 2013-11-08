<?php

namespace Models;

class DeskModel extends BaseModel{
		
	public $table = 'games';
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
		$desk = parent::GetItem( $id );

		$game = new \stdClass();
		$game->info = clone $desk;
		$game->desk = $this->GetDesk( $desk );
		
		return $game;
	}
	
	/*
	 * Fills in desk array with moves from DB
	 */
	public function GetDesk( &$game ) {
		$result  = array();
	
		// set the whole desk to default value	
		for( $row = 0; $row < $game->game_size; $row++ ) {
			$result[$row] = array();
			for( $col = 0; $col < $game->game_size; $col++ ) {					
				$result[$row][$col] = '';
			}
		}
		// now load moves from the DB
		$moves_model = new \Models\MoveModel( $this->f3, $this->db );
		$moves_model->SetState( array( 'game_id' => $game->game_id ) );
		$moves = $moves_model->GetList();
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