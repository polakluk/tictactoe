<?php

namespace Models;

class MoveModel extends BaseModel{
		
	public $table = 'moves';
	/*
	 * Resets inner state of the model
	 */
	public function ResetModel() {
		$this->state = array(
			'order_by' => 'created DESC',
			'order_limit' => '',
			'order_offset' => '0',
			'game_id' => 0
		);
	}
	
	
	/*
	 * Gets a list of items based on the conditions
	 */
	public function GetList()
	{
		$results = new \DB\SQL\Mapper( $this->db, $this->table );
		$where = array();
		if( $this->state{'game_id'} > 0 ) {
			$where = array( 'game_id = ?', $this->state['game_id'] );
		}
		
		$results->load( $where, array( 
										'order' => $this->state['order_by'],
										'offset' => $this->state['order_offset'],
										'limit' => $this->state['order_limit'] )
		);

		return $results;
	}
	
	/*
	 * This method checks, if this move already exists on the desk. Returns true, if the move already exists
	 */
	public function CheckExistence( $game_id, $row, $col ){
		$table = new \DB\SQL\Mapper( $this->db, $this->table );
		$table->load( array( 'game_id = ? AND row = ? AND col = ?', $game_id, $row, $col ) );
	
		return $table->dry() ? 0 : $table->state;
	}
}