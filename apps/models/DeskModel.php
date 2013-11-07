<?php

namespace Models;

class DeskModel extends BaseModel{
		
	/*
	 * Gets a simple item from this model based on its ID
	 */
	public function GetItem( $id ) {
		$game=  new \stdClass();
		$game->game_id = $id;
		$game->turn = $id + 4;
		$game->team = $id == 1 ? 'red' : 'blue';
			
		$game->table = array();

		for( $row = 0; $row < 3; $row++ ) {
			$game->table[$row] = array();
			for( $col = 0; $col < 3; $col++ ) {
				$game->table[$row][$col] = $id == 1 ? ( $row + $col ) % 2 : '';
			}
		}
			
		$game->table[1][0] = '';

		return $game;
	}
	
	/*
	 * Gets a list of items based on the conditions
	 */
	public function GetList( $conditions = '' )
	{
		$results = array();
		
		for( $i = 0; $i < 2; $i++ ) {
			$obj = new \stdClass();
			$obj->game_id = $i + 1;
			$results []= $obj;
		}
		return $results;		
	}
}