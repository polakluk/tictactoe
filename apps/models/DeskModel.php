<?php

namespace Models;

class DeskModel extends BaseModel{
		
	public $table = 'games';
	protected $primary_key = 'game_id';
	
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
		if( $desk === false ) { // no game was found
			return false;
		}
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
		if( $game->game_id > 0 ) {
			$moves_model = new \Models\MoveModel( $this->f3, $this->db );
			$moves_model->SetState( array( 'game_id' => $game->game_id ) );
			$moves = $moves_model->GetList();
			while( !$moves->dry() ) {
				$result[$moves->row][$moves->col] = $moves->team;
				$moves->skip(1);
			}
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
	
	/*
	 * Get numbers of playes in red and blue teams (and spectators) - team 0 means all kinds of players
	 */
	public function GetNumberPlayers( $id, $team = 0 ) {
		$result = array();
		
		$results = new \DB\SQL\Mapper( $this->db, 'games_players_xref' );
		$conditions = array();
		if( $team == 0 ) {
			$result = array(
				\Tools::TEAM_BLUE_SQL => 0,
				\Tools::TEAM_RED_SQL => 0,
				\Tools::TEAM_SPEC_SQL => 0
					);

			$results->players = 'COUNT(player_id)';
			$results->load( array( 'game_id = ?', $id ), array( 'group' => 'player_team' ) );
			while( !$results->dry() ) {
				$result[ $results->player_team ] = $results->players;
				$results->skip(1);
			}
		} else {
			$results->players = 'COUNT(player_id)';
			$results->load( array( 'game_id = ? AND player_team = ?', $id, $team ), array( 'group' => 'player_team' ) );
			$result = $results->players;
		}
		
		return $result;
	}
}